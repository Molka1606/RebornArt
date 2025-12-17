# Gmail API Setup Instructions

This guide will help you set up the Gmail API for sending emails instead of SMTP.

## Why Use Gmail API?

- ✅ OAuth2 authentication (more secure than app passwords)
- ✅ Better rate limits (higher sending capacity)
- ✅ More reliable delivery
- ✅ Better tracking and analytics
- ✅ No need for app passwords

## Prerequisites

1. A Google account
2. Composer installed
3. PHP 7.4 or higher

## Step-by-Step Setup

### Step 1: Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Click "Select a project" → "New Project"
3. Enter project name: "RebornArt Events" (or any name)
4. Click "Create"

### Step 2: Enable Gmail API

1. In the project dashboard, go to "APIs & Services" → "Library"
2. Search for "Gmail API"
3. Click on "Gmail API"
4. Click "Enable"

### Step 3: Create OAuth 2.0 Credentials

1. Go to "APIs & Services" → "Credentials"
2. Click "Create Credentials" → "OAuth client ID"
3. If prompted, configure OAuth consent screen:
   - User Type: External (or Internal if using Google Workspace)
   - App name: "RebornArt Events"
   - User support email: Your email
   - Developer contact: Your email
   - Click "Save and Continue"
   - Scopes: Add "https://www.googleapis.com/auth/gmail.send"
   - Click "Save and Continue"
   - Test users: Add your email (for testing)
   - Click "Save and Continue"
4. Application type: **Desktop app**
5. Name: "RebornArt Events Mailer"
6. Click "Create"
7. Click "Download JSON"
8. Save the file as `config/credentials.json` in your project

### Step 4: Install Google API Client

```bash
cd c:\xampp\htdocs\res_event
composer require google/apiclient
```

### Step 5: Authorize the Application

1. Update `config/gmail_config.php` with your email
2. Make a test reservation (or run a setup script)
3. You'll get an authorization URL
4. Visit the URL in your browser
5. Sign in with your Google account
6. Click "Allow" to grant permissions
7. Copy the authorization code
8. Save it to `config/auth_code.txt`
9. The system will exchange it for tokens automatically

### Step 6: Update ReservationController

In `app/controller/ReservationController.php`, change:

```php
// From:
$mailService = new MailService();

// To:
require_once __DIR__ . '/../service/GmailApiService.php';
$mailService = new GmailApiService();
```

## File Structure

```
res_event/
├── config/
│   ├── credentials.json      (Downloaded from Google Cloud)
│   ├── token.json            (Auto-generated after authorization)
│   └── gmail_config.php      (Configuration file)
├── app/
│   └── service/
│       ├── MailService.php       (SMTP - current)
│       └── GmailApiService.php  (Gmail API - new)
```

## Testing

1. Make a test reservation
2. Check the email inbox
3. Verify the email was sent via Gmail API

## Troubleshooting

### "Credentials not found"
- Make sure `config/credentials.json` exists
- Check file permissions

### "Token expired"
- Delete `config/token.json`
- Re-authorize the application

### "Access denied"
- Make sure Gmail API is enabled
- Check OAuth consent screen is configured
- Verify scopes include `gmail.send`

### Rate Limits
- Gmail API: 1 billion quota units per day
- Sending one email = 100 quota units
- That's ~10 million emails per day (plenty for most use cases)

## Comparison: SMTP vs Gmail API

| Feature | SMTP | Gmail API |
|---------|------|-----------|
| Setup Complexity | Easy | Moderate |
| Authentication | App Password | OAuth2 |
| Rate Limits | Lower | Higher |
| Reliability | Good | Excellent |
| Security | Good | Better |
| Tracking | Limited | Advanced |

## Need Help?

- Google Cloud Console: https://console.cloud.google.com/
- Gmail API Docs: https://developers.google.com/gmail/api
- OAuth2 Guide: https://developers.google.com/identity/protocols/oauth2






