<?php

/**
 * PhoneHelper
 * Comprehensive phone number validation and normalization.
 * Supports Philippine mobile formats and international E.164 formats.
 */
class PhoneHelper
{
    public const ERROR_MESSAGE = 'Please enter a valid phone number with the correct country code and number format.';

    /**
     * Valid ITU-T E.164 Country Calling Codes mapped to their national number length range [min, max].
     */
    private static array $countryCodes = [
        // North America (NANP)
        '1'   => [10, 10], // US, CA, etc.
        // Africa / Middle East / Asia / Europe / Americas / Oceania
        '7'   => [10, 10], // RU, KZ
        '20'  => [9, 10],  // Egypt
        '27'  => [9, 9],   // South Africa
        '30'  => [10, 10], // Greece
        '31'  => [9, 9],   // Netherlands
        '32'  => [8, 9],   // Belgium
        '33'  => [9, 9],   // France
        '34'  => [9, 9],   // Spain
        '36'  => [8, 9],   // Hungary
        '39'  => [9, 11],  // Italy
        '40'  => [9, 9],   // Romania
        '41'  => [9, 9],   // Switzerland
        '43'  => [4, 13],  // Austria
        '44'  => [9, 10],  // United Kingdom
        '45'  => [8, 8],   // Denmark
        '46'  => [7, 13],  // Sweden
        '47'  => [8, 8],   // Norway
        '48'  => [9, 9],   // Poland
        '49'  => [9, 12],  // Germany
        '51'  => [9, 9],   // Peru
        '52'  => [10, 10], // Mexico
        '54'  => [10, 11], // Argentina
        '55'  => [10, 11], // Brazil
        '56'  => [9, 9],   // Chile
        '57'  => [10, 10], // Colombia
        '58'  => [10, 10], // Venezuela
        '60'  => [9, 10],  // Malaysia
        '61'  => [9, 9],   // Australia
        '62'  => [8, 12],  // Indonesia
        '63'  => [10, 10], // Philippines (Mobile national number is 10 digits starting with 9)
        '64'  => [8, 10],  // New Zealand
        '65'  => [8, 8],   // Singapore
        '66'  => [8, 9],   // Thailand
        '81'  => [9, 10],  // Japan
        '82'  => [9, 10],  // South Korea
        '84'  => [9, 10],  // Vietnam
        '86'  => [10, 11], // China
        '90'  => [10, 10], // Turkey
        '91'  => [10, 10], // India
        '92'  => [10, 10], // Pakistan
        '93'  => [9, 9],   // Afghanistan
        '94'  => [9, 9],   // Sri Lanka
        '95'  => [7, 10],  // Myanmar
        '98'  => [10, 10], // Iran
        '212' => [9, 9],   // Morocco
        '213' => [9, 9],   // Algeria
        '216' => [8, 8],   // Tunisia
        '218' => [9, 9],   // Libya
        '234' => [10, 10], // Nigeria
        '254' => [9, 9],   // Kenya
        '351' => [9, 9],   // Portugal
        '353' => [9, 9],   // Ireland
        '358' => [5, 12],  // Finland
        '380' => [9, 9],   // Ukraine
        '880' => [10, 10], // Bangladesh
        '960' => [7, 7],   // Maldives
        '961' => [7, 8],   // Lebanon
        '962' => [8, 9],   // Jordan
        '963' => [8, 9],   // Syria
        '964' => [9, 10],  // Iraq
        '965' => [8, 8],   // Kuwait
        '966' => [9, 9],   // Saudi Arabia
        '967' => [8, 9],   // Yemen
        '968' => [8, 8],   // Oman
        '971' => [9, 9],   // United Arab Emirates
        '972' => [8, 9],   // Israel / Palestine
        '973' => [8, 8],   // Bahrain
        '974' => [8, 8],   // Qatar
        '977' => [8, 10],  // Nepal
    ];

    /**
     * Get the standardized error message.
     */
    public static function getErrorMessage(): string
    {
        return self::ERROR_MESSAGE;
    }

    /**
     * Determine whether a phone number is structurally and format-wise valid.
     */
    public static function isValid(?string $phone): bool
    {
        if ($phone === null) {
            return false;
        }

        $phone = trim($phone);
        if ($phone === '') {
            return false;
        }

        // 1. Character Check: Only digits, spaces, dashes, parentheses, dots, and at most one leading '+'
        if (!preg_match('/^\+?[0-9\s\-\(\)\.]+$/', $phone)) {
            return false;
        }

        // Balanced parentheses check
        $openCount = substr_count($phone, '(');
        $closeCount = substr_count($phone, ')');
        if ($openCount !== $closeCount || $openCount > 1) {
            return false;
        }
        if ($openCount === 1) {
            $openPos = strpos($phone, '(');
            $closePos = strpos($phone, ')');
            if ($closePos <= $openPos) {
                return false;
            }
        }

        // Clean out spaces, dashes, parentheses, dots
        $cleaned = preg_replace('/[\s\-\(\)\.]/', '', $phone);

        // Normalize leading 00 to +
        if (str_starts_with($cleaned, '00')) {
            $cleaned = '+' . substr($cleaned, 2);
        }

        // Total digits count (excluding +)
        $digitsOnly = str_replace('+', '', $cleaned);
        $digitCount = strlen($digitsOnly);

        // Standard ITU-T E.164: must be between 7 and 15 digits
        if ($digitCount < 7 || $digitCount > 15) {
            return false;
        }

        // Impossible patterns: All repeating identical digits (e.g., 11111111111, 00000000000)
        if (preg_match('/^(\d)\1+$/', $digitsOnly)) {
            return false;
        }

        // Impossible patterns: Trivial ascending or descending sequences
        if (in_array($digitsOnly, ['01234567890', '12345678901', '09876543210', '98765432109'], true)) {
            return false;
        }

        // 2. Philippine Formats Check
        // Case A: Local Philippine Mobile Format (09XXXXXXXXX)
        if (preg_match('/^09\d{9}$/', $cleaned)) {
            // Check that subscriber digits are not all identical (e.g. 09000000000, 09111111111)
            $subscriber = substr($cleaned, 2);
            if (preg_match('/^(\d)\1+$/', $subscriber)) {
                return false;
            }
            return true;
        }

        // Case B: International Philippine Mobile with '+' (+639XXXXXXXXX)
        if (preg_match('/^\+639\d{9}$/', $cleaned)) {
            $subscriber = substr($cleaned, 4);
            if (preg_match('/^(\d)\1+$/', $subscriber)) {
                return false;
            }
            return true;
        }

        // Case C: Philippine Mobile without '+' (639XXXXXXXXX)
        if (preg_match('/^639\d{9}$/', $cleaned)) {
            $subscriber = substr($cleaned, 3);
            if (preg_match('/^(\d)\1+$/', $subscriber)) {
                return false;
            }
            return true;
        }

        // If the number has 11 digits but starts with 0 and not 09 (e.g. 01234567890, 02123456789)
        // or has 11 digits without country code (e.g. 11111111111, 91712345671)
        if (!$cleaned || $cleaned[0] !== '+') {
            // Without a leading +, local format must be valid Philippine 09XXXXXXXXX
            // Reject any other local number format that does not specify an international country code
            return false;
        }

        // 3. International Format Check (must start with '+')
        if (str_starts_with($cleaned, '+')) {
            $numWithoutPlus = substr($cleaned, 1);
            
            // Try matching against our specific known country codes (longest prefix first)
            $matchedCode = null;
            $matchedRules = null;

            // Check 3-digit, 2-digit, then 1-digit prefixes
            for ($len = 3; $len >= 1; $len--) {
                $candidateCode = substr($numWithoutPlus, 0, $len);
                if (isset(self::$countryCodes[$candidateCode])) {
                    $matchedCode = $candidateCode;
                    $matchedRules = self::$countryCodes[$candidateCode];
                    break;
                }
            }

            if ($matchedCode !== null && $matchedRules !== null) {
                $nationalNumber = substr($numWithoutPlus, strlen($matchedCode));
                $nationalLen = strlen($nationalNumber);

                // National number must be within length range
                if ($nationalLen < $matchedRules[0] || $nationalLen > $matchedRules[1]) {
                    return false;
                }

                // National number must not start with 0 in E.164 (trunk prefix 0 must not follow country code)
                if (str_starts_with($nationalNumber, '0')) {
                    return false;
                }

                // National subscriber digits cannot be all repeating
                if (preg_match('/^(\d)\1+$/', $nationalNumber)) {
                    return false;
                }

                // Country-specific structural checks
                if ($matchedCode === '1') {
                    // NANP (US/Canada): 10 digits: Area code [2-9][0-8][0-9], Central Office [2-9]XX
                    if (!preg_match('/^[2-9][0-8][0-9][2-9]\d{6}$/', $nationalNumber)) {
                        return false;
                    }
                } elseif ($matchedCode === '65') {
                    // Singapore: 8 digits starting with 3, 6, 8, or 9
                    if (!preg_match('/^[3689]\d{7}$/', $nationalNumber)) {
                        return false;
                    }
                } elseif ($matchedCode === '966' || $matchedCode === '971') {
                    // Saudi Arabia / UAE: 9 digits, mobile starts with 5
                    if (!preg_match('/^[1-9]\d{8}$/', $nationalNumber)) {
                        return false;
                    }
                }

                return true;
            }

            // General E.164 fallback for other valid global countries:
            // Must have 1-3 digits country code followed by 5-14 digits national number (total 7-15 digits)
            if (preg_match('/^\+([1-9]\d{0,2})([1-9]\d{5,13})$/', $cleaned, $matches)) {
                $subscriber = $matches[2];
                if (!preg_match('/^(\d)\1+$/', $subscriber)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Normalize phone number before saving to database.
     * Preserves leading zero for local format (09XXXXXXXXX)
     * and leading '+' for international format (+639XXXXXXXXX, +12025550123).
     * Converts 639XXXXXXXXX to +639XXXXXXXXX.
     */
    public static function normalize(?string $phone): string
    {
        if ($phone === null) {
            return '';
        }

        $trimmed = trim($phone);
        if ($trimmed === '') {
            return '';
        }

        // Remove spaces, hyphens, parentheses, dots
        $cleaned = preg_replace('/[\s\-\(\)\.]/', '', $trimmed);

        // Normalize leading 00 to +
        if (str_starts_with($cleaned, '00')) {
            $cleaned = '+' . substr($cleaned, 2);
        }

        // Philippine local mobile format: 09XXXXXXXXX -> keep as 09XXXXXXXXX
        if (preg_match('/^09\d{9}$/', $cleaned)) {
            return $cleaned;
        }

        // Philippine mobile starting with 639XXXXXXXXX -> normalize to +639XXXXXXXXX
        if (preg_match('/^639\d{9}$/', $cleaned)) {
            return '+' . $cleaned;
        }

        // Already starting with +
        if (str_starts_with($cleaned, '+')) {
            return $cleaned;
        }

        return $cleaned;
    }

    /**
     * Get equivalent formats for checking duplicates across database records.
     * E.g. 09171234567 <-> +639171234567 <-> 639171234567
     *
     * @return string[] Array of unique equivalent string representations.
     */
    public static function getEquivalentFormats(?string $phone): array
    {
        $normalized = self::normalize($phone);
        if (empty($normalized)) {
            return [];
        }

        $formats = [$normalized];

        // If Philippine format
        if (preg_match('/^(?:\+63|63|0)(9\d{9})$/', $normalized, $m)) {
            $subscriber = $m[1];
            $formats = [
                '0' . $subscriber,
                '+63' . $subscriber,
                '63' . $subscriber,
            ];
        }

        return array_values(array_unique($formats));
    }
}
