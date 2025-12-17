# Quick Fix: Email Error

## The Problem
You're getting this error:
```
Warning: mail(): Failed to connect to mailserver at "localhost" port 25
```

This happens because:
1. PHPMailer is not installed
2. The system falls back to PHP's `mail()` function
3. PHP's `mail()` requires a mail server (which you don't have)

## Solution: Install PHPMailer

### Option 1: Automatic Installation (Easiest)

1. Open in browser: `http://localhost/res_event/install_phpmailer.php`
2. Click "Install PHPMailer Now"
3. Wait for download and extraction
4. Done!

### Option 2: Manual Installation

1. Go to: https://github.com/PHPMailer/PHPMailer/releases
2. Download the latest ZIP file (e.g., `PHPMailer-6.9.1.zip`)
3. Extract the ZIP file
4. Copy the extracted folder to: `vendor/phpmailer/phpmailer/`
5. The structure should be:
   ```
   vendor/
   └── phpmailer/
       └── phpmailer/
           └── src/
               ├── PHPMailer.php
               ├── SMTP.php
               └── Exception.php
   ```

### Option 3: Using Composer (If Available)

If you have Composer installed:
```bash
cd c:\xampp\htdocs\res_event
composer require phpmailer/phpmailer
```

## After Installation

1. **Configure Email Settings:**
   - Open `config/email_config.php`
   - Update with your Gmail credentials:
     ```php
     'smtp_username' => 'your-email@gmail.com',
     'smtp_password' => 'your-app-password',  // Get from: https://myaccount.google.com/apppasswords
     'from_email' => 'your-email@gmail.com',
     ```

2. **For Gmail:**
   - Enable 2-Factor Authentication
   - Generate App Password: https://myaccount.google.com/apppasswords
   - Use the app password (not your regular password)

3. **Test:**
   - Make a test reservation
   - Check if email is sent successfully

## Verify Installation

Check if PHPMailer is installed:
- Look for: `vendor/phpmailer/phpmailer/src/PHPMailer.php`
- Or open: `http://localhost/res_event/install_phpmailer.php`

## Still Having Issues?

1. Check file permissions (vendor folder should be writable)
2. Check PHP error logs
3. Verify email config settings
4. Make sure you're using Gmail App Password (not regular password)






