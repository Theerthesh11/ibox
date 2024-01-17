<?php
//author:Theertheshwaran T
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "../config.php";
require_once "../Admin/admin_functions.php";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : header("user_login.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inboxflow</title>
    <link rel="stylesheet" href="../css/admin_registeration_page.css">
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
</head>

<body>
    <div class="container">
        <div class="fraction_one">
            <div style="display: flex;justify-content: center;margin: 23%;">
                <img src="../Images/logo.png" alt="">
            </div>
        </div>
        <div class="fraction_two">
            <div class="fraction_two_container">
                <h2 style="text-align: center;">PASSWORD RESET</h2>
                <div class="form">
                    <!-- <div class="elfsight-app-4b41d601-2ed8-4d61-bc2d-d0a89568f569" data-elfsight-app-lazy></div> -->
                    <form action="../user/token_verify.php" method="post">
                        <label for="token">Token</label><br>
                        <input type="password" name="token" id="token"><br><br>
                        <div class="form-buttons" style="text-align: center;">
                            <input type="submit" name="verify" value="VERIFY">
                            <input type="reset" value="CLEAR" class="clear-btn ">
                            <!-- <h6>New user? click here to <a href="registeration_login_page.php">register</a></h6> -->
                        </div>
                    </form>
                    <?php
                    if (isset($_POST['verify'])) {
                        $get_query = "select * from user_details where username='{$username}';";
                        $get_query_output = $conn->query($get_query);
                        if ($get_query_output->num_rows > 0) {
                            $result = $get_query_output->fetch_assoc();
                            if (!empty($_POST['token']) && preg_match("/^[a-fA-F0-9]*$/", $_POST['token'])) {
                                $token_id = $_POST['token'];
                                if ($token_id === bin2hex($result['token_id'])) {
                                    $forgot_attempt_update = "update user_details set forgot_pass_attempt='1',token_attempt='0' where username='$username';";
                                    $conn->query($forgot_attempt_update);
                                    header("location:password_change.php");
                                } else {
                                    $disable_user_query = "update user_details set password_attempt='0',forgot_pass_attempt='0',token_attempt='0',user_status='disable' where username='$username';";
                                    $conn->query($disable_user_query);
                                    header("location:user_login.php");
                                }
                            } else {
                                $disable_user_query = "update user_details set password_attempt='0',forgot_pass_attempt='0',token_attempt='0',user_status='disable' where username='$username';";
                                $conn->query($disable_user_query);
                                header("location:user_login.php");
                            }
                        } else {
                            header("location:user_login.php");
                        }
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>