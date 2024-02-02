<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
//Load Composer's autoloader
// require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
function sendmail($sender_email, $sender_name, $receiver_email, $receiver_name, $file_path, $subject, $notes)
{
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'inboxflowpvt@gmail.com';
        $mail->Password   = 'wmoj jxgg qqrn pmsd';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;
        $mail->setFrom($sender_email, $sender_name);
        $mail->addAddress($receiver_email, $receiver_name);
        $mail->addReplyTo($sender_email, $sender_name);
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');
        if ($file_path != NULL) {
            $mail->addAttachment($file_path);
        }
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $notes;
        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
