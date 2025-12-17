<?php
/**
 * Quick Check: Gmail API Setup Status
 * Open in browser to verify your Gmail API setup
 */

echo "<h2>Gmail API Setup Status</h2>";
echo "<style>body { font-family: Arial; padding: 20px; } .ok { color: green; } .error { color: red; } .info { color: blue; }</style>";

// Check 1: Google API Client
echo "<h3>1. Google API Client Installation</h3>";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    if (class_exists('Google_Client')) {
        echo "<p class='ok'>✅ Google API Client is installed</p>";
    } else {
        echo "<p class='error'>❌ Google API Client class not found</p>";
        echo "<p>Run: <code>composer require google/apiclient</code></p>";
    }
} else {
    echo "<p class='error'>❌ vendor/autoload.php not found</p>";
    echo "<p>Install Composer and run: <code>composer require google/apiclient</code></p>";
}

// Check 2: Config file
echo "<h3>2. Gmail Configuration</h3>";
$configFile = __DIR__ . '/config/gmail_config.php';
if (file_exists($configFile)) {
    echo "<p class='ok'>✅ config/gmail_config.php exists</p>";
    $config = require $configFile;
} else {
    echo "<p class='error'>❌ config/gmail_config.php not found</p>";
    echo "<p>Create it using config/gmail_config.php as template</p>";
}

// Check 3: Credentials
echo "<h3>3. OAuth Credentials</h3>";
if (isset($config)) {
    $credentialsPath = $config['credentials_path'];
    if (file_exists($credentialsPath)) {
        echo "<p class='ok'>✅ Credentials file found: " . htmlspecialchars($credentialsPath) . "</p>";
    } else {
        echo "<p class='error'>❌ Credentials file not found: " . htmlspecialchars($credentialsPath) . "</p>";
        echo "<p>Download from Google Cloud Console and save as credentials.json</p>";
    }
}

// Check 4: Token
echo "<h3>4. Authorization Token</h3>";
if (isset($config)) {
    $tokenPath = $config['token_path'];
    if (file_exists($tokenPath)) {
        echo "<p class='ok'>✅ Authorization token exists</p>";
        $token = json_decode(file_get_contents($tokenPath), true);
        if (isset($token['expires_in'])) {
            $expires = date('Y-m-d H:i:s', $token['created'] + $token['expires_in']);
            echo "<p class='info'>Token expires: $expires</p>";
        }
    } else {
        echo "<p class='error'>❌ Authorization token not found</p>";
        echo "<p>Run <a href='setup_gmail_api.php'>setup_gmail_api.php</a> to authorize</p>";
    }
}

// Check 5: ReservationController
echo "<h3>5. Code Configuration</h3>";
$controllerFile = __DIR__ . '/app/controller/ReservationController.php';
$controllerContent = file_get_contents($controllerFile);
if (strpos($controllerContent, 'GmailApiService') !== false) {
    echo "<p class='ok'>✅ ReservationController is using GmailApiService</p>";
} else {
    echo "<p class='info'>ℹ️ ReservationController is using MailService (SMTP)</p>";
    echo "<p>To switch to Gmail API, see <a href='SWITCH_TO_GMAIL_API.md'>SWITCH_TO_GMAIL_API.md</a></p>";
}

echo "<hr>";
echo "<p><a href='setup_gmail_api.php'>Run Gmail API Setup</a> | <a href='public/index.php'>Go to Home</a></p>";






