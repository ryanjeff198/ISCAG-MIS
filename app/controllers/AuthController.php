<?php

require_once BASE_PATH . '/app/controllers/Controller.php';
require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/helpers/Mailer.php';
require_once BASE_PATH . '/app/helpers/AuditLogger.php';
require_once BASE_PATH . '/app/helpers/Security.php';

class AuthController extends Controller
{
    protected User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCsrf($_POST['csrf_token'] ?? '')) {
                die("CSRF token validation failed.");
            }
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->authenticate($email, $password);

            if ($user) {
                if (!$user['is_verified']) {
                    $error = "Please verify your account first.";
                    $this->view('auth/login', ['error' => $error]);
                    return;
                }

                $_SESSION['user_id'] = $user['tenant_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['sex'] = $user['sex'] ?? $user['gender'] ?? 'Male';
                
                AuditLogger::log('AUTH', 'LOGIN', "User logged into the system");

                // Redirect based on role
                if ($user['role'] === 'Admin') {
                    header('Location: ' . url('/admin/dashboard'));
                } elseif ($user['role'] === 'Staff_Tenant') {
                    header('Location: ' . url('/admin/apartment'));
                } elseif ($user['role'] === 'Staff_Male') {
                    header('Location: ' . url('/admin/dawah/male'));
                } elseif ($user['role'] === 'Staff_Female') {
                    header('Location: ' . url('/admin/dawah/female'));
                } elseif ($user['role'] === 'Staff_Damayan') {
                    header('Location: ' . url('/admin/damayan'));
                } else {
                    header('Location: ' . url('/user/dashboard'));
                }
                exit;
            } else {
                $error = "Invalid email or password.";
                $this->view('auth/login', ['error' => $error]);
                return;
            }
        }
        $this->view('auth/login');
    }

    public function register(): void
    {
        unset($_SESSION['reset_mode']); // Clear any stale reset state
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Capture data for sticky form
            $postedData = $_POST;
            $_SESSION['temp_reg_data'] = $postedData; // Store for "Back to Email" flow

            if (!Security::validateCsrf($_POST['csrf_token'] ?? '')) {
                die("CSRF token validation failed.");
            }

            // Capture data for sticky form
            $postedData = $_POST;

            $password = $_POST['password'] ?? '';
            $confirmpass = $_POST['confirmpass'] ?? '';

            if ($password !== $confirmpass) {
                $error = "Passwords do not match.";
                $this->view('auth/register', ['error' => $error, 'data' => $postedData]);
                return;
            }

            $email = trim($_POST['email'] ?? '');
            
            // Check if email already exists
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser) {
                if ($existingUser['is_verified']) {
                    $error = "Email already registered and verified.";
                    $this->view('auth/register', ['error' => $error, 'data' => $postedData]);
                    return;
                }
            }

            $contactnum = trim($_POST['contactnum'] ?? '');
            if (!empty($contactnum) && $this->userModel->exists('contactnum', $contactnum)) {
                // If we are updating an existing unverified user, we should allow it if the phone matches the same user
                if (!$existingUser || $existingUser['contactnum'] !== $contactnum) {
                    $error = "Phone number already used.";
                    $this->view('auth/register', ['error' => $error, 'data' => $postedData]);
                    return;
                }
            }

            $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $otpExpiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            $data = [
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'sex' => $_POST['sex'] ?? 'Male',
                'email' => $email,
                'contactnum' => $contactnum,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'confirmpass' => password_hash($confirmpass, PASSWORD_DEFAULT),
                'role' => 'Guest',
                'otp_code' => $otpCode,
                'otp_expiry' => $otpExpiry,
                'is_verified' => 0
            ];

            $success = false;
            if ($existingUser) {
                // Update unverified user
                $success = $this->updateUnverifiedUser($existingUser['email'], $data);
            } else {
                // Create new user
                $success = $this->userModel->create($data);
            }

            if ($success) {
                // Send OTP
                if (Mailer::sendOTP($data['email'], $data['otp_code'])) {
                    $_SESSION['temp_email'] = $data['email'];
                    $_SESSION['otp_expiry'] = strtotime($data['otp_expiry']) * 1000; // JS timestamp
                    header('Location: ' . url('/verify-otp'));
                    exit;
                } else {
                    $error = "Account created but failed to send verification email. Please check your email or try again.";
                    $this->view('auth/register', ['error' => $error, 'data' => $postedData]);
                    return;
                }
            } else {
                $error = "Failed to create account. Please try again.";
                $this->view('auth/register', ['error' => $error, 'data' => $postedData]);
                return;
            }
        }
        $data = $_SESSION['temp_reg_data'] ?? [];
        $this->view('auth/register', ['data' => $data]);
    }

    /**
     * Helper to update an existing but unverified user.
     */
    private function updateUnverifiedUser(string $email, array $data): bool
    {
        $sql = "UPDATE tenant_accounts SET 
                first_name = :first_name, 
                last_name = :last_name, 
                sex = :sex, 
                contactnum = :contactnum, 
                password = :password, 
                confirmpass = :confirmpass, 
                role = :role,
                otp_code = :otp_code, 
                otp_expiry = :otp_expiry 
                WHERE email = :email AND is_verified = 0";
        
        $db = getDbConnection();
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'sex' => $data['sex'],
            'contactnum' => $data['contactnum'],
            'password' => $data['password'],
            'confirmpass' => $data['confirmpass'],
            'role' => $data['role'] ?? 'Guest',
            'otp_code' => $data['otp_code'],
            'otp_expiry' => $data['otp_expiry'],
            'email' => $email
        ]);
    }

    public function verifyOtp(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCsrf($_POST['csrf_token'] ?? '')) {
                die("CSRF token validation failed.");
            }
            $email = trim($_SESSION['temp_email'] ?? '');
            
            $otp = '';
            if (!empty($_POST['otp_full'])) {
                $otp = trim($_POST['otp_full']);
            } elseif (isset($_POST['otp']) && is_array($_POST['otp'])) {
                $otp = implode('', array_map('trim', $_POST['otp']));
            } elseif (isset($_POST['otp'])) {
                $otp = trim($_POST['otp']);
            }
            $otp = preg_replace('/\D/', '', $otp);

            if (!$email) {
                $isReset = isset($_SESSION['reset_mode']) && $_SESSION['reset_mode'];
                $error = "Session expired. Please " . ($isReset ? "request a new reset link." : "register again.");
                $this->view('auth/verify-otp', ['error' => $error]);
                return;
            }

            if ($this->userModel->verifyAccount($email, $otp)) {
                // Check if this is a password reset flow
                if (isset($_SESSION['reset_mode']) && $_SESSION['reset_mode']) {
                    $_SESSION['otp_verified'] = true;
                    $_SESSION['reset_email'] = $email;
                    header('Location: ' . url('/reset-password'));
                    exit;
                }

                // Fetch user to auto-login (Standard Registration Flow)
                $user = $this->userModel->findByEmail($email);
                
                unset($_SESSION['temp_email'], $_SESSION['otp_expiry'], $_SESSION['temp_reg_data']);
                
                $_SESSION['user_id'] = $user['tenant_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['sex'] = $user['sex'];

                // Send a Welcome notification to the new tenant
                try {
                    require_once BASE_PATH . '/app/models/Notification.php';
                    $notif = new Notification();
                    $notif->create(
                        $user['tenant_id'],
                        'Welcome to ISCAG MIS',
                        'Your account has been verified! We are excited to have you. You can now apply for an apartment or explore our services.',
                        'info'
                    );
                } catch (\Exception $e) {}

                // Redirect based on role
                if ($user['role'] === 'Admin') {
                    header('Location: ' . url('/admin/dashboard'));
                } elseif ($user['role'] === 'Staff_Tenant') {
                    header('Location: ' . url('/admin/apartment'));
                } elseif ($user['role'] === 'Staff_Male') {
                    header('Location: ' . url('/admin/dawah/male'));
                } elseif ($user['role'] === 'Staff_Female') {
                    header('Location: ' . url('/admin/dawah/female'));
                } elseif ($user['role'] === 'Staff_Damayan') {
                    header('Location: ' . url('/admin/damayan'));
                } else {
                    header('Location: ' . url('/user/dashboard'));
                }
                exit;
            } else {
                $error = "Invalid or expired OTP code. Please try again.";
                $this->view('auth/verify-otp', ['error' => $error]);
                return;
            }
        }
        $data = [];
        if (isset($_GET['resend']) && $_GET['resend'] === 'success') {
            $data['success'] = "A new verification code has been sent to your email.";
        } elseif (isset($_GET['resend']) && $_GET['resend'] === 'failed') {
            $data['error'] = "Failed to resend verification code to your email. Please try again.";
        }
        $this->view('auth/verify-otp', $data);
    }

    public function forgotPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCsrf($_POST['csrf_token'] ?? '')) {
                die("CSRF token validation failed.");
            }
            $email = $_POST['email'] ?? '';
            $user = $this->userModel->findByEmail($email);

            if ($user) {
                $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                
                $this->userModel->updateOTP($email, $otp, $expiry);
                
                if (Mailer::sendOTP($email, $otp)) {
                    $_SESSION['temp_email'] = $email;
                    $_SESSION['otp_expiry'] = strtotime($expiry) * 1000;
                    $_SESSION['reset_mode'] = true; // Flag for reset flow
                    header('Location: ' . url('/verify-otp'));
                    exit;
                } else {
                    $error = "Failed to send OTP.";
                }
            } else {
                $error = "Email address not found.";
            }
            $this->view('auth/forgot-password', ['error' => $error]);
            return;
        }
        $this->view('auth/forgot-password');
    }

    public function resetPassword(): void
    {
        if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
            header('Location: ' . url('/forgot-password'));
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCsrf($_POST['csrf_token'] ?? '')) {
                die("CSRF token validation failed.");
            }
            $password = $_POST['password'] ?? '';
            $confirmpass = $_POST['confirmpass'] ?? '';
            $email = $_SESSION['reset_email'] ?? '';

            if ($password !== $confirmpass) {
                $error = "Passwords do not match.";
            } else {
                if ($this->userModel->updatePassword($email, $password)) {
                    unset($_SESSION['otp_verified'], $_SESSION['reset_email'], $_SESSION['reset_mode']);
                    header('Location: ' . url('/login') . '?reset=success');
                    exit;
                } else {
                    $error = "Failed to reset password. Please try again.";
                }
            }
            $this->view('auth/reset-password', ['error' => $error]);
            return;
        }
        $this->view('auth/reset-password');
    }

    public function changeRegistrationEmail(): void
    {
        $currentEmail = $_SESSION['temp_email'] ?? '';
        if (!$currentEmail) {
            header('Location: ' . url('/register'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCsrf($_POST['csrf_token'] ?? '')) {
                die("CSRF token validation failed.");
            }

            $newEmail = $_POST['email'] ?? '';
            
            // Check if new email is already taken by a verified user
            $existing = $this->userModel->findByEmail($newEmail);
            if ($existing && $existing['is_verified']) {
                $this->view('auth/change-registration-email', [
                    'error' => 'This email is already registered.',
                    'current_email' => $newEmail
                ]);
                return;
            }

            // Update the email and generate new OTP
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            // We need a way to update the email of an unverified user
            // I'll add a helper to User model or do it here.
            $db = getDbConnection();
            $stmt = $db->prepare("UPDATE tenant_accounts SET email = :new_email, otp_code = :otp, otp_expiry = :expiry WHERE email = :old_email AND is_verified = 0");
            $success = $stmt->execute([
                'new_email' => $newEmail,
                'otp' => $otp,
                'expiry' => $expiry,
                'old_email' => $currentEmail
            ]);

            if ($success) {
                if (Mailer::sendOTP($newEmail, $otp)) {
                    $_SESSION['temp_email'] = $newEmail;
                    $_SESSION['otp_expiry'] = strtotime($expiry) * 1000;
                    
                    // Update temp_reg_data if it exists
                    if (isset($_SESSION['temp_reg_data'])) {
                        $_SESSION['temp_reg_data']['email'] = $newEmail;
                    }

                    header('Location: ' . url('/verify-otp') . '?resend=success');
                    exit;
                } else {
                    $error = "Email updated but failed to send OTP.";
                }
            } else {
                $error = "Failed to update email. It might already be in use.";
            }
            
            $this->view('auth/change-registration-email', ['error' => $error, 'current_email' => $newEmail]);
            return;
        }

        $this->view('auth/change-registration-email', ['current_email' => $currentEmail]);
    }

    public function resendOtp(): void
    {
        $email = trim($_SESSION['temp_email'] ?? '');
        if ($email) {
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            
            error_log("[OTP_DEBUG] Resend OTP requested for '{$email}'. Generated new OTP: {$otp}");

            // Send email FIRST to ensure delivery before updating DB
            if (Mailer::sendOTP($email, $otp)) {
                $this->userModel->updateOTP($email, $otp, $expiry);
                $_SESSION['otp_expiry'] = strtotime($expiry) * 1000;
                error_log("[OTP_DEBUG] Resend OTP email delivered successfully for '{$email}'. DB updated with new OTP.");
                header('Location: ' . url('/verify-otp') . '?resend=success');
                exit;
            } else {
                error_log("[OTP_DEBUG] Resend OTP email delivery FAILED for '{$email}'. Database OTP was NOT overwritten.");
                header('Location: ' . url('/verify-otp') . '?resend=failed');
                exit;
            }
        }
        $redirect = (isset($_SESSION['reset_mode']) && $_SESSION['reset_mode']) ? '/forgot-password' : '/register';
        header('Location: ' . url($redirect));
        exit;
    }

    public function logout(): void
    {
        AuditLogger::log('AUTH', 'LOGOUT', "User logged out of the system");

        // Unset all session variables
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
        
        header('Location: ' . url('/login'));
        exit;
    }
}

