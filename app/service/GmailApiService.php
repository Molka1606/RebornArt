<?php

class GmailApiService {
    private $client;
    private $service;
    private $config;

    public function __construct() {
        $configFile = __DIR__ . '/../../config/gmail_config.php';
        if (file_exists($configFile)) {
            $this->config = require $configFile;
        } else {
            $this->config = [
                'credentials_path' => __DIR__ . '/../../config/credentials.json',
                'token_path' => __DIR__ . '/../../config/token.json',
                'from_email' => 'your-email@gmail.com',
                'from_name' => 'RebornArt Events',
            ];
        }

        $this->initClient();
    }

    private function initClient() {
        if (!class_exists('Google_Client')) {
            $autoloadPath = __DIR__ . '/../../vendor/autoload.php';
            if (file_exists($autoloadPath)) {
                require_once $autoloadPath;
            } else {
                throw new Exception('Google API Client not found. Install with: composer require google/apiclient');
            }
        }

        $this->client = new Google_Client();
        $this->client->setApplicationName('RebornArt Events');
        $this->client->setScopes(Google_Service_Gmail::GMAIL_SEND);
        $this->client->setAuthConfig($this->config['credentials_path']);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists
        $tokenPath = $this->config['token_path'];
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired
        if ($this->client->isAccessTokenExpired()) {
            // Refresh the token if possible, otherwise fetch a new one
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                // Request authorization from the user
                $authUrl = $this->client->createAuthUrl();
                throw new Exception("Please visit this URL to authorize: $authUrl\nThen save the code to config/auth_code.txt");
            }
            
            // Save the token for next run
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
        }

        $this->service = new Google_Service_Gmail($this->client);
    }

    /**
     * Send reservation confirmation email using Gmail API
     */
    public function sendReservationConfirmation($reservation, $event) {
        $to = $reservation['email'];
        $name = $reservation['full_name'];
        $subject = 'Reservation Confirmation - ' . $event['title'];
        
        $body = $this->getReservationEmailTemplate($reservation, $event);
        
        return $this->sendEmail($to, $name, $subject, $body);
    }

    /**
     * Send email using Gmail API
     */
    private function sendEmail($to, $name, $subject, $htmlBody) {
        try {
            $message = $this->createMessage($to, $name, $subject, $htmlBody);
            $result = $this->service->users_messages->send('me', $message);
            return $result->getId() !== null;
        } catch (Exception $e) {
            error_log("Gmail API Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a message for Gmail API
     */
    private function createMessage($to, $name, $subject, $htmlBody) {
        $message = new Google_Service_Gmail_Message();
        
        $rawMessage = $this->createRawMessage(
            $this->config['from_email'],
            $this->config['from_name'],
            $to,
            $name,
            $subject,
            $htmlBody
        );
        
        $message->setRaw($rawMessage);
        return $message;
    }

    /**
     * Create raw email message
     */
    private function createRawMessage($from, $fromName, $to, $toName, $subject, $htmlBody) {
        $boundary = uniqid(rand(), true);
        $altBoundary = uniqid(rand(), true);
        
        $rawMessage = "To: $toName <$to>\r\n";
        $rawMessage .= "From: $fromName <$from>\r\n";
        $rawMessage .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $rawMessage .= "MIME-Version: 1.0\r\n";
        $rawMessage .= "Content-Type: multipart/alternative; boundary=$boundary\r\n";
        $rawMessage .= "\r\n";
        
        // Plain text version
        $plainText = strip_tags($htmlBody);
        $rawMessage .= "--$boundary\r\n";
        $rawMessage .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $rawMessage .= "Content-Transfer-Encoding: base64\r\n";
        $rawMessage .= "\r\n";
        $rawMessage .= chunk_split(base64_encode($plainText)) . "\r\n";
        
        // HTML version
        $rawMessage .= "--$boundary\r\n";
        $rawMessage .= "Content-Type: text/html; charset=UTF-8\r\n";
        $rawMessage .= "Content-Transfer-Encoding: base64\r\n";
        $rawMessage .= "\r\n";
        $rawMessage .= chunk_split(base64_encode($htmlBody)) . "\r\n";
        
        $rawMessage .= "--$boundary--\r\n";
        
        return base64_encode($rawMessage);
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
}






