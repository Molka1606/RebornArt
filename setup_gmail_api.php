<?php
/**
 * Gmail API Setup Helper Script
 * Run this script once to authorize your Gmail account
 * 
 * Usage: Open in browser: http://localhost/res_event/setup_gmail_api.php
 */

require_once __DIR__ . '/vendor/autoload.php';

if (!class_exists('Google_Client')) {
    die('
    <h2>Google API Client Not Found</h2>
    <p>Please install Google API Client first:</p>
    <ol>
        <li>Install Composer: <a href="https://getcomposer.org/download/" target="_blank">Download Composer</a></li>
        <li>Run: <code>composer require google/apiclient</code></li>
        <li>Or follow manual installation in <code>INSTALL_GOOGLE_API.md</code></li>
    </ol>
    ');
}

$configFile = __DIR__ . '/config/gmail_config.php';
if (!file_exists($configFile)) {
    die('
    <h2>Gmail Config Not Found</h2>
    <p>Please create <code>config/gmail_config.php</code> first.</p>
    <p>See <code>README_GMAIL_API_SETUP.md</code> for instructions.</p>
    ');
}

$config = require $configFile;
$credentialsPath = $config['credentials_path'];
$tokenPath = $config['token_path'];

if (!file_exists($credentialsPath)) {
    die('
    <h2>Credentials File Not Found</h2>
    <p>Please download your OAuth 2.0 credentials from Google Cloud Console:</p>
    <ol>
        <li>Go to <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
        <li>Select your project</li>
        <li>Go to APIs & Services → Credentials</li>
        <li>Download OAuth 2.0 Client ID JSON</li>
        <li>Save as: <code>' . htmlspecialchars($credentialsPath) . '</code></li>
    </ol>
    ');
}

// Initialize Google Client
$client = new Google_Client();
$client->setApplicationName('RebornArt Events');
$client->setScopes(Google_Service_Gmail::GMAIL_SEND);
$client->setAuthConfig($credentialsPath);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

// Check if we have authorization code
if (isset($_GET['code'])) {
    // Exchange authorization code for access token
    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (array_key_exists('error', $accessToken)) {
        die('
        <h2>Authorization Error</h2>
        <p>Error: ' . htmlspecialchars($accessToken['error_description']) . '</p>
        <p><a href="setup_gmail_api.php">Try Again</a></p>
        ');
    }
    
    // Save token
    if (!file_exists(dirname($tokenPath))) {
        mkdir(dirname($tokenPath), 0700, true);
    }
    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    
    echo '
    <h2>✅ Authorization Successful!</h2>
    <p>Your Gmail account has been authorized. You can now use Gmail API to send emails.</p>
    <p><strong>Next Steps:</strong></p>
    <ol>
        <li>Update <code>app/controller/ReservationController.php</code> to use GmailApiService</li>
        <li>Test by making a reservation</li>
    </ol>
    <p><a href="public/index.php">Go to Home Page</a></p>
    ';
    exit;
}

// Check if token already exists
if (file_exists($tokenPath)) {
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);
    
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            echo '<h2>✅ Token Refreshed Successfully!</h2>';
        } else {
            // Need to re-authorize
            $authUrl = $client->createAuthUrl();
            echo '
            <h2>Re-authorization Required</h2>
            <p>Your access token has expired. Please authorize again:</p>
            <p><a href="' . htmlspecialchars($authUrl) . '" class="btn btn-primary">Authorize Gmail</a></p>
            ';
            exit;
        }
    } else {
        echo '
        <h2>✅ Already Authorized</h2>
        <p>Your Gmail account is already authorized and ready to use.</p>
        <p><strong>Token expires:</strong> ' . date('Y-m-d H:i:s', $client->getAccessToken()['created'] + $client->getAccessToken()['expires_in']) . '</p>
        <p><a href="public/index.php">Go to Home Page</a></p>
        ';
        exit;
    }
}

// First time authorization
$authUrl = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gmail API Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 40px; background: #f5f5f5; }
        .container { max-width: 600px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gmail API Authorization</h2>
        <p>To use Gmail API for sending emails, you need to authorize this application to access your Gmail account.</p>
        
        <div class="alert alert-info">
            <strong>What this does:</strong> This will allow the application to send emails on your behalf using your Gmail account. The authorization is secure and uses OAuth2.
        </div>
        
        <ol>
            <li>Click the button below</li>
            <li>Sign in with your Google account</li>
            <li>Grant permission to send emails</li>
            <li>You'll be redirected back here</li>
        </ol>
        
        <a href="<?= htmlspecialchars($authUrl) ?>" class="btn btn-primary btn-lg">Authorize Gmail</a>
        
        <hr>
        
        <h5>Already have credentials?</h5>
        <p>If you've already authorized, you can test the connection:</p>
        <a href="?test=1" class="btn btn-secondary">Test Connection</a>
    </div>
</body>
</html>






