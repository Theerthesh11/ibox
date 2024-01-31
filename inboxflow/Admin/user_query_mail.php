<?php
$to_mail = "inboxflowpvt@gmail.com";
$subject = "Password Reset Request";
$message = 'Dear Theertheshwaran ,' . "\n\t" . 'We have received a request to reset the password for your account. To complete the password reset process, please follow the instructions below:' . "\r\n" . 'Enter the following One-Time Password (OTP) when prompted: 44567' . "\n" . 'Token = 1234567890' . "\r\n" . 'Follow the on-screen instructions to create a new password for your account.'."\r\n".'Please ensure that you complete this process promptly to secure your account. If you did not initiate this password reset request, please contact our support team immediately at inboxflowpvt@gmail.com for assistance.'."\n".'Thank you for your cooperation.'."\n\n".'Best regards,'."\n".'Inboxflow Pvt Ltd';
if (mail($to_mail, $subject, $message)) {
    echo "sent successfully";
} else {
    echo "not sent";
}
