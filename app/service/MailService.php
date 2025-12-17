<?php
/**
 * Mail Service for sending emails
 * Uses PHPMailer library
 * 
 * To install PHPMailer, run: composer require phpmailer/phpmailer
 * Or download from: https://github.com/PHPMailer/PHPMailer
 */

class MailService {
    private $mailer;
    private $config;

    public function __construct() {
        // Load email configuration
        $configFile = __DIR__ . '/../../config/email_config.php';
        if (file_exists($configFile)) {
            $this->config = require $configFile;
        } else {
            // Default configuration - Update these with your SMTP settings
            $this->config = [
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_username' => 'your-email@gmail.com',
                'smtp_password' => 'your-app-password',
                'smtp_encryption' => 'tls',
                'from_email' => 'your-email@gmail.com',
                'from_name' => 'RebornArt Events',
                'reply_to' => 'your-email@gmail.com',
            ];
        }

        // Try to initialize PHPMailer
        $this->initPHPMailer();
    }

    private function initPHPMailer() {
        // Try to load PHPMailer from different possible locations
        $possiblePaths = [
            __DIR__ . '/../../vendor/autoload.php',  // Composer installation
            __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php',  // Manual installation
        ];
        
        $loaded = false;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                require_once $path;
                $loaded = true;
                break;
            }
        }
        
        // If autoload didn't work, try manual includes
        if (!$loaded) {
            $phpmailerPath = __DIR__ . '/../../vendor/phpmailer/phpmailer/src/';
            if (file_exists($phpmailerPath . 'PHPMailer.php')) {
                require_once $phpmailerPath . 'PHPMailer.php';
                require_once $phpmailerPath . 'SMTP.php';
                require_once $phpmailerPath . 'Exception.php';
                $loaded = true;
            }
        }
        
        // Also check for PHPMailer-master (if extracted from GitHub)
        if (!$loaded) {
            $phpmailerPath = __DIR__ . '/../../vendor/phpmailer/phpmailer/PHPMailer-master/src/';
            if (file_exists($phpmailerPath . 'PHPMailer.php')) {
                require_once $phpmailerPath . 'PHPMailer.php';
                require_once $phpmailerPath . 'SMTP.php';
                require_once $phpmailerPath . 'Exception.php';
                $loaded = true;
            }
        }
        
        if (!$loaded || !class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            return false;
        }
        
        $this->mailer = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->config['smtp_host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $this->config['smtp_username'];
        $this->mailer->Password = $this->config['smtp_password'];
        $this->mailer->SMTPSecure = $this->config['smtp_encryption'];
        $this->mailer->Port = $this->config['smtp_port'];
        $this->mailer->CharSet = 'UTF-8';
        
        // Additional settings for better compatibility
        $this->mailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Sender
        $this->mailer->setFrom($this->config['from_email'], $this->config['from_name']);
        
        // Reply-to
        if (!empty($this->config['reply_to'])) {
            $this->mailer->addReplyTo($this->config['reply_to'], $this->config['from_name']);
        }
    }

    /**
     * Send reservation confirmation email
     */
    public function sendReservationConfirmation($reservation, $event) {
        $to = $reservation['email'];
        $name = $reservation['full_name'];
        $subject = 'Reservation Confirmation - ' . $event['title'];
        
        // Get email template
        $body = $this->getReservationEmailTemplate($reservation, $event);
        
        return $this->sendEmail($to, $name, $subject, $body);
    }

    /**
     * Send email using PHPMailer or mail() fallback
     */
    private function sendEmail($to, $name, $subject, $body) {
        if ($this->mailer) {
            // Use PHPMailer
            try {
                $this->mailer->clearAddresses();
                $this->mailer->addAddress($to, $name);
                $this->mailer->isHTML(true);
                $this->mailer->Subject = $subject;
                $this->mailer->Body = $body;
                $this->mailer->AltBody = strip_tags($body);
                
                // Enable verbose debugging (can be disabled in production)
                $this->mailer->SMTPDebug = 0; // 0 = off, 1 = client, 2 = client and server
                
                $result = $this->mailer->send();
                
                if (!$result) {
                    $errorMsg = "PHPMailer Error: " . $this->mailer->ErrorInfo;
                    error_log($errorMsg);
                    // Also log to a file for easier debugging
                    file_put_contents(__DIR__ . '/../../logs/email_errors.log', 
                        date('Y-m-d H:i:s') . " - " . $errorMsg . "\n", 
                        FILE_APPEND);
                }
                
                return $result;
            } catch (Exception $e) {
                $errorMsg = "Mail Exception: " . $e->getMessage() . " | PHPMailer Error: " . $this->mailer->ErrorInfo;
                error_log($errorMsg);
                file_put_contents(__DIR__ . '/../../logs/email_errors.log', 
                    date('Y-m-d H:i:s') . " - " . $errorMsg . "\n", 
                    FILE_APPEND);
                return false;
            }
        } else {
            // PHPMailer not available - log error and return false
            $errorMsg = "PHPMailer not installed. Please install it with: composer require phpmailer/phpmailer or run install_phpmailer.php";
            error_log($errorMsg);
            file_put_contents(__DIR__ . '/../../logs/email_errors.log', 
                date('Y-m-d H:i:s') . " - " . $errorMsg . "\n", 
                FILE_APPEND);
            return false;
        }
    }

    /**
     * Get reservation confirmation email template
     */
    private function getReservationEmailTemplate($reservation, $event) {
        $eventDate = date('F j, Y', strtotime($event['event_date']));
        $eventLocation = htmlspecialchars($event['location']);
        $eventTitle = htmlspecialchars($event['title']);
        $eventDescription = nl2br(htmlspecialchars($event['description']));
        $userName = htmlspecialchars($reservation['full_name']);
        $userMessage = !empty($reservation['message']) ? nl2br(htmlspecialchars($reservation['message'])) : 'None';
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #008037, #23d5ab); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .event-details { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #008037; }
                .button { display: inline-block; padding: 12px 30px; background: #008037; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Reservation Confirmed!</h1>
                    <p>Thank you for your reservation</p>
                </div>
                <div class='content'>
                    <p>Dear <strong>{$userName}</strong>,</p>
                    
                    <p>We're excited to confirm your reservation! Your spot has been secured for the following event:</p>
                    
                    <div class='event-details'>
                        <h2 style='color: #008037; margin-top: 0;'>{$eventTitle}</h2>
                        <p><strong>📅 Date:</strong> {$eventDate}</p>
                        <p><strong>📍 Location:</strong> {$eventLocation}</p>
                        <p><strong>📝 Description:</strong></p>
                        <p>{$eventDescription}</p>
                    </div>
                    
                    <p><strong>Your Message:</strong> {$userMessage}</p>
                    
                    <p>We look forward to seeing you at the event! If you have any questions or need to make changes, please don't hesitate to contact us.</p>
                    
                    <p style='margin-top: 30px;'>Best regards,<br><strong>RebornArt Events Team</strong></p>
                </div>
                <div class='footer'>
                    <p>This is an automated confirmation email. Please do not reply to this message.</p>
                    <p>&copy; " . date('Y') . " RebornArt Events. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Update email configuration
     */
    public function setConfig($config) {
        $this->config = array_merge($this->config, $config);
        if ($this->mailer) {
            $this->initPHPMailer();
        }
    }
}

