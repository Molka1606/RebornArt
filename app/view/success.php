
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$emailSent = $_SESSION['email_sent'] ?? null;
unset($_SESSION['email_sent']); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/res_event/assets/css/success.css">
    <link rel="stylesheet" href="/res_event/assets/css/modern-styles.css" />
</head>
<body>
<div class="success-container bounce-in">
    <div class="card glass" style="background: rgba(255, 255, 255, 0.95); padding: 40px; max-width: 500px; margin: 0 auto; text-align: center;">
        <h2 class="mb-3">🎉 Reservation Successful!</h2>
        <p class="mb-4">Thank you for booking your spot.</p>
        
        <?php if ($emailSent === true): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-envelope"></i> A confirmation email has been sent to your inbox!
            </div>
        <?php elseif ($emailSent === false): ?>
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle"></i> Reservation confirmed, but email could not be sent. Please check your email manually.
            </div>
        <?php endif; ?>
        
        <a href="/res_event/public/index.php" class="btn btn-primary btn-lg">Back to Home</a>
    </div>
</div>
</body>
</html>
