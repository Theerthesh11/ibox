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
            <div class="heading">
                <h4>Complaint Status</h4>
            </div>
            <form action="../user/user_complaint_status.php" method="post">
                <div class="text-boxes">
                    <div>
                        <label for="complaint_no">Complaint No.</label>
                        <input type="text" name="complaint_no" id="complaint_no" value="<?= !empty($_POST['complaint_no']) ? $_POST['complaint_no'] : ""; ?>" required>
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" value="<?= !empty($_POST['username']) ? $_POST['username'] : ""; ?>" required>
                    </div>
                </div>
                <div class="form-btns">
                    <input type="submit" name="check" value="CHECK">
                    <input type="submit" name="clear_btn" value="CLEAR">
                    <!-- <a href="user_login.php">Sign in</a> -->
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
                                    <table class="query">
                                        <tr>
                                            <th>Your Complaint</th>
                                            <th>Support reply</th>
                                        </tr>
                                        <tr>
                                            <td><textarea name="user_complaint" readonly><?= $complaint['user_complaint'] ?></textarea></td>
                                            <td><textarea name="support_reply" readonly><?= $complaint['support_reply'] ?></textarea></td>
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
                                                <td><textarea name="support_reply_1" readonly><?= $complaint['support_reply_1'] ?></textarea></td>
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
                                                <td><textarea name="support_reply_2" readonly><?= $complaint['support_reply_2'] ?></textarea></td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                    <div style="text-align: center;">
                                        <input type="submit" name="send" value="Submit">
                                    </div>

            </form>
        </div>
<?php
                            }
                        }
                    }
                }
                if (isset($_POST['send'])) {
                    $username = !empty($_POST['username']) ? sanitizing($_POST['username']) : NULL;
                    $complaint_no = !empty($_POST['complaint_no']) ? sanitizing($_POST['complaint_no']) : NULL;
                    $user_reply_1 = !empty($_POST['user_reply_1']) ? sanitizing($_POST['user_reply_1']) : NULL;
                    $user_reply_2 = !empty($_POST['user_reply_2']) ? sanitizing($_POST['user_reply_2']) : NULL;
                    $complaint_update_query = "update user_queries set user_reply_1='$user_reply_1',user_reply_2='$user_reply_2' where username='$username' and complaint_no='$complaint_no';";
                    $conn->query($complaint_update_query);
                }
?>
    </div>
    </div>
</body>

</html>