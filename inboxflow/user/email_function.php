<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
// ini_set('log_errors', 1);
require_once "../config.php";
global $conn;
//random function generates according to given length
function random($length)
{
    $result = substr(str_shuffle('1234567890ABCDEF'), 0, $length);
    return $result;
}
//returns a char from this to fit in token_id
function random_byte()
{
    return substr(str_shuffle('89AB'), 0, 1);
}
//function sanitizes the data 
function sanitizing($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
//creates user name for non-registered user
function recipient_name($to_mail)
{
    // require 'config.php';
    global $conn;
    //fetching username from db if user exist
    $fetch_name_query = "select name from user_details where email='$to_mail';";
    $fetch_name_output = $conn->query($fetch_name_query);
    if ($fetch_name_output->num_rows > 0) {
        $fetch_name_result = $fetch_name_output->fetch_assoc();
        return $fetch_name_result['name'];
    } else {
        //creating username using mail_id
        $name_find = strpos($to_mail, "@");
        $find_string = substr($to_mail, 0, $name_find);
        $reciever_name = preg_replace('/[0-9]+/', '', $find_string); //replacing numbers in username
        return ucfirst($reciever_name);
    }
}
//total_mail function counts the total mails
function total_mail($column_name, $addition = " ")
{
    global $email, $conn;
    // require "config.php";
    $count_query = "select count($column_name) from mail_list where ($column_name='$email' $addition ;";
    $count_output = $conn->query($count_query);
    if ($count_output->num_rows > 0) {
        if ($result = $count_output->fetch_assoc()) {
            if ($result["count($column_name)"]) {
                echo "<p>" . $result["count($column_name)"] . "</p>";
            } else {
                return 0;
            }
        }
    } else {
        return;
    }
}
//trash_mail function counts the total mails in trash
function trash_mail($column, $column_name, $column_name2, $addition = " ")
{
    global $email, $conn;
    // require_once "../config.php";
    $count_query = "select count($column) from mail_list where ($column_name='{$email}' or $column_name2='{$email}') and $addition";
    $count_output = $conn->query($count_query);
    if ($count_output->num_rows > 0) {
        if ($result = $count_output->fetch_assoc()) {
            if ($result["count($column)"] > 0) {
                echo "<p>" . $result["count($column)"] . "</p>";
            } else {
                return 0;
            }
        }
    } else {
        return;
    }
}
//name_setting sets name for star,archive,trash according to current option in email 
function name_setting($button_name, $value1, $value2)
{
    if (!empty($_GET['option'])) {
        if ($_GET['option'] == $button_name) {
            echo $value1;
        } else {
            echo $value2;
        }
    } else {
        if (isset($_POST[$button_name])) {
            echo $value1;
        } else {
            echo $value2;
        }
    }
}

//converts numeric date from database to alpha numeric format
function dateconvertion($numericdate, $output_format = "d M")
{
    if (!is_null($numericdate)) {
        $dateString = $numericdate;
        $date = new DateTime($dateString);
        $formattedDate = $date->format($output_format);
        return $formattedDate;
    }
    return;
}
//displays the user as me in their application whereever their email appears
function usermail_as_me($sender_mail, $reciever_mail, $sender_username, $reciever_username)
{
    global $email;
    // $fetch_username_query = "select * from mail_list where mail_no='';";
    if ($sender_mail == $email) {
        echo "me, " . $reciever_username;
    } elseif ($reciever_mail == $email) {
        echo $sender_username . ", me";
    } else {
        echo $sender_username . "," . $reciever_username;
    }
}

//this function does the updation in db according to each button
function email_options($starred_mail, $checkbox_value, $user_details_result)
{
    global $conn, $starred_status, $archived_status, $trash_status;
    //stars a mail in inbox
    if (isset($_POST['star'])) {
        // print_r($starred_mail);
        //$starred_status = "reciever_starred_status" if user is reciever of that mail
        //$starred_status = "sender_starred_status" if user is sender of that mail
        foreach ($starred_mail as $mail_number) {
            $star_query = "update mail_list set $starred_status ='{$user_details_result['username']}' where mail_no='$mail_number';";
            $star_output = $conn->query($star_query);
        }
    } //archives a mail
    elseif (isset($_POST['archive'])) {
        //$archived_status = "reciever_archived_status" if user is reciever of that mail
        //$archived_status = "sender_archived_status" if user is sender of that mail
        foreach ($checkbox_value as $mail_number) {
            $archive_query = "update mail_list set $archived_status ='{$user_details_result['username']}' where mail_no='$mail_number';";
            $archive_output = $conn->query($archive_query);
        }
    }
    //moves a mail to trash
    elseif (isset($_POST['trash'])) {
        foreach ($checkbox_value as $mail_number) {
            //$trash_status = "reciever_trash_status" if user is reciever of that mail
            //$trash_status = "sender_trash_status" if user is sender of that mail
            $trash_query = "update mail_list set $trash_status ='{$user_details_result['username']}' where mail_no='$mail_number';";
            $trash_output = $conn->query($trash_query);
        }
    } //marks mail as read when in unread
    elseif (isset($_POST['mark_as_read'])) {
        foreach ($checkbox_value as $mail_number) {
            $mark_as_read_query = "update mail_list set inbox_status='read' where mail_no='$mail_number';";
            $mark_as_read_output = $conn->query($mark_as_read_query);
        }
    } //unarchives a mail
    elseif (isset($_POST['unarchive'])) {
        foreach ($checkbox_value as $mail_number) {
            //$archived_status = "reciever_archived_status" if user is reciever of that mail
            //$archived_status = "sender_archived_status" if user is sender of that mail
            $unarchive_query = "update mail_list set $archived_status =NULL where mail_no='$mail_number';";
            $unarchive_query_output = $conn->query($unarchive_query);
        }
    } //unstars a mail
    elseif (isset($_POST['unstar'])) {
        foreach ($starred_mail as $mail_number) {
            //$starred_status = "reciever_starred_status" if user is reciever of that mail
            //$starred_status = "sender_starred_status" if user is sender of that mail
            $unstar_query = "update mail_list set $starred_status =NULL where mail_no='$mail_number';";
            $unstar_output = $conn->query($unstar_query);
        }
    } //restores a mail from trash
    elseif (isset($_POST['restore'])) {
        foreach ($checkbox_value as $mail_number) {
            //$trash_status = "reciever_trash_status" if user is reciever of that mail
            //$trash_status = "sender_trash_status" if user is sender of that mail
            $restore_query = "update mail_list set $trash_status =NULL where mail_no='$mail_number'";
            $restore_output = $conn->query($restore_query);
        }
    } //deletes a mail from trash
    elseif (isset($_POST['delete'])) {
        foreach ($checkbox_value as $mail_number) {
            //$trash_status = "reciever_archived_status" if user is reciever of that mail
            //$trash_status = "sender_archived_status" if user is sender of that mail
            $delete_query = "update mail_list set " . $trash_status . "='delete' where mail_no='$mail_number'";
            $delete_output = $conn->query($delete_query);
        }
    }
}
//This function displays the data fetched for each options
function pagination($page, $query, $result, $page_number)
{
    global $email, $conn;
    $results_per_page = 10;
    $number_of_result = $result->num_rows;
    $number_of_page = ceil($number_of_result / $results_per_page);
    if (!isset($_GET['page_no'])) {
        $page_no = 1;
    } else {
        $page_no = $_GET['page_no'];
    }
    $page_first_result = ($page_no - 1) * $results_per_page;
    $pagination_query =  "$query ORDER BY date_of_sending DESC LIMIT " . $page_first_result . ',' . $results_per_page;
    $pagination_output = $conn->query($pagination_query);
    if ($pagination_output->num_rows > 0) {
        while ($pagination_result = $pagination_output->fetch_assoc()) {
            if ($pagination_result['sender_email'] == $email) {
                $starred_status = $pagination_result['sender_starred_status'];
            } elseif ($pagination_result['reciever_email'] == $email) {
                $starred_status = $pagination_result['reciever_starred_status'];
            } elseif ($pagination_result['cc'] == $email) {
                $starred_status = $pagination_result['cc_starred_status'];
            } elseif ($pagination_result['bcc'] == $email) {
                $starred_status = $pagination_result['bcc_starred_status'];
            }
            if ($pagination_result['cc'] == $email && $pagination_result['cc_inbox_status'] == "unread") {
                $color = "color:black;";
            } elseif ($pagination_result['bcc'] == $email && $pagination_result['bcc_inbox_status'] == "unread") {
                $color = "color:black;";
            } elseif ($pagination_result['inbox_status'] == "read") {
                $color = "color:grey;";
            } elseif ($pagination_result['reciever_email'] == $email && $pagination_result['inbox_status'] == "unread") {
                $color = "color:black;";
            } elseif (($pagination_result['reciever_email'] == $email) && ($pagination_result['sender_email'] == $email) && $pagination_result['inbox_status'] == "unread") {
                $color = "color:black;";
            } elseif ($pagination_result['sender_email'] == $email) {
                $color = "color:grey;";
            } else {
                $color = "color:grey;";
            }
?>
            <tr class="mail-line" style="<?= $color ?>">
                <td style="width:10%;margin-left:20px;">
                    <input type="checkbox" name="check[]" value="<?= $pagination_result['mail_no'] ?>" class="archive" form="checkbox_form">
                    <input type="checkbox" name="star-check[]" value="<?= $pagination_result['mail_no'] ?>" <?= $starred_status == NULL ? 'class="star"' : 'class="stared"' ?> form="checkbox_form">
                </td>
                <td style="width:30%;">
                    <a href="email.php?page=Email&option=<?= $page ?>&page_no=<?= $page_number ?>&token=<?= urlencode(base64_encode($pagination_result['token_id']))  ?>&mailno=<?= $pagination_result['mail_no'] ?>" style="<?= $color ?>">
                        <?= usermail_as_me($pagination_result['sender_email'], $pagination_result['reciever_email'], $pagination_result['sender_name'], $pagination_result['reciever_name']) ?>
                    </a>
                </td>
                <td style="width:50%;">
                    <a href="email.php?page=Email&option=<?= $page ?>&page_no=<?= $page_number ?>&token=<?= urlencode(base64_encode($pagination_result['token_id'])) ?>&mailno=<?= $pagination_result['mail_no'] ?>" style="<?= $color ?>">
                        <?php
                        if (!empty($pagination_result['attachment_name'])) {
                            echo '<img src="../icons/attachment.png" alt="clip" class="attachment-logo">';
                        }
                        echo substr($pagination_result['subject'], 0, 25);
                        if (strlen($pagination_result['subject']) > 25) {
                            echo "...";
                        }
                        ?>
                    </a>
                </td>

                <td style="width:10%;">
                    <a href="email.php?page=Email&option=<?= $page ?>&page_no=<?= $page_number ?>&token=<?= urlencode(base64_encode($pagination_result['token_id'])) ?>&mailno=<?= $pagination_result['mail_no'] ?>" style="margin-right:20px;<?= $color ?>">
                        <?= dateconvertion($pagination_result['date_of_sending']) ?>
                    </a>
                </td>
            </tr>
        <?php
        }
    }
    //page numbers for records when records count exceed 10
    echo '<div class="result-page-numbers"><div class="page_numbers">';
    echo '<button style="width:40px"><a href = "email.php?page=Email&option=' . $page . '&page_no=1"><<</a></button>';
    for ($page_no = 1; $page_no <= $number_of_page; $page_no++) {
        echo '<button><a href = "email.php?page=Email&option=' . $page . '&page_no=' . $page_no . '"> ' .  $page_no . ' </a></button>';
    }
    echo '<button style="width:40px;margin-left:20px"><a href = "email.php?page=Email&option=' . $page . '&page_no=' . ($page_no - 1) . '">>></a></button>';
    echo "</div><br><br>";
    if (isset($_GET['option'])) {
        echo '<div class="results">Total mails(' . $result->num_rows . ')</div>';
    }
    echo '</div>';
}

function search_pagination($query, $result, $page_number, $search_content)
{
    global $email, $conn;
    // require "config.php";
    $results_per_page = 10;
    $number_of_result = $result->num_rows;
    $number_of_page = ceil($number_of_result / $results_per_page);
    if (!isset($_GET['page_no'])) {
        $page_no = 1;
    } else {
        $page_no = $_GET['page_no'];
    }
    $page_first_result = ($page_no - 1) * $results_per_page;
    $pagination_query =  "$query ORDER BY date_of_sending DESC LIMIT " . $page_first_result . ',' . $results_per_page;
    $pagination_output = $conn->query($pagination_query);
    if ($pagination_output->num_rows > 0) {
        while ($pagination_result = $pagination_output->fetch_assoc()) {
            if ($pagination_result['sender_email'] == $email) {
                $starred_status = $pagination_result['sender_starred_status'];
            } elseif ($pagination_result['reciever_email'] == $email) {
                $starred_status = $pagination_result['reciever_starred_status'];
            } elseif ($pagination_result['cc'] == $email) {
                $starred_status = $pagination_result['cc_starred_status'];
            } elseif ($pagination_result['bcc'] == $email) {
                $starred_status = $pagination_result['bcc_starred_status'];
            }
            if ($pagination_result['cc'] == $email && $pagination_result['cc_inbox_status'] == "unread") {
                $color = "color:black;";
            } elseif ($pagination_result['bcc'] == $email && $pagination_result['bcc_inbox_status'] == "unread") {
                $color = "color:black;";
            } elseif ($pagination_result['inbox_status'] == "read") {
                $color = "color:grey;";
            } elseif ($pagination_result['reciever_email'] == $email && $pagination_result['inbox_status'] == "unread") {
                $color = "color:black;";
            } elseif (($pagination_result['reciever_email'] == $email) && ($pagination_result['sender_email'] == $email) && $pagination_result['inbox_status'] == "unread") {
                $color = "color:black;";
            } elseif ($pagination_result['sender_email'] == $email) {
                $color = "color:grey;";
            } else {
                $color = "color:grey;";
            }
        ?>
            <tr class="mail-line" style="<?= $color ?>">
                <td style="width:10%;margin-left:20px;">
                    <input type="checkbox" name="check[]" value="<?= $pagination_result['mail_no'] ?>" class="archive" form="checkbox_form">
                    <input type="checkbox" name="star-check[]" value="<?= $pagination_result['mail_no'] ?>" <?= $starred_status == NULL ? 'class="star"' : 'class="stared"' ?> form="checkbox_form">
                </td>
                <td style="width:30%;">
                    <a href="email.php?page=Email&page_no=<?= $page_number ?>&token=<?= urlencode(base64_encode($pagination_result['token_id']))  ?>&mailno=<?= $pagination_result['mail_no'] ?>" style="<?= $color ?>">
                        <?= usermail_as_me($pagination_result['sender_email'], $pagination_result['reciever_email'], $pagination_result['sender_name'], $pagination_result['reciever_name']) ?>
                    </a>
                </td>
                <td style="width:50%;">
                    <a href="email.php?page=Email&page_no=<?= $page_number ?>&token=<?= urlencode(base64_encode($pagination_result['token_id'])) ?>&mailno=<?= $pagination_result['mail_no'] ?>" style="<?= $color ?>">
                        <?php
                        if (!empty($pagination_result['attachment_name'])) {
                            echo '<img src="../icons/attachment.png" alt="clip" class="attachment-logo">';
                        }
                        echo $pagination_result['subject']; ?>
                    </a>
                </td>

                <td style="width:10%;">
                    <a href="email.php?page=Email&page_no=<?= $page_number ?>&token=<?= urlencode(base64_encode($pagination_result['token_id'])) ?>&mailno=<?= $pagination_result['mail_no'] ?>" style="margin-right:20px;<?= $color ?>">
                        <?= dateconvertion($pagination_result['date_of_sending']) ?>
                    </a>
                </td>
            </tr>
<?php
        }
    }
    //page numbers for records when records count exceed 10
    echo '<div class="result-page-numbers"><div class="page_numbers">';
    echo '<button style="width:40px"><a href = "email.php?page=Email&search=' . $search_content . '&page_no=1"><<</a></button>';
    for ($page_no = 1; $page_no <= $number_of_page; $page_no++) {
        echo '<button><a href = "email.php?page=Email&search=' . $search_content . '&page_no=' . $page_no . '"> ' .  $page_no . ' </a></button>';
    }
    echo '<button style="width:40px;margin-left:20px"><a href = "email.php?page=Email&search=' . $search_content . '&page_no=' . ($page_no - 1) . '">>>  </a></button>';
    echo "</div><br><br>";
    if (isset($_GET['search'])) {
        echo '<div class="results">Search results(' . $result->num_rows . ')</div>';
    }
    echo '</div>';
}

function mark_as_read($mail_no, $email, $result)
{
    global $conn;
    if ($result['reciever_email'] == $email) {
        $mark_as_read = "update mail_list set inbox_status='read' where mail_no='$mail_no'";
        $conn->query($mark_as_read);
    } elseif ($result['cc'] == $email) {
        $mark_as_read = "update mail_list set cc_inbox_status='read' where mail_no='$mail_no'";
        $conn->query($mark_as_read);
    } elseif ($result['bcc'] == $email) {
        $mark_as_read = "update mail_list set bcc_inbox_status='read' where mail_no='$mail_no'";
        $conn->query($mark_as_read);
    }
}
function getIPAddress()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
