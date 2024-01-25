<?php
//author:Theertheshwaran T
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "../config.php";
require_once "../Admin/admin_functions.php";
$ip_address = getIPAddress();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inboxflow</title>
    <link rel="stylesheet" href="../css/admin_registeration_page.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="overall-container">
        <div class="form-container">
            <div class="navigation-anchor">
                <a href="../user/user_login.php">SIGN IN</a>
            </div>
            <div class="heading">
                <h4>Complaint Status</h4>
            </div>
            <form action="../user/user_complaint_status.php" method="post">
                <div class="text-boxes">
                    <div>
                        <label for="complaint_no">Complaint No.</label>
                        <input type="text" name="complaint_no" id="complaint_no" value="<?= !empty($_POST['complaint_no']) && !isset($_POST['clear_btn']) ? $_POST['complaint_no'] : ""; ?>" required>
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" value="<?= !empty($_POST['username']) && !isset($_POST['clear_btn']) ? $_POST['username'] : ""; ?>" required>
                    </div>
                </div>
                <div class="form-btns">
                    <input type="submit" name="check" value="CHECK">
                    <input type="submit" name="clear_btn" value="CLEAR">
                </div>
                <?php
                if (isset($_POST['check']) || isset($_POST['send'])) {
                    $username = !empty($_POST['username']) ? sanitizing($_POST['username']) : NULL;
                    $complaint_no = !empty($_POST['complaint_no']) ? sanitizing($_POST['complaint_no']) : NULL;
                    if (!is_null($username) && !is_null($complaint_no)) {
                        $get_complaint_query = "select * from user_queries where username='$username' and complaint_no='$complaint_no'";
                        $get_complaint_output = $conn->query($get_complaint_query);
                        if ($get_complaint_output->num_rows > 0) {
                            while ($complaint = $get_complaint_output->fetch_assoc()) {
                ?>
                                <div class="form-table">
                                    <h5 style="text-align: center;color:grey;">Note: You will only get 5 replies from the support team, APPEAL wisely</h5>
                                    <h5 style="color: green;text-align:center;">STATUS :<?=$complaint['status']?></h5>
                                    <table class="query">
                                        <tr>
                                            <th>Your Complaint</th>
                                            <th>Support reply</th>
                                        </tr>
                                        <tr>
                                            <td><textarea name="user_complaint" readonly><?= $complaint['user_complaint'] ?></textarea></td>
                                            <td><textarea name="support_reply" readonly><?= support_reply($username, $complaint['support_reply'] , $complaint['assigned_to'])?></textarea></td>
                                        </tr>
                                        <?php
                                        if (!empty($complaint['support_reply'])) {
                                            if (!empty($complaint['user_reply_1'])) {
                                                $readonly = "readonly";
                                            } else {
                                                $readonly = "";
                                            } ?>
                                            <tr>
                                                <td><textarea name="user_reply_1" <?= $readonly ?>><?= $complaint['user_reply_1'] ?></textarea></td>
                                                <td><textarea name="support_reply_1" readonly><?=support_reply($username, $complaint['support_reply_1'] , $complaint['assigned_to'])?></textarea></td>
                                            </tr>
                                        <?php
                                        }
                                        if (!empty($complaint['support_reply_1'])) {
                                            if (!empty($complaint['user_reply_2'])) {
                                                $readonly = "readonly";
                                            } else {
                                                $readonly = "";
                                            } ?>
                                            <tr>
                                                <td><textarea name="user_reply_2" <?= $readonly ?>><?= $complaint['user_reply_2'] ?></textarea></td>
                                                <td><textarea name="support_reply_2" readonly><?= support_reply($username, $complaint['support_reply_2'] , $complaint['assigned_to']) ?></textarea></td>
                                            </tr>
                                        <?php
                                        }
                                        if (!empty($complaint['support_reply_2'])) {
                                            if (!empty($complaint['user_reply_3'])) {
                                                $readonly = "readonly";
                                            } else {
                                                $readonly = "";
                                            } ?>
                                            <tr>
                                                <td><textarea name="user_reply_3" <?= $readonly ?>><?= $complaint['user_reply_3'] ?></textarea></td>
                                                <td><textarea name="support_reply_3" readonly><?= support_reply($username, $complaint['support_reply_3'] , $complaint['assigned_to']) ?></textarea></td>
                                            </tr>
                                        <?php
                                        }
                                        if (!empty($complaint['support_reply_3'])) {
                                            if (!empty($complaint['user_reply_4'])) {
                                                $readonly = "readonly";
                                            } else {
                                                $readonly = "";
                                            } ?>
                                            <tr>
                                                <td><textarea name="user_reply_4" <?= $readonly ?>><?= $complaint['user_reply_4'] ?></textarea></td>
                                                <td><textarea name="support_reply_4" readonly><?= support_reply($username, $complaint['support_reply_4'] , $complaint['assigned_to']) ?></textarea></td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                    <?php
                                    if ($complaint['support_reply_4'] == NULL) {
                                    ?>
                                        <div style="text-align: center;">
                                            <input type="submit" name="send" value="Submit">
                                        </div>
                                    <?php
                                    }
                                    ?>

            </form>
        </div>
<?php
                            }
                        }
                    }
                } else {
                    echo '<div style="display: flex;justify-content: center;margin: 10% 10% 10% 5%;">
                    <img src="../Images/logo.png" alt="">
                </div>';
                }
                if (isset($_POST['send'])) {
                    $username = !empty($_POST['username']) ? sanitizing($_POST['username']) : NULL;
                    $complaint_no = !empty($_POST['complaint_no']) ? sanitizing($_POST['complaint_no']) : NULL;
                    $user_reply_1 = !empty($_POST['user_reply_1']) ? sanitizing($_POST['user_reply_1']) : NULL;
                    $user_reply_2 = !empty($_POST['user_reply_2']) ? sanitizing($_POST['user_reply_2']) : NULL;
                    $user_reply_3 = !empty($_POST['user_reply_3']) ? sanitizing($_POST['user_reply_3']) : NULL;
                    $user_reply_4 = !empty($_POST['user_reply_4']) ? sanitizing($_POST['user_reply_4']) : NULL;
                    $complaint_update_query = "update user_queries set user_reply_1='$user_reply_1',user_reply_2='$user_reply_2',user_reply_3='$user_reply_3',user_reply_4='$user_reply_4' where username='$username' and complaint_no='$complaint_no';";
                    $conn->query($complaint_update_query);
                }
?>
    </div>
    </div>
</body>

</html>