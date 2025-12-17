<?php
/**
 * Email Testing & Debugging Script
 * Run this to test and debug email sending
 * Open in browser: http://localhost/res_event/test_email.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Email System Diagnostic</h2>";
echo "<style>
    body { font-family: Arial; padding: 20px; background: #f5f5f5; }
    .container { max-width: 800px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .ok { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    pre { background: #f0f0f0; padding: 15px; border-radius: 5px; overflow-x: auto; }
    .test-btn { display: inline-block; padding: 12px 24px; background: #008037; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
    .test-btn:hover { background: #006628; }
</style>";

echo "<div class='container'>";

// Check 1: PHPMailer Installation
echo "<h3>1. PHPMailer Installation Check</h3>";
$phpmailerPath = __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
if (file_exists($phpmailerPath)) {
    echo "<p class='ok'>✅ PHPMailer is installed</p>";
    echo "<p class='info'>Location: " . htmlspecialchars($phpmailerPath) . "</p>";
    
    require_once $phpmailerPath;
    require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';
    require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';
    
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "<p class='ok'>✅ PHPMailer class is available</p>";
        $phpmailerAvailable = true;
    } else {
        echo "<p class='error'>❌ PHPMailer class not found after loading</p>";
        $phpmailerAvailable = false;
    }
} else {
    echo "<p class='error'>❌ PHPMailer is NOT installed</p>";
    echo "<p>Install it: <a href='install_phpmailer.php'>Install PHPMailer</a></p>";
    $phpmailerAvailable = false;
}

// Check 2: Email Configuration
echo "<h3>2. Email Configuration Check</h3>";
$configFile = __DIR__ . '/config/email_config.php';
if (file_exists($configFile)) {
    echo "<p class='ok'>✅ Config file exists</p>";
    $config = require $configFile;
    
    echo "<pre>";
    echo "SMTP Host: " . htmlspecialchars($config['smtp_host']) . "\n";
    echo "SMTP Port: " . htmlspecialchars($config['smtp_port']) . "\n";
    echo "SMTP Username: " . htmlspecialchars($config['smtp_username']) . "\n";
    echo "SMTP Password: " . (empty($config['smtp_password']) || $config['smtp_password'] === 'your-app-password' ? '<span class="error">NOT SET</span>' : '✅ Set') . "\n";
    echo "From Email: " . htmlspecialchars($config['from_email']) . "\n";
    echo "From Name: " . htmlspecialchars($config['from_name']) . "\n";
    echo "</pre>";
    
    // Check if credentials are configured
    if (empty($config['smtp_password']) || $config['smtp_password'] === 'your-app-password' || $config['smtp_username'] === 'your-email@gmail.com') {
        echo "<p class='warning'>⚠️ Email credentials are not configured!</p>";
        echo "<p>Please update <code>config/email_config.php</code> with your Gmail credentials.</p>";
        $configValid = false;
    } else {
        echo "<p class='ok'>✅ Email credentials are configured</p>";
        $configValid = true;
    }
} else {
    echo "<p class='error'>❌ Config file not found</p>";
    $configValid = false;
}

// Check 3: Test Email Sending
if (isset($_GET['test']) && $phpmailerAvailable && $configValid) {
    echo "<h3>3. Testing Email Sending</h3>";
    
    $testEmail = $_GET['email'] ?? $config['from_email'];
    
    try {
        require_once __DIR__ . '/app/service/MailService.php';
        
        // Create a test reservation and event
        $testReservation = [
            'full_name' => 'Test User',
            'email' => $testEmail,
            'message' => 'This is a test email'
        ];
        
        $testEvent = [
            'title' => 'Test Event',
            'location' => 'Test Location',
            'event_date' => date('Y-m-d', strtotime('+7 days')),
            'description' => 'This is a test event for email verification'
        ];
        
        $mailService = new MailService();
        $result = $mailService->sendReservationConfirmation($testReservation, $testEvent);
        
        if ($result) {
            echo "<p class='ok'>✅ Test email sent successfully!</p>";
            echo "<p>Check your inbox: <strong>" . htmlspecialchars($testEmail) . "</strong></p>";
            echo "<p>Also check your spam folder.</p>";
        } else {
            echo "<p class='error'>❌ Failed to send test email</p>";
            echo "<p>Check PHP error logs for details.</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
} else {
    echo "<h3>3. Test Email Sending</h3>";
    if (!$phpmailerAvailable) {
        echo "<p class='warning'>⚠️ Cannot test - PHPMailer not installed</p>";
    } elseif (!$configValid) {
        echo "<p class='warning'>⚠️ Cannot test - Email config not valid</p>";
    } else {
        $testEmail = $config['from_email'] ?? '';
        echo "<p>Send a test email to verify everything works:</p>";
        echo "<form method='get' style='margin: 20px 0;'>";
        echo "<input type='hidden' name='test' value='1'>";
        echo "<label>Test Email Address: <input type='email' name='email' value='" . htmlspecialchars($testEmail) . "' required style='padding: 8px; width: 300px; margin-left: 10px;'></label>";
        echo "<br><br>";
        echo "<button type='submit' class='test-btn'>Send Test Email</button>";
        echo "</form>";
    }
}

// Check 4: PHP Error Logs
echo "<h3>4. Recent Errors</h3>";
$errorLog = ini_get('error_log');
if ($errorLog && file_exists($errorLog)) {
    $errors = file_get_contents($errorLog);
    $recentErrors = array_slice(explode("\n", $errors), -20);
    echo "<pre>" . htmlspecialchars(implode("\n", $recentErrors)) . "</pre>";
} else {
    echo "<p class='info'>Error log location: " . ($errorLog ?: 'Default PHP error log') . "</p>";
    echo "<p>Check XAMPP error logs: <code>C:\xampp\php\logs\php_error_log</code></p>";
}

// Check 5: MailService Debug
echo "<h3>5. MailService Status</h3>";
if ($phpmailerAvailable) {
    try {
        require_once __DIR__ . '/app/service/MailService.php';
        $mailService = new MailService();
        
        // Use reflection to check private properties
        $reflection = new ReflectionClass($mailService);
        $mailerProperty = $reflection->getProperty('mailer');
        $mailerProperty->setAccessible(true);
        $mailer = $mailerProperty->getValue($mailService);
        
        if ($mailer) {
            echo "<p class='ok'>✅ MailService initialized with PHPMailer</p>";
        } else {
            echo "<p class='error'>❌ MailService initialized but mailer is null</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error initializing MailService: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='public/index.php' class='test-btn'>Go to Home</a> ";
echo "<a href='test_email.php' class='test-btn'>Refresh</a></p>";

echo "</div>";






