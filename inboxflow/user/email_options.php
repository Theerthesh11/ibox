<?php
require_once "../user/email_function.php";
//stores the value of the checked checkbox in this array
//starred_mail array stores the value of star-check
$starred_mail = !empty($_POST['star-check']) ? $_POST['star-check'] : array();
//checkbox_value array stores the values of check (checkbox)
//This array is passed into archive,trash,delete,mark_as_read
$checkbox_value = !empty($_POST['check']) ? $_POST['check'] : array();
//fetching data from db for options in email page
if (isset($_GET['option']) && !isset($_POST['reply'])) {
    //decides who starred the mail
    $starred_mail_status = array();
    foreach ($starred_mail as $mail_number) {
        $mail_details_query = "select * from mail_list where mail_no='$mail_number';";
        $mail_details_output = $conn->query($mail_details_query);
        if ($mail_details_output->num_rows > 0) {
            while ($mail_details_result = $mail_details_output->fetch_assoc()) {
                if ($mail_details_result['sender_email'] == $user_details_result['email']) {
                    $starred_status = "sender_starred_status";
                } elseif ($mail_details_result['reciever_email'] == $user_details_result['email']) {
                    $starred_status = "reciever_starred_status";
                } elseif ($mail_details_result['cc'] == $user_details_result['email']) {
                    $starred_status = "cc_starred_status";
                } elseif ($mail_details_result['bcc'] == $user_details_result['email']) {
                    $starred_status = "bcc_starred_status";
                }
            }
            $starred_mail_status[$mail_number] = $starred_status;
        }
    }
    //decides who archived or deleted the mail
    $checkbox_archive_status = array();
    $checkbox_trash_status = array();
    foreach ($checkbox_value as $mail_number) {
        $mail_details_query = "select * from mail_list where mail_no='$mail_number';";
        $mail_details_output = $conn->query($mail_details_query);
        if ($mail_details_output->num_rows > 0) {
            while ($mail_details_result = $mail_details_output->fetch_assoc()) {
                if ($mail_details_result['sender_email'] == $user_details_result['email']) {
                    $archived_status = "sender_archived_status";
                    $trash_status = "sender_trash_status";
                } elseif ($mail_details_result['reciever_email'] == $user_details_result['email']) {
                    $archived_status = "reciever_archived_status";
                    $trash_status = "reciever_trash_status";
                } elseif ($mail_details_result['cc'] == $email) {
                    $archived_status = "cc_archived_status";
                    $trash_status = "cc_trash_status";
                } elseif ($mail_details_result['bcc'] == $email) {
                    $archived_status = "bcc_archived_status";
                    $trash_status = "bcc_trash_status";
                }
            }
            $checkbox_archive_status[$mail_number]= $archived_status;
            $checkbox_trash_status[$mail_number] = $trash_status;
        }
    }
    //fetches data from db when option in url has Inbox as value
    // or (cc='$email' or bcc='$email')
    if ($_GET['option'] == "Inbox" && !isset($_GET['token'])) {
        $_SESSION['last_activity'] = time();
        $inbox_query = "select * from mail_list where (reciever_email='{$email}' and mail_status='sent' and reciever_archived_status is null and reciever_trash_status is null and spam is null) or ((cc='$email' or bcc='$email') and cc_archived_status is null and bcc_archived_status is null and spam is null) and (cc_trash_status is null and bcc_trash_status is null)";
        $inbox_output = $conn->query($inbox_query);
        if ($inbox_output->num_rows > 0) {
            echo "<div class=\"table-container\"><table>";
            email_list("Inbox", $inbox_query, $inbox_output, $page_no);
            echo "</form></table></div>";
        } else {
            echo "<div class=\"alert-message\"><p>No mails in inbox</p></div>";
        }
        email_options($starred_mail, $checkbox_value, $user_details_result);
    } //fetches data from db when option in url has Unread as value 
    elseif ($_GET['option'] == "Unread" && !isset($_GET['token'])) {
        $_SESSION['last_activity'] = time();
        $unread_query = "select * from mail_list where (reciever_email='{$email}' and mail_status='sent') and (reciever_archived_status is null and reciever_trash_status is null )and (spam is null and inbox_status='unread')";
        $unread_output = $conn->query($unread_query);
        if ($unread_output->num_rows > 0) {
            echo "<div class=\"table-container\"><table>";
            email_list("Unread", $unread_query, $unread_output, $page_no);
            echo "</table></div>";
        } else {
            echo "<div class=\"alert-message\"><p>All mails have been read already</p></div>";
        }
        email_options($starred_mail, $checkbox_value, $user_details_result);
    } //fetches data from db when option in url has Sent as value
    elseif ($_GET['option'] == "Sent" && !isset($_GET['token'])) {
        $_SESSION['last_activity'] = time();
        $sent_query = "select * from mail_list where (sender_email='{$email}' and mail_status='sent') and (sender_archived_status is null and sender_trash_status is null)";
        $sent_output = $conn->query($sent_query);
        if ($sent_output->num_rows > 0) {
            echo "<div class=\"table-container\"><table>";
            email_list("Sent", $sent_query, $sent_output, $page_no);
            echo "</table></div>";
        } else {
            echo "<div class=\"alert-message\"><p>No sent messages! <a href=\"email.php?page=Email&option=Compose\">Send</a> one now!</p></div>";
        }
        email_options($starred_mail, $checkbox_value, $user_details_result);
    } //fetches data from db when option in url has Draft as value
    elseif ($_GET['option'] == "Draft" && !isset($_GET['token'])) {
        $_SESSION['last_activity'] = time();
        $draft_query = "select * from mail_list where (sender_email='{$email}'and mail_status='draft')";
        $draft_output = $conn->query($draft_query);
        if ($draft_output->num_rows > 0) {
            echo "<div class=\"table-container\"><table>";
            email_list("Draft", $draft_query, $draft_output, $page_no);
            echo "</table></div>";
        } else {
            echo "<div class=\"alert-message\"><p>No draft mails</p></div>";
        }
        email_options($starred_mail, $checkbox_value, $user_details_result);
    } //fetches data from db when option in url has Starred as value
    elseif ($_GET['option'] == "Starred" && !isset($_GET['token'])) {
        $_SESSION['last_activity'] = time();
        $starred_query = "select * from mail_list where ((reciever_starred_status='{$user_details_result['username']}' or sender_starred_status='{$user_details_result['username']}') and (reciever_trash_status is null or sender_trash_status is null)) or (cc_starred_status='{$user_details_result['username']}' or bcc_starred_status='{$user_details_result['username']}')";
        $starred_output = $conn->query($starred_query);
        if ($starred_output->num_rows > 0) {
            echo "<div class=\"table-container\"><table>";
            email_list("Starred", $starred_query, $starred_output, $page_no);
            echo "</table></div>";
        } else {
            echo "<div class=\"alert-message\"><p>No starred mails</p></div>";
        }
        email_options($starred_mail, $checkbox_value, $user_details_result);
    } //fetches data from db when option in url has Archived as value
    elseif ($_GET['option'] == "Archived" && !isset($_GET['token'])) {
        $_SESSION['last_activity'] = time();
        $archive_query = "select * from mail_list where ((reciever_archived_status='{$user_details_result['username']}' or sender_archived_status='{$user_details_result['username']}') and (reciever_trash_status is null and sender_trash_status is null)) or (cc_archived_status ='{$user_details_result['username']}' or bcc_archived_status='{$user_details_result['username']}' and cc_trash_status is null and bcc_trash_status is null)";
        $archive_output = $conn->query($archive_query);
        if ($archive_output->num_rows > 0) {
            echo "<div class=\"table-container\"><table>";
            email_list("Archived", $archive_query, $archive_output, $page_no);
            echo "</table></div>";
        } else {
            echo "<div class=\"alert-message\"><p>No archived mails</p></div>";
        }
        email_options($starred_mail, $checkbox_value, $user_details_result);
    } //fetches data from db when option in url has Trash as value
    elseif ($_GET['option'] == "Trash" && !isset($_GET['token'])) {
        $_SESSION['last_activity'] = time();
        $trash_query = "SELECT * FROM mail_list WHERE (reciever_trash_status = '{$user_details_result['username']}' OR sender_trash_status = '{$user_details_result['username']}') OR (cc_trash_status = '{$user_details_result['username']}' OR bcc_trash_status = '{$user_details_result['username']}')";
        $trash_output = $conn->query($trash_query);
        if ($trash_output->num_rows > 0) {
            echo "<div class=\"table-container\"><table>";
            email_list("Trash", $trash_query, $trash_output, $page_no);
            echo "</table></div>";
        } else {
            echo "<div class=\"alert-message\"><p>No deleted mails</p></div>";
        }
        email_options($starred_mail, $checkbox_value, $user_details_result);
    } //fetches data from db when option in url has Spam as value
    elseif ($_GET['option'] == "Spam" && !isset($_GET['token'])) {
        $_SESSION['last_activity'] = time();
        $spam_query = "select * from mail_list where (reciever_email='$email' and mail_status='sent') and (reciever_archived_status is null and reciever_trash_status is null) and spam='yes'";
        $spam_output = $conn->query($spam_query);
        if ($spam_output->num_rows > 0) {
            echo "<div class=\"table-container\"><table>";
            email_list("Spam", $spam_query, $spam_output, $page_no);
            echo "</table></div>";
        } else {
            echo "<div class=\"alert-message\"><p>No Spam mails</p></div>";
        }
        email_options($starred_mail, $checkbox_value, $user_details_result);
    }
}
//fetches data according to search content
elseif (isset($_GET['search']) && !isset($_GET['token'])) {
    if (!empty($_GET['search'])) {
        $_SESSION['last_activity'] = time();
        $search_content = sanitizing($_GET['search']);
        $search_query = "select * from mail_list where (reciever_email='$email' or sender_email='$email' or cc='$email' or bcc='$email') and (sender_email like '%$search_content%' or reciever_email like '%$search_content%')";
        $search_output = $conn->query($search_query);
        if ($search_output->num_rows > 0) {
            echo "<div class=\"table-container\"><table>";
            search_list($search_query, $search_output, $page_no, $search_content);
            echo "</table></div>";
        } else {
            echo "<div class=\"alert-message\"><p>No results found</p></div>";
        }
    } else {
        echo "<div class=\"alert-message\"><p>Types something to search..</p></div>";
    }
    email_options($starred_mail, $checkbox_value, $user_details_result);
}
//mail sending code
if (isset($_POST['send']) || isset($_POST['send_mail']) && $_GET['option'] == "Draft") {

    if (!empty($_POST['mail']) && !empty($_POST['subject'])) {

        if (filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {

            $to_mail = $_POST['mail']; //stores recipient mail
            $recipient_name = recipient_name($to_mail);
            $subject = $_POST['subject']; //stores user entered subject
            $notes = $_POST['notes']; //stores user entered notes content
            if (empty($notes)) {
                $spam = "yes";
            } else {
                $spam = NULL;
            }
            $cc = !empty($_POST['cc']) ? $_POST['cc'] : NULL;
            $bcc = !empty($_POST['bcc']) ? $_POST['bcc'] : NULL;
            $cc_inbox_status = !empty($cc) ? "unread" : NULL;
            $bcc_inbox_status = !empty($bcc) ? "unread" : NULL;
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: multipart/mixed; boundary=\"boundary\"\r\n";

            // Add the message part
            $body = "--boundary\r\n";
            $body .= "Content-Type: text/plain; charset=iso-8859-1\r\n";
            $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $body .= $notes . "\r\n";
            $attachment_path = null;
            $attachment_name = "";
            if (!empty($_FILES['file']['name'])) {
                // print_r($_FILES);
                $uploadFolder = '../Attachments/' . bin2hex($token_id) . '/';
                if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['file']['tmp_name'];
                    $fileName = $_FILES['file']['name'];
                    move_uploaded_file($tmpName, $uploadFolder . $fileName);
                }
                // Add the attachment part
                $attachment_path = $uploadFolder . $fileName;
                $file_contents = file_get_contents($attachment_path);
                $encoded_attachment = chunk_split(base64_encode($file_contents));

                $body .= "--boundary\r\n";
                $body .= "Content-Type: application/octet-stream; name=\"" . $fileName . "\"\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n";
                $body .= "Content-Disposition: attachment; filename=\"" . $fileName . "\"\r\n\r\n";
                $body .= $encoded_attachment . "\r\n";
            }

            // End the boundary
            $body .= "--boundary--";
            if (!empty($cc) && !empty($bcc)) {
                $headers = 'From:' . $email . "\r\n";
                $headers .= 'Reply-To: ' . $email . "\r\n";
                $headers .= 'Cc:' . $cc . "\r\n";
                $headers .= 'Bcc:' . $bcc . "\r\n";
            }
            $updated_by = $user_details_result['username']; //stores the username
            $created_by = $user_details_result['username'];
            //mail function to send the stored data
            if (mail($to_mail, $subject, $body, $headers)) {
                $mail_status = "sent";
                //generates unique mail_no for each mail
                if (!isset($_GET['mailno'])) {
                    $mail_no = strtoupper(substr($user_details_result['username'], 0, 2)) . random(5);
                    //inserts the value into the mail_list table
                    $insert_query = $conn->prepare("insert into mail_list ( token_id, mail_no, sender_email,sender_name, reciever_email,reciever_name, cc, bcc, subject, notes,attachment_name,attachment_path, date_of_sending, mail_status,cc_inbox_status,bcc_inbox_status, spam,updated_by, created_by, updated_on) values(?,?,?,?,?,?,?,?,?,?,?,?,current_timestamp,?,?,?,?,?,?,current_timestamp)");
                    $insert_query->bind_param("ssssssssssssssssss", $token_id, $mail_no, $email, $user_details_result['name'], $to_mail, $recipient_name, $cc, $bcc, $subject, $notes, $fileName, $attachment_path, $mail_status, $cc_inbox_status, $bcc_inbox_status, $spam, $created_by, $updated_by);
                    $insert_query->execute();
                } else {
                    $mail_no = $_GET['mailno'];
                    $update_query = $conn->prepare("update mail_list set reciever_email=?,reciever_name=?, cc=?, bcc=?, subject=?, notes=?,attachment_name=?,attachment_path=?, date_of_sending=current_timestamp, mail_status=?,cc_inbox_status=?,bcc_inbox_status=?, spam=?,updated_by=?, created_by=?, updated_on=current_timestamp where mail_no=?");
                    $update_query->bind_param("sssssssssssssss", $to_mail, $recipient_name, $cc, $bcc, $subject, $notes, $fileName, $attachment_path, $mail_status, $cc_inbox_status, $bcc_inbox_status, $spam, $created_by, $updated_by, $mail_no);
                    $update_query->execute();
                }
                echo '<div class="alert-message"><p style="color:green;"> Mail sent successfully</p></div>';
            } else {
                //stores as draft if mail not sent
                $mail_status = "draft";
                //generates unique mail_no for each mail
                if (!isset($_GET['mailno'])) {
                    $mail_no = strtoupper(substr($user_details_result['username'], 0, 2)) . random(5);
                    $insert_query = $conn->prepare("insert into mail_list ( token_id, mail_no, sender_email,sender_name, reciever_email,reciever_name, cc, bcc, subject, notes,attachment_name,attachment_path, date_of_sending, mail_status,cc_inbox_status,bcc_inbox_status, spam,updated_by, created_by, updated_on) values(?,?,?,?,?,?,?,?,?,?,?,?,current_timestamp,?,?,?,?,?,?,current_timestamp)");
                    $insert_query->bind_param("ssssssssssssssssss", $token_id, $mail_no, $email, $user_details_result['name'], $to_mail, $recipient_name, $cc, $bcc, $subject, $notes, $fileName, $attachment_path, $mail_status, $cc_inbox_status, $bcc_inbox_status, $spam, $created_by, $updated_by);
                    $insert_query->execute();
                } else {
                    $mail_no = $_GET['mailno'];
                    echo "<div class=\"alert-message\"><p style=\" color:red;\">mail not sent</p></div>";
                    $update_query = $conn->prepare("update mail_list set mail_no=?, reciever_email=?,reciever_name=?, cc=?, bcc=?, subject=?, notes=?,attachment_name=?,attachment_path=?, date_of_sending=current_timestamp, mail_status=?,cc_inbox_status=?,bcc_inbox_status=?, spam=?,updated_by=?, created_by=?, updated_on=current_timestamp where mail_no=?");
                    $update_query->bind_param("sssssssssssssss", $to_mail, $recipient_name, $cc, $bcc, $subject, $notes, $fileName, $attachment_path, $mail_status, $cc_inbox_status, $bcc_inbox_status, $spam, $created_by, $updated_by, $mail_no);
                    $update_query->execute();
                }
                echo "<div class=\"alert-message\"><p style=\" color:red;\">mail not sent</p></div>";
            }
        } else {
            echo "<div class=\"alert-message\"><p style=\" color:red;\">Enter a valid email id</p></div>";
        }
    }
}
//saves the changes made in user profile
if (isset($_POST['save'])) {
    $name = !empty($_POST['name']) ? sanitizing($_POST['name']) : "";
    $dob = !empty($_POST['dob']) ? sanitizing($_POST['dob']) : "";
    $dob = dateconvertion($dob, "y-m-d");
    $phone_number = !empty($_POST['cell_number']) ? sanitizing($_POST['cell_number']) : "";
    if (!empty($_POST)) {
        $update_details = "update user_details set name='$name', date_of_birth='$dob', phone_no='$phone_number', updated_on = current_timestamp where email='{$result['email']}';";
        $conn->query($update_details);
    }
}
//logout button's code
if (isset($_POST['logout'])) {
    $logout_query = "update user_login_log set logout_time=current_timestamp where login_id='{$_SESSION['login_id']}';";
    $conn->query($logout_query);
    unset($_SESSION['token_id']);
    header("location:user_login.php");
}
