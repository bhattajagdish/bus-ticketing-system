<?php 
    session_start();
    include("connection.php");
    include("nav.php");

// Include shared email configuration (handles PHPMailer loading)
include("email_config.php");

// Import PHPMailer classes for this script
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// OTP expiry constant
    define('OTP_EXPIRY', 10 * 60);                              // 10 minutes in seconds


    // make sure OTP table exists (avoid insert errors)
    $createOtpTable = "CREATE TABLE IF NOT EXISTS `password_reset_otp` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `email` varchar(255) NOT NULL,
        `otp` varchar(6) NOT NULL,
        `expiry_time` bigint(20) NOT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `user_id` (`user_id`),
        KEY `email` (`email`),
        KEY `expiry_time` (`expiry_time`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    mysqli_query($conn, $createOtpTable);
    // make sure email column exists (older installs might lack it)
    $alter = "ALTER TABLE password_reset_otp ADD COLUMN IF NOT EXISTS email varchar(255) NOT NULL AFTER user_id";
    if (!mysqli_query($conn, $alter)) {
        error_log('Could not add email column to password_reset_otp: ' . mysqli_error($conn));
    }

    $error = '';
    $success = '';
    $step = 1; // Step 1: Email, Step 2: OTP Verification, Step 3: Reset Password

    // Function to send OTP via Gmail using PHPMailer (classes already loaded by email_config.php)
function sendOTPEmail($email, $otp) {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = GMAIL_ADDRESS;
        $mail->Password = GMAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // debugging output to error log (comment out in production)
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'error_log';

        $mail->setFrom(GMAIL_ADDRESS, 'Bus Ticketing System');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset OTP';
        $mail->Body = "<h2>Your OTP: $otp</h2><p>Valid for 10 minutes</p>";

        if (!$mail->send()) {
            error_log('OTP email send failed: ' . $mail->ErrorInfo);
            return false;
        }
        return true;
    } catch (Exception $e) {
        error_log('OTP email exception: ' . $e->getMessage());
        return false;
    }
}

    // Check if user submitted the form
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // Step 1: User enters email
        if (isset($_POST['action']) && $_POST['action'] == 'send_otp') {
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Please enter a valid email address!";
                $step = 1;
            } else {
                // Check if email exists
                $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
                $result = mysqli_query($conn, $query);
                
                if (mysqli_num_rows($result) > 0) {
                    $user = mysqli_fetch_assoc($result);
                    
                    // Generate 6-digit OTP
                    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    $expiry_time = time() + OTP_EXPIRY;
                    
                    // Store OTP in database
                    $insert_otp = "INSERT INTO password_reset_otp (user_id, email, otp, expiry_time, created_at) 
                                   VALUES ({$user['id']}, '$email', '$otp', $expiry_time, NOW())
                                   ON DUPLICATE KEY UPDATE otp = '$otp', expiry_time = $expiry_time, created_at = NOW()";
                    
                    if (mysqli_query($conn, $insert_otp)) {
                        // Send OTP to email
                        if (sendOTPEmail($email, $otp)) {
                            $_SESSION['reset_email'] = $email;
                            $_SESSION['reset_user_id'] = $user['id'];
                            $step = 2;
                            $success = "OTP sent to your email! Please check your inbox.";
                        } else {
                            $error = "Failed to send OTP. Please try again later.";
                            $step = 1;
                        }
                    } else {
                        error_log('OTP insert failed: ' . mysqli_error($conn));
                        $error = "Error processing request. Please try again!";
                        $step = 1;
                    }
                } else {
                    $error = "Email not found in our system!";
                    $step = 1;
                }
            }
        }
        
        // Step 2: User verifies OTP
        elseif (isset($_POST['action']) && $_POST['action'] == 'verify_otp') {
            if (!isset($_SESSION['reset_user_id'])) {
                $error = "Session expired. Please start again.";
                $step = 1;
                session_unset();
            } else {
                $user_id = $_SESSION['reset_user_id'];
                $entered_otp = mysqli_real_escape_string($conn, $_POST['otp']);
                
                // Check OTP validity
                $otp_query = "SELECT * FROM password_reset_otp 
                             WHERE user_id = $user_id AND otp = '$entered_otp' AND expiry_time > " . time();
                $otp_result = mysqli_query($conn, $otp_query);
                
                if (mysqli_num_rows($otp_result) > 0) {
                    $_SESSION['otp_verified'] = true;
                    $step = 3;
                    $success = "OTP verified! Please set your new password.";
                } else {
                    $error = "Invalid or expired OTP! Please try again.";
                    $step = 2;
                }
            }
        }
        
        // Step 3: Reset password
        elseif (isset($_POST['action']) && $_POST['action'] == 'reset_password') {
            if (!isset($_SESSION['reset_user_id']) || !isset($_SESSION['otp_verified'])) {
                $error = "Session expired. Please start again.";
                $step = 1;
                session_unset();
            } else {
                $user_id = $_SESSION['reset_user_id'];
                $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
                $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
                
                // Validate password
                if (strlen($new_password) < 6) {
                    $error = "Password must be at least 6 characters!";
                    $step = 3;
                } elseif ($new_password !== $confirm_password) {
                    $error = "Passwords do not match!";
                    $step = 3;
                } else {
                    // Update password
                   $hashed = password_hash($new_password, PASSWORD_DEFAULT);

$update_query = "UPDATE users SET password = '$hashed' WHERE id = $user_id";
                    
                    if (mysqli_query($conn, $update_query)) {
                        // Delete used OTP
                        mysqli_query($conn, "DELETE FROM password_reset_otp WHERE user_id = $user_id");
                        
                        $success = "Password reset successfully! Redirecting to login...";
                        session_unset();
                        echo "<script>
                            setTimeout(function() {
                                window.location.href = 'user-login.php';
                            }, 2000);
                        </script>";
                    } else {
                        $error = "Error resetting password. Please try again!";
                        $step = 3;
                    }
                }
            }
        }
    }
    
    // Determine current step from session
    if (isset($_SESSION['reset_user_id']) && isset($_SESSION['otp_verified'])) {
        $step = 3;
    } elseif (isset($_SESSION['reset_user_id'])) {
        $step = 2;
    }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/login.css">
    <style>
        .forgot-password-box {
            width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .forgot-password-box h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .forgot-password-box .step-indicator {
            margin-bottom: 20px;
            color: #666;
            font-size: 14px;
        }

        .forgot-password-box form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .forgot-password-box input {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .forgot-password-box input:focus {
            outline: none;
            border-color: #00b4d8;
            box-shadow: 0 0 5px rgba(0, 180, 216, 0.3);
        }

        .forgot-password-box button {
            padding: 12px;
            background: #00b4d8;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .forgot-password-box button:hover {
            background: #0096c7;
        }

        .forgot-password-box .back-link {
            margin-top: 15px;
        }

        .forgot-password-box a {
            color: #00b4d8;
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-password-box a:hover {
            text-decoration: underline;
        }

        .error {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #c62828;
        }

        .success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #2e7d32;
        }

        .password-strength {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
            display: none;
        }

        .password-strength.show {
            display: block;
        }

        .password-strength.weak {
            background: #ffebee;
            color: #c62828;
        }

        .password-strength.medium {
            background: #fff3e0;
            color: #e65100;
        }

        .password-strength.strong {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .input-group {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 12px;
            cursor: pointer;
            color: #666;
            background: none;
            border: none;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="forgot-password-box">
        <h1>Reset Password</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <!-- Step 1: Enter Email -->
            <p class="step-indicator">Step 1 of 3: Enter your email address</p>
            <form method="POST">
                <input type="hidden" name="action" value="send_otp">
                <input type="email" name="email" placeholder="Enter your registered email" required>
                <button type="submit">Send OTP</button>
                <div class="back-link">
                    <a href="user-login.php">Back to Login</a>
                </div>
            </form>

        <?php elseif ($step == 2): ?>
            <!-- Step 2: Verify OTP -->
            <p class="step-indicator">Step 2 of 3: Enter OTP (Check your email)</p>
            <form method="POST">
                <input type="hidden" name="action" value="verify_otp">
                <input type="text" name="otp" placeholder="Enter 6-digit OTP" maxlength="6" pattern="[0-9]{6}" required>
                <p style="font-size: 13px; color: #666; margin-top: 10px;">OTP expires in 10 minutes</p>
                <button type="submit">Verify OTP</button>
                <div class="back-link">
                    <a href="forget-password.php">Resend OTP</a>&nbsp;|&nbsp;
                    <a href="user-login.php">Back to Login</a>
                </div>
            </form>

        <?php elseif ($step == 3): ?>
            <!-- Step 3: Reset Password -->
            <p class="step-indicator">Step 3 of 3: Set your new password</p>
            <form method="POST" id="resetForm">
                <input type="hidden" name="action" value="reset_password">
                
                <div class="input-group">
                    <input type="password" name="new_password" id="newPassword" placeholder="Enter new password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('newPassword')">👁️</button>
                </div>
                <div id="passwordStrength" class="password-strength"></div>
                
                <div class="input-group">
                    <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">👁️</button>
                </div>
                
                <button type="submit">Reset Password</button>
                <div class="back-link">
                    <a href="user-login.php">Back to Login</a>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        // Password strength checker
        const passwordInput = document.getElementById('newPassword');
        const strengthDisplay = document.getElementById('passwordStrength');

        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 'weak';
                let message = 'Weak password';

                if (password.length >= 8) {
                    if (/[A-Z]/.test(password) && /[0-9]/.test(password) && /[!@#$%^&*]/.test(password)) {
                        strength = 'strong';
                        message = '✓ Strong password';
                    } else if (/[A-Z]/.test(password) || /[0-9]/.test(password)) {
                        strength = 'medium';
                        message = '⚠ Medium password (add numbers/symbols for stronger)';
                    }
                } else if (password.length >= 6) {
                    strength = 'medium';
                    message = '⚠ Medium password (add 2+ more characters)';
                }

                strengthDisplay.className = `password-strength show ${strength}`;
                strengthDisplay.textContent = message;
            });
        }

        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }

        // Form validation
        const resetForm = document.getElementById('resetForm');
        if (resetForm) {
            resetForm.addEventListener('submit', function(e) {
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;

                if (newPassword.length < 6) {
                    e.preventDefault();
                    alert('Password must be at least 6 characters!');
                    return false;
                }

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                    return false;
                }
            });
        }
    </script>
</body>
</html>
