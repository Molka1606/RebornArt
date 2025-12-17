# How to Switch from SMTP to Gmail API

Once you've installed Google API Client, follow these steps to switch from SMTP to Gmail API:

## Step 1: Install Google API Client

Choose one method from `INSTALL_GOOGLE_API.md`:
- **Option 1**: Install Composer, then run `composer require google/apiclient`
- **Option 2**: Manual installation
- **Option 3**: Use composer.phar

## Step 2: Verify Installation

Check if the installation was successful by looking for:
```
vendor/
└── google/
    └── apiclient/
        └── src/
            └── Google/
```

Or run this in your browser:
```
http://localhost/res_event/setup_gmail_api.php
```

If you see "Google API Client Not Found", the installation didn't work.

## Step 3: Authorize Gmail Account

1. Open in browser: `http://localhost/res_event/setup_gmail_api.php`
2. Click "Authorize Gmail"
3. Sign in with your Google account
4. Grant permissions
5. You'll be redirected back with a success message

## Step 4: Update ReservationController

Open `app/controller/ReservationController.php` and find this line (around line 13):

**Change FROM:**
```php
require_once __DIR__ . '/../service/MailService.php';
```

**Change TO:**
```php
require_once __DIR__ . '/../service/GmailApiService.php';
```

**And change this line (around line 36):**
```php
$mailService = new MailService();
```

**To:**
```php
$mailService = new GmailApiService();
```

## Step 5: Test

1. Make a test reservation
2. Check if the confirmation email is sent
3. Verify it's working correctly

## Troubleshooting

### "Class 'Google_Client' not found"
- Google API Client is not installed correctly
- Check `vendor/autoload.php` exists
- Re-run installation

### "Credentials file not found"
- Make sure `config/credentials.json` exists
- Download from Google Cloud Console

### "Token expired"
- Run `setup_gmail_api.php` again to refresh token
- Or delete `config/token.json` and re-authorize

### Still using SMTP?
- Make sure you updated ReservationController.php
- Clear any PHP opcache if enabled
- Restart your web server

## Reverting to SMTP

If you want to go back to SMTP, just change the code back:
```php
require_once __DIR__ . '/../service/MailService.php';
$mailService = new MailService();
```






