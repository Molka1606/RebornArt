<?php
/**
 * PHPMailer Installation Script
 * This script will download and install PHPMailer manually
 * Run this in your browser: http://localhost/res_event/install_phpmailer.php
 */

// Check if vendor folder exists
$vendorDir = __DIR__ . '/vendor';
if (!is_dir($vendorDir)) {
    mkdir($vendorDir, 0755, true);
}

$phpmailerDir = $vendorDir . '/phpmailer/phpmailer';
$phpmailerZip = __DIR__ . '/phpmailer.zip';

echo "<h2>PHPMailer Installation</h2>";
echo "<style>body { font-family: Arial; padding: 20px; } .ok { color: green; } .error { color: red; }</style>";

// Check if already installed
if (is_dir($phpmailerDir) && file_exists($phpmailerDir . '/src/PHPMailer.php')) {
    echo "<p class='ok'>✅ PHPMailer is already installed!</p>";
    echo "<p>Location: " . htmlspecialchars($phpmailerDir) . "</p>";
    echo "<p><a href='public/index.php'>Go to Home</a></p>";
    exit;
}

// Download PHPMailer
if (isset($_GET['download'])) {
    echo "<h3>Downloading PHPMailer...</h3>";
    
    $downloadUrl = 'https://github.com/PHPMailer/PHPMailer/archive/refs/heads/master.zip';
    
    $zipContent = @file_get_contents($downloadUrl);
    if ($zipContent === false) {
        echo "<p class='error'>❌ Failed to download PHPMailer. Please download manually:</p>";
        echo "<ol>";
        echo "<li>Go to: <a href='https://github.com/PHPMailer/PHPMailer/releases' target='_blank'>PHPMailer Releases</a></li>";
        echo "<li>Download the latest ZIP file</li>";
        echo "<li>Extract it to: <code>vendor/phpmailer/phpmailer/</code></li>";
        echo "</ol>";
        exit;
    }
    
    file_put_contents($phpmailerZip, $zipContent);
    echo "<p class='ok'>✅ Download complete!</p>";
    
    // Extract
    echo "<h3>Extracting...</h3>";
    $zip = new ZipArchive;
    if ($zip->open($phpmailerZip) === TRUE) {
        // Extract to temp location first
        $tempDir = __DIR__ . '/temp_phpmailer';
        $zip->extractTo($tempDir);
        $zip->close();
        
        // Move to correct location
        $extractedDir = $tempDir . '/PHPMailer-master';
        if (is_dir($extractedDir)) {
            if (!is_dir($vendorDir . '/phpmailer')) {
                mkdir($vendorDir . '/phpmailer', 0755, true);
            }
            rename($extractedDir, $phpmailerDir);
            
            // Cleanup
            rmdir($tempDir);
            unlink($phpmailerZip);
            
            echo "<p class='ok'>✅ PHPMailer installed successfully!</p>";
            echo "<p>Location: " . htmlspecialchars($phpmailerDir) . "</p>";
            echo "<p><strong>Next step:</strong> Configure your email settings in <code>config/email_config.php</code></p>";
            echo "<p><a href='public/index.php'>Go to Home</a></p>";
        } else {
            echo "<p class='error'>❌ Extraction failed. Please extract manually.</p>";
        }
    } else {
        echo "<p class='error'>❌ Failed to extract ZIP file.</p>";
    }
    exit;
}

// Show installation option
?>
<!DOCTYPE html>
<html>
<head>
    <title>Install PHPMailer</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f5f5f5; }
        .container { max-width: 600px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn { display: inline-block; padding: 12px 24px; background: #008037; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #006628; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Install PHPMailer</h2>
        <p>PHPMailer is required to send emails. This script will download and install it automatically.</p>
        
        <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>What will happen:</strong>
            <ol>
                <li>Download PHPMailer from GitHub</li>
                <li>Extract to <code>vendor/phpmailer/phpmailer/</code></li>
                <li>Ready to use!</li>
            </ol>
        </div>
        
        <a href="?download=1" class="btn">Install PHPMailer Now</a>
        
        <hr style="margin: 30px 0;">
        
        <h3>Manual Installation</h3>
        <p>If automatic installation doesn't work:</p>
        <ol>
            <li>Download from: <a href="https://github.com/PHPMailer/PHPMailer/releases" target="_blank">PHPMailer Releases</a></li>
            <li>Extract ZIP file</li>
            <li>Copy to: <code>vendor/phpmailer/phpmailer/</code></li>
        </ol>
        
        <p><a href="public/index.php">← Back to Home</a></p>
    </div>
</body>
</html>






