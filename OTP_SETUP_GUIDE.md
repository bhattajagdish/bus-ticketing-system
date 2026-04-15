# OTP-Based Password Reset Setup Guide

## Step 1: Create OTP Database Table

Run this SQL query in phpMyAdmin to create the password reset OTP table:

```sql
CREATE TABLE IF NOT EXISTS `password_reset_otp` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

## Step 2: Configure Gmail SMTP (IMPORTANT)

### Option 1: Using Gmail App Password (RECOMMENDED)

1. Go to your **Google Account**: https://myaccount.google.com
2. Click on **Security** in the left sidebar
3. Enable **2-Step Verification** (if not already enabled)
4. Scroll down and find **App passwords**
5. Select **Mail** and **Windows Computer** (or your device)
6. Google will generate a 16-character app password
7. Copy this password

### Option 2: Using Regular Gmail Password

If you don't have 2-Step Verification:
1. Enable **Less secure app access**: https://myaccount.google.com/lesssecureapps
2. Use your regular Gmail password

## Step 3: Update forget-password.php

Open `forget-password.php` and update these lines (around line 7-8):

```php
define('GMAIL_ADDRESS', 'your-email@gmail.com');      // Your Gmail address
define('GMAIL_PASSWORD', 'your-app-password');        // 16-char app password or regular password
```

**Example:**
```php
define('GMAIL_ADDRESS', 'bhattajagdish606@gmail.com');
define('GMAIL_PASSWORD', 'abcd efgh ijkl mnop');  // App password with spaces
```

## Step 4: Enable PHP mail() Function

Make sure your `php.ini` has mail configured:

```ini
[mail function]
SMTP = smtp.gmail.com
smtp_port = 587
```

Or, the script uses PHP's `mail()` function which should work with Gmail's servers.

## How It Works

### User Flow:
1. **Step 1**: User enters email → System sends 6-digit OTP to Gmail
2. **Step 2**: User enters OTP → System verifies OTP (valid for 10 minutes)
3. **Step 3**: User sets new password → System updates database and clears OTP

### OTP Features:
- ✓ 6-digit random OTP
- ✓ 10-minute expiration
- ✓ Beautiful HTML email format
- ✓ One-time use (deleted after password reset)
- ✓ Session-based security

## Testing

1. Go to `http://localhost/bus-ticketing-system/forget-password.php`
2. Enter your test user's email
3. OTP will be sent to that email
4. Check your email inbox for OTP
5. Enter the OTP
6. Set new password
7. Login with new password

## Troubleshooting

**"Failed to send OTP":**
- Check Gmail account credentials
- Ensure 2-Step Verification is enabled
- Use App Password instead of regular password
- Check if "Less secure apps" is enabled

**"OTP not received":**
- Check spam/junk folder
- Verify email address is correct
- Try sending again

**"Invalid or expired OTP":**
- OTP expires after 10 minutes
- Make sure you copy-paste OTP correctly
- Click "Resend OTP" to get a new one

## Security Notes

⚠️ **Never commit actual passwords to Git!**

Use environment variables:
```php
define('GMAIL_ADDRESS', getenv('GMAIL_ADDRESS'));
define('GMAIL_PASSWORD', getenv('GMAIL_PASSWORD'));
```

Or create a config.php file (add to .gitignore):
```php
<?php
return [
    'gmail_address' => 'your-email@gmail.com',
    'gmail_password' => 'your-app-password'
];
?>
```
