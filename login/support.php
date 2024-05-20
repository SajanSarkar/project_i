<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $supportEmail = $_POST['supportEmail'];
    $supportMessage = $_POST['supportMessage'];

    // Send the support email (assuming you have a configured mail server)
    $to = 'support@example.com';
    $subject = 'Support Request';
    $message = "From: $supportEmail\n\n$supportMessage";
    $headers = "From: $supportEmail";

    if (mail($to, $subject, $message, $headers)) {
        echo "Support message sent successfully.";
    } else {
        echo "Failed to send support message.";
    }
}
?>
