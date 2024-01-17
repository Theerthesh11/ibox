<?php
//error reporting
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
//including required files
require_once '../config.php';
require_once "../user/email_function.php";
// echo getIPAddress();
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 10)) {
    $logout_query="update user_login_log set logout_time=current_timestamp where login_id='{$_SESSION['login_id']}';";
    $conn->query($logout_query);
    unset($_SESSION['token_id']);
}
//checks for token_id in session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : header("location:user_login.php");
$token_id = isset($_SESSION['token_id']) ?  $_SESSION['token_id'] : header("location:user_login.php");
$login_id = isset($_SESSION['login_id']) ?  $_SESSION['login_id'] : header("location:user_login.php");
//Getting email for that username from database
$user_details_query = "select * from user_details where username='$username';";
$user_details_output = $conn->query($user_details_query);
if ($user_details_output->num_rows > 0) {
    $user_details_result = $user_details_output->fetch_assoc();
    if ($login_id != $user_details_result['login_id']) {
        unset($_SESSION['token_id']);
        unset($_SESSION['login_id']);
    }
    if ($user_details_result['token_id'] == $token_id) {
        $email = $user_details_result['email'];
    } else {
        header("location:user_login.php");
    }
}
if ($user_details_result['user_status'] == "disable") {
    $token_id = "";
    unset($_SESSION['token_id']);
}
//checking for current page,options in that page and page_no
$page = isset($_GET['page']) ? $_GET['page'] : 'Email';
$option = empty($_GET['option']) ? '' : $_GET['option'];
$page_no = empty($_GET['page_no']) ? '1' : $_GET['page_no'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- <meta http-equiv="refresh" content="1"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inboxflow</title>
    <link rel="stylesheet" href="../css/email.css">
</head>

<body>
    <div class="overall-container">
        <!--creating navigation panel-->
        <div class="navigation-bar">
            <div class="top-navigation-bar-logo">
                <!--site logo-->
                <div class="logo">
                    <img src="../images/logo.png" alt="sitelogo">
                </div>
            </div>
            <!--displays current page and option -->
            <div class="top-navigation-content">
                <div class="path">
                    <a href="email.php?page=<?= $page ?>" style="color:rgb(114, 98, 255);">
                        <?= $page ?>
                    </a>
                    <?php
                    if (!empty($option)) {
                    ?>
                        <!--displays option in current page -->
                        <a href="email.php?page=<?= $page ?>&option=<?= $option ?>" style="color:rgb(114, 98, 255);">
                            <?= " | " .  $option ?>
                        </a>
                    <?php
                    }
                    ?>
                </div>
                <!--adding file that displays profile picture-->
                <div class=" profile">
                    <?php require_once "profile_picture.php" ?>
                </div>
            </div>
        </div>
        <!--creating vertical navigation bar -->
        <div class="container">
            <div class="vertical-navigation-bar">
                <ul>
                    <!--pages in vertical navigation bar -->
                    <br><br>
                    <!--page value sent to url when clicked-->
                    <li><a href="?page=Dashboard"><button <?= isset($_GET['page']) && $_GET['page'] === 'Dashboard' ? '" class="active"' : '' ?>><!--<i class="fa fa-bolt"></i>--> Dashboard</button></a></li>
                    <li><a href="?page=Email&option=Inbox"><button <?= (isset($_GET['page']) && $_GET['page'] == 'Email') || $page === 'Email' ? '" class="active"' : '' ?>><!--<i class="fa fa-envelope-open-o" aria-hidden="true"></i>--> Email</button></a></li>
                    <li><a href="?page=Chat"><button <?= isset($_GET['page']) && $_GET['page'] === 'Chat' ? '" class="active"' : '' ?>><!--<i class="fa fa-chat"></i>-->Chat</button></a></li>
                    <li><a href="?page=User"><button <?= isset($_GET['page']) && $_GET['page'] === 'User' ? '" class="active"' : '' ?>><!--<i class="fa fa-user"></i>-->User</button></a></li>
                    <li><a href="?page=Calender"><button <?= isset($_GET['page']) && $_GET['page'] === 'Calender' ? '" class="active"' : '' ?>><!--<i class="fa fa-calendar"></i>--> Calender</button></a></li>
                </ul>
            </div>
            <?php
            //php condition for each page using switch statement when each link is clicked
            switch ($page) {
                    //when the active page is dashboard
                case 'Dashboard':
                    $_SESSION['last_activity'] = time();
            ?>
                    <div class="dashboard-container">
                        <div class="dashboard-content">
                            <div class="dashboard-counts">
                                <!--total mails sent by the user-->
                                <div class="dashboard-count1">
                                    <h3>Total mails sent</h3>
                                    <?= total_mail("sender_email", "and mail_status='sent')") ?>
                                </div>
                                <!--total mails recieved by the user-->
                                <div class="dashboard-count2">
                                    <h3>Total mails recieved</h3>
                                    <?= total_mail("reciever_email", "and mail_status='sent')") ?>
                                </div>
                                <!--user as cc and bcc mails-->
                                <div class="dashboard-count3">
                                    <h3>CC & BCC mails</h3>
                                    <?= total_mail("cc", "or bcc='$email') and mail_status='sent'") ?>
                                </div>
                                <!--first login date by the user-->
                                <div class="dashboard-count4">
                                    <h3>First login</h3>
                                    <?= dateconvertion($user_details_result['created_on']) ?>
                                </div>
                                <!--last login by the user-->
                                <div class="dashboard-count5">
                                    <h3>Last login</h3>
                                    <?= dateconvertion($user_details_result['last_login']) ?>
                                </div>
                                <!--last profile update by the user-->
                                <div class="dashboard-count5">
                                    <h3>Last profile update</h3>
                                    <?= dateconvertion($user_details_result['updated_on']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    break;
                    //email page code
                case 'Email':
                ?>
                    <div class="email-container">
                        <div class="email-navigation-bar">
                            <!--options in email page-->
                            <!--option page is open -->
                            <div class="compose-btn">
                                <a href="email.php?page=Email&option=Compose"><button><!--<i class="fa fa-pencil"></i>--> Compose</button></a>
                            </div>
                            <div class="email-options">
                                <!--inbox option and unread count-->
                                <div>
                                    <a href="email.php?page=Email&option=Inbox"><button <?= isset($_GET['option']) && $_GET['option'] === 'Inbox' ? 'class="option_active"' : '' ?>> Inbox</button></a>
                                </div>
                                <div class="email-count">
                                    <?= total_mail("reciever_email", "and mail_status='sent') and (reciever_archived_status='no' and reciever_trash_status='no') and (spam='no' and inbox_status='unread');") ?>
                                </div>
                                <!--unread option and count-->
                                <div><a href="email.php?page=Email&option=Unread"><button <?= isset($_GET['option']) && $_GET['option'] === 'Unread' ? 'class="option_active"' : '' ?>>Unread</button></a></div>
                                <div class="email-count">
                                    <?= total_mail("reciever_email", " and mail_status='sent') and (reciever_archived_status='no' and reciever_trash_status='no' )and (spam='no' and inbox_status='unread')") ?>
                                </div>
                                <!--sent option-->
                                <div><a href="email.php?page=Email&option=Sent"><button <?= isset($_GET['option']) && $_GET['option'] === 'Sent' ? 'class="option_active"' : '' ?>>Sent</button></a></div>
                                <div class="email-count">
                                </div>
                                <!--draft option and its count-->
                                <div><a href="email.php?page=Email&option=Draft"><button <?= isset($_GET['option']) && $_GET['option'] === 'Draft' ? 'class="option_active"' : '' ?>>Draft</button></a></div>
                                <div class="email-count">
                                    <?= total_mail("sender_email", "and mail_status='draft')") ?>
                                </div>
                                <!--starred option and its unread count-->
                                <div><a href="email.php?page=Email&option=Starred"><button <?= isset($_GET['option']) && $_GET['option'] === 'Starred' ? 'class="option_active"' : '' ?>>Starred</button></a></div>
                                <div class="email-count">
                                    <?= total_mail("sender_email", "or reciever_email='{$email}') and (reciever_starred_status='{$user_details_result['username']}' or sender_starred_status='{$user_details_result['username']}') and (reciever_trash_status='no' or sender_trash_status='no') and inbox_status='unread'") ?>
                                </div>
                                <!--spam option and its count-->
                                <div><a href="email.php?page=Email&option=Spam"><button <?= isset($_GET['option']) && $_GET['option'] === 'Spam' ? 'class="option_active"' : '' ?>>Spam</button></a></div>
                                <div class="email-count">
                                    <?= total_mail("reciever_email", "and mail_status='sent') and (reciever_archived_status='no' and reciever_trash_status='no') and spam='yes';") ?>
                                </div>
                                <!--archived option and its unread count-->
                                <div><a href="email.php?page=Email&option=Archived"><button <?= isset($_GET['option']) && $_GET['option'] === 'Archived' ? 'class="option_active"' : '' ?>>Archive</button></a></div>
                                <div class="email-count">
                                    <?= total_mail("sender_email", "or reciever_email='{$email}') and (reciever_archived_status='{$user_details_result['username']}' or sender_archived_status='{$user_details_result['username']}') and (reciever_trash_status='no' and sender_trash_status='no') and inbox_status='unread';") ?>
                                </div>
                                <!--trash option and its unread count-->
                                <div><a href="email.php?page=Email&option=Trash"><button <?= isset($_GET['option']) && $_GET['option'] === 'Trash' ? 'class="option_active"' : '' ?>>Trash</button></a></div>
                                <div class="email-count">
                                    <?= trash_mail("mail_no", "reciever_email", "sender_email", "(mail_status='trash' and inbox_status='unread');") ?>
                                </div>
                            </div>
                            <hr>
                            </form><br>
                        </div>
                        <?php
                        ?>
                        <div class="mail-list">
                            <?php
                            //compose option content
                            //reply button also navigates here
                            if (isset($_GET['option']) && $_GET['option'] == "Compose" || isset($_POST['reply'])) {
                                if (isset($_POST['reply'])) {
                                    $reply_addition = "Re-";
                                } else {
                                    $reply_addition = "";
                                }
                            ?>
                                <div class="email_form">
                                    <form action="email.php?page=Email" enctype="multipart/form-data" method="post">
                                        <div class="email-buttons">
                                            <div>
                                                <input type="submit" name="send" value="Send mail">
                                                <input type="reset" value="Clear">
                                            </div>
                                            <div>
                                                <input type="file" name="file" id="attachment">
                                                <div style="margin:12px">
                                                    <label for="attachment" style="color:rgb(114, 98, 255);">Attach</label>
                                                </div>
                                            </div>
                                        </div>
                                        <label for="mail">TO:</label><br>
                                        <input type="text" id="mail" name="mail" value="<?php
                                                                                        //reply brings the appropriate reply mail to this text box
                                                                                        if ($_POST['sender_email'] == $email && !($_GET['option'] == "Sent")) {
                                                                                            $text_box_value = $_POST['mail'];
                                                                                        } elseif ($_POST['mail'] == $email) {
                                                                                            $text_box_value = $_POST['sender_email'];
                                                                                        } else {
                                                                                            $text_box_value = "";
                                                                                        }
                                                                                        echo $text_box_value;
                                                                                        ?>" required><br><br>
                                        <label for="cc" style="margin-right:16px;">CC:</label>
                                        <input type="text" id="cc" name="cc"><br><br>
                                        <label for="bcc" style="margin-right:16px;">BCC:</label>
                                        <input type="text" id="bcc" name="bcc"><br><br>
                                        <label for="subject">SUBJECT:</label>
                                        <input type="text" id="subject" name="subject" value="<?= $reply_addition ?>" required><br><br><br>
                                        <textarea name="notes" placeholder="Type here..."></textarea><br><br>
                                    </form>
                                </div>
                            <?php
                            } else {
                            ?><!--mail-list-options for other options in email page except compose-->
                                <div class="mail-list-options">
                                    <div>
                                        <form action="../user/email.php?page=Email" method="get">
                                            <input type="search" name="search" placeholder="search mail" value="<?= isset($_GET['search']) ? $_GET['search'] : "" ?>">
                                            <input type="submit" name="search-btn" value="Search">
                                        </form>
                                    </div>
                                    <div>
                                        <form action="email.php?page=Email&option=<?= $option ?>&page_no=<?= $page_no ?>" method="post" id="checkbox_form">
                                            <input type="submit" name="<?= name_setting('Starred', 'unstar', 'star') ?>" value="<?= name_setting('Starred', 'Unstar', 'Star') ?>">
                                            <input type="submit" name="<?= name_setting('Archived', 'unarchive', 'archive') ?>" value=" <?= name_setting('Archived', 'Unarchive', 'Archive') ?>">
                                            <input type="submit" name="<?= name_setting('Trash', 'restore', 'trash') ?>" value="<?= name_setting('Trash', 'Restore', 'Trash') ?>">
                                            <?php require_once "../user/delete_permission.php" ?>
                                        </form>
                                    </div><br><br>
                                </div>
                            <?php
                                require_once "../user/email_options.php";
                            }
                            //email_options.php contains the data to be displayed when each options is clicked
                            ?>
                            <?php
                            //gets the encoded token from url and stres it after decoding
                            $user_token_id = isset($_GET['token']) ? urldecode(base64_decode($_GET['token'])) : "";
                            $mail_no = isset($_GET['mailno']) ? $_GET['mailno'] : ""; //stores mail number
                            //The below part makes the mail read when opened
                            if (!empty($token_id) && !empty($mail_no)) {
                                if ($_GET['option'] == "Draft") {
                                    $action_path = 'email.php?page=Email&option=' . $option . '&token=' . urlencode(base64_encode($user_token_id)) . '&mailno=' . $mail_no;
                                } else {
                                    $action_path = "email.php?page=Email&option=Compose";
                                }
                                $select_query = "select * from mail_list where mail_no ='$mail_no' and token_id='$user_token_id';";
                                $select_query_output = $conn->query($select_query);
                                $display_result = $select_query_output->fetch_assoc();
                                mark_as_read($mail_no, $email, $display_result);
                            ?>

                                <div class="mail-display">
                                    <!--form that shows the mail sent or recieved briefly with readonly mode-->
                                    <div class="mail-display-options">
                                        <div>
                                            <a href="email.php?page=Email&option=<?= $option ?>&page_no=<?= $page_no ?>">Back</a>
                                        </div>
                                        <div>
                                            <form action="<?= $action_path ?>" method="post" enctype="multipart/form-data">
                                                <?php
                                                if ($_GET['option'] == "Draft") {
                                                    $action_name = "send_mail";
                                                    $action_value = "Send mail";
                                                    $readonly = "";
                                                    echo '<div class="email-buttons">
                                                    <div><input type="file" name="file" id="attachment">
                                                        <div style="margin:10px"><label for="attachment" style="color:rgb(114, 98, 255);">Attach</label>
                                                        </div>
                                                    </div>
                                                    </div>';
                                                } else {
                                                    $action_name = "reply";
                                                    $action_value = "Reply";
                                                    $readonly = "readonly";
                                                }
                                                ?>
                                                <div>
                                                    <input type="submit" name="<?= $action_name ?>" value="<?= $action_value ?>">
                                                </div>
                                        </div>
                                    </div>
                                    <label>From:</label><br>
                                    <input type="text" name="sender_email" id="sender_email" value="<?= $display_result['sender_email'] ?>" readonly>
                                    <br><br>
                                    <label>To:</label><br>
                                    <input type="text" name="mail" id="email" value="<?= $display_result['reciever_email'] ?>" <?= $readonly ?>><br><br>
                                    <label>Subject:</label><br>
                                    <input type="text" name="subject" id="subject" value="<?= $display_result['subject'] ?>" <?= $readonly ?>><br><br>
                                    <?php
                                    //displays cc and bcc of a mail only if exist
                                    if ($_GET['option'] == "Draft") {
                                        echo '<label>Cc:</label>
                                            <input type="text" name="Cc" id="cc" value=""><br><br>
                                            <label>Bcc:</label>
                                            <input type="text" name="Bcc" id="bcc" value=""><br><br>';
                                    } elseif (empty($display_result['cc']) && empty($display_result['bcc'])) {
                                    } else {
                                    ?>
                                        <label>Cc:</label>
                                        <input type="text" name="Cc" id="cc" value="<?= $display_result['cc'] ?>" <?= $readonly ?>><br><br>
                                        <label>Bcc:</label>
                                        <input type="text" name="Bcc" id="bcc" value="<?= $display_result['bcc'] ?>" <?= $readonly ?>><br><br>
                                    <?php
                                    }
                                    ?>
                                    <br>
                                    <textarea name="notes" <?= $readonly ?>><?= $display_result['notes'] ?></textarea><br><br>
                                    </form>
                                    <?php
                                    //displays the attachment if there is and attachment path in db
                                    if (!empty($display_result['attachment_path'])) {
                                    ?>
                                        <div class="attachment">
                                            <a href="<?= $display_result['attachment_path'] ?>" download>
                                                <?php
                                                //icons to differentiate different format of files
                                                require_once "../user/icon_display.php";
                                                echo $display_result['attachment_name']
                                                ?> </a>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                        </div>

                        </table>
                    <?php
                            }
                            break;
                        case 'Chat':
                            //chat page
                    ?>
                    <div class="chat-container">
                        <div class="chat-content">
                            <?php
                            echo "<h4>Chat will be available soon</h4>";
                            ?>
                        </div>
                    </div>
                <?php
                            break;

                        case 'User':
                            $_SESSION['last_activity'] = time();
                            //user profile page
                            require_once 'email_options.php';
                ?>
                    <div class="profile-container">
                        <div class="profile_picture">
                            <div>
                                <h3>Profile</h3>
                            </div>
                            <!--profile picture display-->
                            <?php require "profile_picture.php" ?>
                        </div>
                        <?php
                            //displays the details in readonly before clicking edit button
                            if (!isset($_POST['edit'])) {
                        ?>
                            <div class="user_details">
                                <form action="email.php?page=User" enctype="multipart/form-data" method="post">
                                    <input type="submit" name="edit" value="Edit">
                                    <input type="submit" name="logout" value="Log out"><br><br>
                                    <label for="username">Username</label>
                                    <input type="text" id="username" name="user_name" value="<?= $user_details_result['username'] ?>" readonly><br><br>
                                    <label for="name">Name</label>
                                    <input type="text" id="name" name="name" value="<?= $user_details_result['name'] ?>" readonly><br><br>
                                    <label for="dob">Date of birth</label>
                                    <input type="text" id="dob" name="dob" value="<?= $user_details_result['date_of_birth'] ?>" readonly><br><br>
                                    <label for="email">Email</label>
                                    <input type="text" id="email" name="email" value="<?= $user_details_result['email'] ?>" readonly><br><br>
                                    <label for="cell_number">Mobile number</label>
                                    <input type="text" id="cell_number" name="cell_number" value="<?= $user_details_result['phone_no'] ?>" readonly><br><br>
                                </form>
                            </div>
                        <?php
                            }
                            //displays the value to be edited after edit is clicked
                            if (isset($_POST['edit'])) {
                        ?>
                            <div class="user_details">
                                <form action="email.php?page=User" enctype="multipart/form-data" method="post">
                                    <input type="file" name="file" id="fileInput" style="display: none;">
                                    <label for="fileInput" style="color:rgb(114, 98, 255)">Update profile picture</label>
                                    <input type="submit" name="save" value="Save"><br><br>
                                    <label for="name">Name</label>
                                    <input type="text" id="name" name="name" value="<?= $user_details_result['name'] ?>"><br><br>
                                    <label for="dob">Date of birth</label>
                                    <input type="date" id="dob" name="dob" value="<?= $user_details_result['date_of_birth'] ?>"><br><br>
                                    <label for="cell_number">Mobile number</label>
                                    <input type="text" id="cell_number" name="cell_number" value="<?= $user_details_result['phone_no'] ?>"><br><br>
                                </form>
                            </div>
                    </div>
                <?php
                            }
                            break;
                        case 'Calender':
                            //calender page
                ?>
                <div class="dashboard-container">
                    <div class="dashboard-content">
                        <h4>calender will be available soon</h4>
                    </div>
                </div>
        <?php
                            break;
                    }
        ?>
                    </div>
        </div>
    </div>
</body>

</html>