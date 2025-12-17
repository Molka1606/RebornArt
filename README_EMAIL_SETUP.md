# Email Setup Instructions

This project uses PHPMailer for sending reservation confirmation emails.

## Installation Options

### Option 1: Using Composer (Recommended)

1. Install Composer if you haven't already: https://getcomposer.org/download/

2. Navigate to your project root directory:
   ```bash
   cd c:\xampp\htdocs\res_event
   ```

3. Install PHPMailer:
   ```bash
   composer require phpmailer/phpmailer
   ```

### Option 2: Manual Installation

1. Download PHPMailer from: https://github.com/PHPMailer/PHPMailer/releases

2. Extract the files to: `vendor/phpmailer/phpmailer/`

3. The MailService will automatically detect PHPMailer if it's available.

## Email Configuration

1. Open `config/email_config.php`

2. Update the following settings with your email provider:

### For Gmail:
```php
'smtp_host' => 'smtp.gmail.com',
'smtp_port' => 587,
'smtp_username' => 'your-email@gmail.com',
'smtp_password' => 'your-app-password',  // Generate from: https://myaccount.google.com/apppasswords
'smtp_encryption' => 'tls',
'from_email' => 'your-email@gmail.com',
'from_name' => 'RebornArt Events',
```

**Important for Gmail:**
- Enable 2-Factor Authentication on your Google account
- Generate an App Password: https://myaccount.google.com/apppasswords
- Use the generated app password (not your regular password)

### For Outlook/Hotmail:
```php
'smtp_host' => 'smtp-mail.outlook.com',
'smtp_port' => 587,
'smtp_username' => 'your-email@outlook.com',
'smtp_password' => 'your-password',
'smtp_encryption' => 'tls',
```

### For Other SMTP Providers:
- **Yahoo**: smtp.mail.yahoo.com, Port: 587
- **Custom SMTP**: Use your provider's SMTP settings

## Testing

1. Make a test reservation through the website
2. Check the user's email inbox
3. Check spam folder if email is not received
4. Check server error logs if email fails

## Troubleshooting

### Email not sending?
1. Check that PHPMailer is installed correctly
2. Verify SMTP credentials in `config/email_config.php`
3. Check server error logs
4. For Gmail, ensure you're using an App Password, not your regular password
5. Check firewall settings (port 587 or 465 must be open)

### Fallback Mode
If PHPMailer is not available, the system will fall back to PHP's built-in `mail()` function. However, this is less reliable and may not work on all servers.

## Security Notes

- Never commit `config/email_config.php` with real credentials to version control
- Use environment variables or secure configuration management in production
- Consider using `.gitignore` to exclude the config file






