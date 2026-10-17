<?php

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Lease.php';
require_once BASE_PATH . '/app/models/Payment.php';
require_once BASE_PATH . '/app/models/MoveOut.php';
require_once BASE_PATH . '/app/helpers/TimeSim.php';

class SoaHelper {
    /**
     * Build unified, accurate transactions for a tenant's Statement of Account.
     * Guaranteed 100% sync with tenant_payment.php billing logic.
     *
     * @param int $tenantId
     * @param int|null $specificLeaseId
     * @return array [lease, occupants, transactions, availableMonths, advanceCreditsTotal, advanceCreditsCount]
     */
    public static function buildTransactions(int $tenantId, ?int $specificLeaseId = null): array {
        $db = getDbConnection();
        $leaseModel = new Lease();
        $paymentModel = new Payment();

        $lease = $specificLeaseId ? $leaseModel->getLeaseById($specificLeaseId) : $leaseModel->getLeaseByTenantId($tenantId);
        if (!$lease) {
            return [
                'lease' => null,
                'occupants' => 1,
                'transactions' => [],
                'availableMonths' => [],
                'advanceCreditsTotal' => 0,
                'advanceCreditsCount' => 0
            ];
        }

        $leaseId = (int)$lease['lease_id'];
        $payments = $paymentModel->getPaymentsByLease($leaseId);

        // Occupants (Tenant + Registered Family members)
        $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM tenant_family_members WHERE tenant_id = ?");
        $stmt->execute([$tenantId]);
        $resCount = $stmt->fetch(PDO::FETCH_ASSOC);
        $occupants = (int)($resCount['cnt'] ?? 0) + 1;

        // Approved Parking Applications
        $stmt = $db->prepare("SELECT parking_id, vehiclename, plateno, datestarted, date FROM tenant_parking WHERE tenant_id = ? AND status = 'Approved'");
        $stmt->execute([$tenantId]);
        $parkingApps = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Manual Billing table entries
        $stmt = $db->prepare("SELECT * FROM billing WHERE tenant_id = ? ORDER BY due_date ASC");
        $stmt->execute([$tenantId]);
        $billingRecords = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Categorize payments from DB
        $directPayments = []; // [lower_key] => payment row
        $advanceQueues = [
            'deposit' => [],
            'rent-advance' => [],
            'water-advance' => [],
            'contribution-advance' => [],
            'parking-advance' => []
        ];
        $settlementPayments = [];
        $usedPaymentIds = [];

        $advanceCreditsTotal = 0;
        $advanceCreditsCount = 0;

        foreach ($payments as $p) {
            if ($p['payment_status'] !== 'Paid') continue;
            $typeLower = strtolower(trim($p['payment_type']));

            if (strpos($typeLower, 'settlement') !== false || strpos($typeLower, 'move-out') !== false) {
                $settlementPayments[$typeLower] = $p;
            } elseif (strpos($typeLower, '-') !== false && !strpos($typeLower, 'advance')) {
                $directPayments[$typeLower] = $p;
            } else {
                $norm = $typeLower;
                if ($norm === 'advance') $norm = 'rent-advance';
                if (!isset($advanceQueues[$norm])) {
                    $advanceQueues[$norm] = [];
                }
                $advanceQueues[$norm][] = $p;

                if (in_array($norm, ['rent-advance', 'water-advance', 'contribution-advance', 'parking-advance'])) {
                    $advanceCreditsTotal += (float)$p['amount'];
                    if ($norm === 'rent-advance') {
                        $advanceCreditsCount++;
                    }
                }
            }
        }

        $advRentCount = count($advanceQueues['rent-advance'] ?? []);
        $advWaterCount = count($advanceQueues['water-advance'] ?? []);
        $advContribCount = count($advanceQueues['contribution-advance'] ?? []);
        $advParkCount = count($advanceQueues['parking-advance'] ?? []);

        $leaseStart = new \DateTime($lease['start_date']);
        $leaseEnd = $lease['end_date'] ? new \DateTime($lease['end_date']) : null;
        $now = TimeSim::now('now', $tenantId);

        // Advance packages paid for Month 1 (Initial) are consumed in Month 0,
        // so each additional advance package covers 1 subsequent calendar month.
        $maxAdv = max($advRentCount, $advWaterCount, $advContribCount, $advParkCount);
        $effectiveAdv = max(0, $maxAdv - 1);
        $limitDate = ($leaseEnd && $leaseEnd < $now) ? clone $leaseEnd : clone $now;
        if ($effectiveAdv > 0) {
            $limitDate->modify("+{$effectiveAdv} month");
        }

        $transactions = [];
        $currentDate = clone $leaseStart;
        $monthCount = 0;

        while ($currentDate <= $limitDate) {
            $monthKey = $currentDate->format('Y-m');
            $monthName = $currentDate->format('F Y');
            $simDate = $currentDate->format('Y-m-d');

            // ══════════════════════════════════════════════════════════════
            // SECTION 1: Month 0 (Initial Move-In Settlement)
            // ══════════════════════════════════════════════════════════════
            if ($monthCount === 0) {
                $periodLabel = "Move-In & Initial Settlement — $monthName";

                // 1. Security Deposit
                if ($lease['deposit_amount'] > 0) {
                    $p = !empty($advanceQueues['deposit']) ? array_shift($advanceQueues['deposit']) : null;
                    $chargeRef = 'LSE-DEP-' . $lease['lease_id'];
                    $transactions[] = [
                        'tenant_id'   => $tenantId,
                        'date'        => $simDate,
                        'period'      => $periodLabel,
                        'type'        => 'Deposit',
                        'description' => 'Security Deposit (Refundable at end of lease)',
                        'ref'         => $chargeRef,
                        'charge'      => (float)$lease['deposit_amount'],
                        'payment'     => 0
                    ];
                    if ($p) {
                        $usedPaymentIds[] = $p['payment_id'];
                        $transactions[] = [
                            'tenant_id'   => $tenantId,
                            'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $simDate,
                            'period'      => $periodLabel,
                            'type'        => 'Payment',
                            'description' => 'Payment Received — Security Deposit',
                            'ref'         => $p['reference_number'] ?: 'PAY-' . $p['payment_id'],
                            'charge'      => 0,
                            'payment'     => (float)$p['amount']
                        ];
                    }
                }

                // 2. Advance Rent (Month 1 Rent)
                if ($lease['advance_amount'] > 0) {
                    $p = !empty($advanceQueues['rent-advance']) ? array_shift($advanceQueues['rent-advance']) : null;
                    $chargeRef = 'LSE-ADV-' . $lease['lease_id'];
                    $transactions[] = [
                        'tenant_id'   => $tenantId,
                        'date'        => $simDate,
                        'period'      => $periodLabel,
                        'type'        => 'Advance Rent',
                        'description' => 'Advance Rent (Month 1 Initial)',
                        'ref'         => $chargeRef,
                        'charge'      => (float)$lease['advance_amount'],
                        'payment'     => 0
                    ];
                    if ($p) {
                        $usedPaymentIds[] = $p['payment_id'];
                        $transactions[] = [
                            'tenant_id'   => $tenantId,
                            'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $simDate,
                            'period'      => $periodLabel,
                            'type'        => 'Payment',
                            'description' => 'Payment Received — Advance Rent (Month 1)',
                            'ref'         => $p['reference_number'] ?: 'PAY-' . $p['payment_id'],
                            'charge'      => 0,
                            'payment'     => (float)$p['amount']
                        ];
                    }
                }

                // 3. Initial Water (Advance)
                $waterAmt = (float)($occupants * 100);
                $p = !empty($advanceQueues['water-advance']) ? array_shift($advanceQueues['water-advance']) : null;
                $chargeRef = 'LSE-W-' . $lease['lease_id'];
                $transactions[] = [
                    'tenant_id'   => $tenantId,
                    'date'        => $simDate,
                    'period'      => $periodLabel,
                    'type'        => 'Water',
                    'description' => "Water Consumption (1st Month Advance — $occupants occupants)",
                    'ref'         => $chargeRef,
                    'charge'      => $waterAmt,
                    'payment'     => 0
                ];
                if ($p) {
                    $usedPaymentIds[] = $p['payment_id'];
                    $transactions[] = [
                        'tenant_id'   => $tenantId,
                        'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $simDate,
                        'period'      => $periodLabel,
                        'type'        => 'Payment',
                        'description' => 'Payment Received — Water Bill (1st Month Advance)',
                        'ref'         => $p['reference_number'] ?: 'PAY-' . $p['payment_id'],
                        'charge'      => 0,
                        'payment'     => (float)$p['amount']
                    ];
                }

                // 4. Initial Contribution (Advance)
                $p = !empty($advanceQueues['contribution-advance']) ? array_shift($advanceQueues['contribution-advance']) : null;
                $chargeRef = 'LSE-C-' . $lease['lease_id'];
                $transactions[] = [
                    'tenant_id'   => $tenantId,
                    'date'        => $simDate,
                    'period'      => $periodLabel,
                    'type'        => 'Contribution',
                    'description' => "Contribution (1st Month Advance — Security/Garbage)",
                    'ref'         => $chargeRef,
                    'charge'      => 150.00,
                    'payment'     => 0
                ];
                if ($p) {
                    $usedPaymentIds[] = $p['payment_id'];
                    $transactions[] = [
                        'tenant_id'   => $tenantId,
                        'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $simDate,
                        'period'      => $periodLabel,
                        'type'        => 'Payment',
                        'description' => 'Payment Received — Contribution (1st Month Advance)',
                        'ref'         => $p['reference_number'] ?: 'PAY-' . $p['payment_id'],
                        'charge'      => 0,
                        'payment'     => (float)$p['amount']
                    ];
                }

                // 5. Initial Parking (Advance) if approved
                foreach ($parkingApps as $pa) {
                    $p = !empty($advanceQueues['parking-advance']) ? array_shift($advanceQueues['parking-advance']) : null;
                    $chargeRef = 'LSE-PKG-' . $pa['parking_id'];
                    $transactions[] = [
                        'tenant_id'   => $tenantId,
                        'date'        => $simDate,
                        'period'      => $periodLabel,
                        'type'        => 'Parking Fee',
                        'description' => 'Parking Fee (1st Month Advance — ' . ($pa['vehiclename'] ?: 'Vehicle') . ')',
                        'ref'         => $chargeRef,
                        'charge'      => 1000.00,
                        'payment'     => 0
                    ];
                    if ($p) {
                        $usedPaymentIds[] = $p['payment_id'];
                        $transactions[] = [
                            'tenant_id'   => $tenantId,
                            'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $simDate,
                            'period'      => $periodLabel,
                            'type'        => 'Payment',
                            'description' => 'Payment Received — Parking (1st Month Advance)',
                            'ref'         => $p['reference_number'] ?: 'PAY-' . $p['payment_id'],
                            'charge'      => 0,
                            'payment'     => (float)$p['amount']
                        ];
                    }
                }
            }

            // ══════════════════════════════════════════════════════════════
            // SECTION 2: Month 1+ (Recurring Monthly Cycles)
            // ══════════════════════════════════════════════════════════════
            if ($monthCount > 0) {
                $periodLabel = "Billing Statement — $monthName";

                // A) Monthly Rent
                $rentKey = "rent-$monthKey";
                $rentAmt = (float)$lease['monthly_rent'];
                $chargeRef = 'LSE-R' . $lease['lease_id'] . '-' . $currentDate->format('my');
                $transactions[] = [
                    'tenant_id'   => $tenantId,
                    'date'        => $simDate,
                    'period'      => $periodLabel,
                    'type'        => 'Monthly Rent',
                    'description' => "Monthly Rent — $monthName",
                    'ref'         => $chargeRef,
                    'charge'      => $rentAmt,
                    'payment'     => 0
                ];

                if (isset($directPayments[$rentKey])) {
                    $p = $directPayments[$rentKey];
                    $usedPaymentIds[] = $p['payment_id'];
                    $transactions[] = [
                        'tenant_id'   => $tenantId,
                        'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $simDate,
                        'period'      => $periodLabel,
                        'type'        => 'Payment',
                        'description' => "Payment Received — Rent ($monthName)",
                        'ref'         => $p['reference_number'] ?: 'PAY-' . $p['payment_id'],
                        'charge'      => 0,
                        'payment'     => (float)$p['amount']
                    ];
                } elseif (!empty($advanceQueues['rent-advance'])) {
                    $p = array_shift($advanceQueues['rent-advance']);
                    $usedPaymentIds[] = $p['payment_id'];
                    $transactions[] = [
                        'tenant_id'   => $tenantId,
                        'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $simDate,
                        'period'      => $periodLabel,
                        'type'        => 'Payment',
                        'description' => "Payment Applied from Advance — Rent for $monthName",
                        'ref'         => $p['reference_number'] ?: 'ADV-' . $p['payment_id'],
                        'charge'      => 0,
                        'payment'     => (float)$p['amount']
                    ];
                }

                // B) Water Bill
                $waterKey = "water-$monthKey";
                $waterAmt = (float)($occupants * 100);
                $chargeRef = 'LSE-W' . $lease['lease_id'] . '-' . $currentDate->format('my');
                $transactions[] = [
                    'tenant_id'   => $tenantId,
                    'date'        => $simDate,
                    'period'      => $periodLabel,
                    'type'        => 'Water',
                    'description' => "Water Consumption ($occupants occupants) — $monthName",
                    'ref'         => $chargeRef,
                    'charge'      => $waterAmt,
                    'payment'     => 0
                ];

                if (isset($directPayments[$waterKey])) {
                    $p = $directPayments[$waterKey];
                    $usedPaymentIds[] = $p['payment_id'];
                    $transactions[] = [
                        'tenant_id'   => $tenantId,
                        'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $simDate,
                        'period'      => $periodLabel,
                        'type'        => 'Payment',
                        'description' => "Payment Received — Water ($monthName)",
                        'ref'         => $p['reference_number'] ?: 'PAY-' . $p['payment_id'],
                        'charge'      => 0,
                        'payment'     => (float)$p['amount']
                    ];
                } elseif (!empty($advanceQueues['water-advance'])) {
                    $p = array_shift($advanceQueues['water-advance']);
                    $usedPaymentIds[] = $p['payment_id'];
                    $transactions[] = [
                        'tenant_id'   => $tenantId,
                        'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $simDate,
                        'period'      => $periodLabel,
                        'type'        => 'Payment',
                        'description' => "Payment Applied from Advance — Water for $monthName",
                        'ref'         => $p['reference_number'] ?: 'ADV-' . $p['payment_id'],
                        'charge'      => 0,
                        'payment'     => (float)$p['amount']
                    ];
                }

                // C) Contribution Fee
                $contribKey = "contribution-$monthKey";
                $chargeRef = 'LSE-C' . $lease['lease_id'] . '-' . $currentDate->format('my');
                $transactions[] = [
                    'tenant_id'   => $tenantId,
                    'date'        => $simDate,
                    'period'      => $periodLabel,
                    'type'        => 'Contribution',
                    'description' => "Contribution (Security/Garbage) — $monthName",
                    'ref'         => $chargeRef,
                    'charge'      => 150.00,
                    'payment'     => 0
                ];

                if (isset($directPayments[$contribKey])) {
                    $p = $directPayments[$contribKey];
                    $usedPaymentIds[] = $p['payment_id'];
                    $transactions[] = [
                        'tenant_id'   => $tenantId,
                        'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $simDate,
                        'period'      => $periodLabel,
                        'type'        => 'Payment',
                        'description' => "Payment Received — Contribution ($monthName)",
                        'ref'         => $p['reference_number'] ?: 'PAY-' . $p['payment_id'],
                        'charge'      => 0,
                        'payment'     => (float)$p['amount']
                    ];
                } elseif (!empty($advanceQueues['contribution-advance'])) {
                    $p = array_shift($advanceQueues['contribution-advance']);
                    $usedPaymentIds[] = $p['payment_id'];
                    $transactions[] = [
                        'tenant_id'   => $tenantId,
                        'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $simDate,
                        'period'      => $periodLabel,
                        'type'        => 'Payment',
                        'description' => "Payment Applied from Advance — Contribution for $monthName",
                        'ref'         => $p['reference_number'] ?: 'ADV-' . $p['payment_id'],
                        'charge'      => 0,
                        'payment'     => (float)$p['amount']
                    ];
                }

                // D) Recurring Parking Fees
                foreach ($parkingApps as $pa) {
                    $parkStart = new \DateTime($pa['datestarted'] ?: $pa['date']);
                    $parkStart->modify('+1 month');
                    if ($currentDate >= $parkStart) {
                        $pKey = 'parking-' . $pa['parking_id'] . '-' . $monthKey;
                        $chargeRef = 'PKG-' . $pa['parking_id'] . '-' . $currentDate->format('my');
                        $transactions[] = [
                            'tenant_id'   => $tenantId,
                            'date'        => $simDate,
                            'period'      => $periodLabel,
                            'type'        => 'Parking Fee',
                            'description' => 'Parking Fee — ' . ($pa['vehiclename'] ?: 'Vehicle') . " — $monthName",
                            'ref'         => $chargeRef,
                            'charge'      => 1000.00,
                            'payment'     => 0
                        ];

                        if (isset($directPayments[$pKey])) {
                            $p = $directPayments[$pKey];
                            $usedPaymentIds[] = $p['payment_id'];
                            $transactions[] = [
                                'tenant_id'   => $tenantId,
                                'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $simDate,
                                'period'      => $periodLabel,
                                'type'        => 'Payment',
                                'description' => "Payment Received — Parking ($monthName)",
                                'ref'         => $p['reference_number'] ?: 'PAY-' . $p['payment_id'],
                                'charge'      => 0,
                                'payment'     => (float)$p['amount']
                            ];
                        } elseif (!empty($advanceQueues['parking-advance'])) {
                            $p = array_shift($advanceQueues['parking-advance']);
                            $usedPaymentIds[] = $p['payment_id'];
                            $transactions[] = [
                                'tenant_id'   => $tenantId,
                                'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $simDate,
                                'period'      => $periodLabel,
                                'type'        => 'Payment',
                                'description' => "Payment Applied from Advance — Parking for $monthName",
                                'ref'         => $p['reference_number'] ?: 'ADV-' . $p['payment_id'],
                                'charge'      => 0,
                                'payment'     => (float)$p['amount']
                            ];
                        }
                    }
                }
            }

            $currentDate->modify('+1 month');
            $monthCount++;
            if ($currentDate > $limitDate && $currentDate->format('mY') === $limitDate->format('mY')) break;
        }

        // ══════════════════════════════════════════════════════════════
        // SECTION 3: Move-Out Settlement (if any)
        // ══════════════════════════════════════════════════════════════
        $moveout = (new MoveOut())->getSettlementForTenant($tenantId, ['Processing', 'Completed']);
        if ($moveout) {
            $moPeriod = "Move-Out Settlement & Clearance";
            $moDate = date('Y-m-d', strtotime($moveout['updated_at'] ?? 'now'));

            $damageCosts = (float)($moveout['damage_costs'] ?? 0);
            $excessDamage = max(0, $damageCosts - 1000);
            if ($excessDamage > 0) {
                $transactions[] = [
                    'tenant_id'   => $tenantId,
                    'date'        => $moDate,
                    'period'      => $moPeriod,
                    'type'        => 'Move-Out Damage',
                    'description' => 'Move-Out Settlement: Repair Costs (Excess of Security Deposit)',
                    'ref'         => 'MO-DMG-' . $moveout['moveout_id'],
                    'charge'      => $excessDamage,
                    'payment'     => 0
                ];
                if (isset($settlementPayments['move-out damage']) || isset($settlementPayments['settlement-damage'])) {
                    $p = $settlementPayments['move-out damage'] ?? $settlementPayments['settlement-damage'];
                    $usedPaymentIds[] = $p['payment_id'];
                    $transactions[] = [
                        'tenant_id'   => $tenantId,
                        'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $moDate,
                        'period'      => $moPeriod,
                        'type'        => 'Payment',
                        'description' => 'Payment Received — Move-Out Damage',
                        'ref'         => $p['reference_number'] ?: 'PAY-' . $p['payment_id'],
                        'charge'      => 0,
                        'payment'     => (float)$p['amount']
                    ];
                }
            }

            $utilityDeductions = (float)($moveout['utility_deductions'] ?? 0);
            if ($utilityDeductions > 0) {
                $transactions[] = [
                    'tenant_id'   => $tenantId,
                    'date'        => $moDate,
                    'period'      => $moPeriod,
                    'type'        => 'Move-Out Utility',
                    'description' => 'Move-Out Settlement: Final Meter & Utility Deductions',
                    'ref'         => 'MO-UTL-' . $moveout['moveout_id'],
                    'charge'      => $utilityDeductions,
                    'payment'     => 0
                ];
                if (isset($settlementPayments['move-out utility']) || isset($settlementPayments['settlement-utility'])) {
                    $p = $settlementPayments['move-out utility'] ?? $settlementPayments['settlement-utility'];
                    $usedPaymentIds[] = $p['payment_id'];
                    $transactions[] = [
                        'tenant_id'   => $tenantId,
                        'date'        => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $moDate,
                        'period'      => $moPeriod,
                        'type'        => 'Payment',
                        'description' => 'Payment Received — Move-Out Utility',
                        'ref'         => $p['reference_number'] ?: 'PAY-' . $p['payment_id'],
                        'charge'      => 0,
                        'payment'     => (float)$p['amount']
                    ];
                }
            }
        }

        // ══════════════════════════════════════════════════════════════
        // SECTION 4: Catch-All for Any Remaining Paid Records
        // ══════════════════════════════════════════════════════════════
        foreach ($payments as $p) {
            if ($p['payment_status'] !== 'Paid' || in_array($p['payment_id'], $usedPaymentIds)) continue;
            $payDate = $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : date('Y-m-d', strtotime($p['created_at']));
            $transactions[] = [
                'tenant_id'   => $tenantId,
                'date'        => $payDate,
                'period'      => 'Additional Payment Records',
                'type'        => 'Payment',
                'description' => 'Payment Received — ' . ucwords(str_replace('-', ' ', $p['payment_type'])),
                'ref'         => $p['reference_number'] ?: 'PAY-' . $p['payment_id'],
                'charge'      => 0,
                'payment'     => (float)$p['amount']
            ];
        }

        // ══════════════════════════════════════════════════════════════
        // SECTION 5: Manual Invoices from Billing Table (if non-duplicate)
        // ══════════════════════════════════════════════════════════════
        foreach ($billingRecords as $b) {
            $bDue = $b['due_date'];
            $bMonthKey = substr($bDue, 0, 7);
            // Check if this month rent is already represented
            $alreadyExists = false;
            foreach ($transactions as $t) {
                if (strpos($t['ref'] ?? '', 'LSE-R') !== false && substr($t['date'], 0, 7) === $bMonthKey) {
                    $alreadyExists = true;
                    break;
                }
            }
            if (!$alreadyExists) {
                $transactions[] = [
                    'tenant_id'   => $tenantId,
                    'date'        => $bDue,
                    'period'      => 'Manual Billing Records',
                    'type'        => 'Monthly Rent',
                    'description' => 'Monthly Rent Invoice',
                    'ref'         => 'INV-' . str_pad($b['billing_id'], 4, '0', STR_PAD_LEFT),
                    'charge'      => (float)$b['amount'],
                    'payment'     => 0
                ];
                if ($b['status'] === 'Paid') {
                    $transactions[] = [
                        'tenant_id'   => $tenantId,
                        'date'        => $bDue,
                        'period'      => 'Manual Billing Records',
                        'type'        => 'Payment',
                        'description' => 'Payment Received — Rent Invoice',
                        'ref'         => 'PAY-INV-' . $b['billing_id'],
                        'charge'      => 0,
                        'payment'     => (float)$b['amount']
                    ];
                }
            }
        }

        // Available Months list
        $availableMonths = [];
        foreach ($transactions as $t) {
            $tMonth = substr($t['date'], 0, 7);
            if (!in_array($tMonth, $availableMonths) && !empty($tMonth) && $tMonth !== '1970-01') {
                $availableMonths[] = $tMonth;
            }
        }
        sort($availableMonths);

        return [
            'lease'               => $lease,
            'occupants'           => $occupants,
            'transactions'        => $transactions,
            'availableMonths'     => $availableMonths,
            'advanceCreditsTotal' => $advanceCreditsTotal,
            'advanceCreditsCount' => $advanceCreditsCount,
            'limitDate'           => $limitDate->format('Y-m-d')
        ];
    }
}
