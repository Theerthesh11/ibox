<?php
//author:Theertheshwaran T
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "../config.php";
require_once "../Admin/admin_functions.php";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : header("location:user_login.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inboxflow</title>
    <link rel="stylesheet" href="../css/admin_registeration_page.css">
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
                    <form action="password_change.php" method="post">
                        <label for="code">CODE</label><br>
                        <input type="text" name="code" id="code"><br><br>
                        <label for="password">Password</label><br>
                        <input type="text" name="password" id="password"><br><br>
                        <label for="confirm-password">Confirm-Password</label><br>
                        <input type="password" name="confirm_password" id="confirm-password"><br><br>
                        <div class="form-buttons" style="text-align: center;">
                            <input type="submit" name="set_password" value="SET PASSWORD">
                            <input type="reset" value="CLEAR" class="clear-btn">
                            <!-- <h6>New user? click here to <a href="registeration_login_page.php">register</a></h6> -->
                        </div>
                    </form>
                    <?php
                    if (isset($_POST['set_password'])) {
                        $get_query = "select * from user_details where username='$username';";
                        $get_query_output = $conn->query($get_query);
                        if ($get_query_output->num_rows > 0) {
                            $result = $get_query_output->fetch_assoc();
                            $temporary_code = !empty($_POST['code']) ? $_POST['code'] : '';
                            if ($temporary_code === $result['otp']) {
                                if (!empty($_POST['password']) && preg_match("/^[a-zA-Z0-9@_. ]*$/", $_POST['password'])) {
                                    $password = $_POST['password'];
                                    if (!empty($_POST['confirm_password']) && preg_match("/^[a-zA-Z0-9@_ .]*$/", $_POST['confirm_password'])) {
                                        $confirm_password = $_POST['confirm_password'];
                                        if ($password === $confirm_password) {
                                            // $user_login_query = "insert into user_login_log (login_id,username,login_status,ip_address,login_time,logout_time) values('{$_SESSION['login_id']}','$username','Password updated','$ip_address',current_timestamp,NULL);";
                                            // $conn->query($user_login_query);
                                            $hash_password = password_hash($password, PASSWORD_DEFAULT);
                                            $password_update_query = "update user_details set password='$hash_password', password_attempt='4',forgot_pass_attempt='2',token_attempt='1' where username='$username';";
                                            $conn->query($password_update_query);
                                            header("location:user_login.php");
                                        } else {
                                            echo '<h6 style="text-align: center; color:red;">Password doesn\'t match</h6>';
                                        }
                                    } else {
                                        echo '<h6 style="text-align: center; color:red;">Invalid password</h6>';
                                    }
                                } else {
                                    echo '<h6 style="text-align: center; color:red;">Invalid password</h6>';
                                }
                            } else {
                                // $user_login_query = "insert into user_login_log (login_id,username,login_status,ip_address,login_time,logout_time) values('{$_SESSION['login_id']}','$username','Invalid OTP','$ip_address',current_timestamp,NULL);";
                                // $conn->query($user_login_query);
                                $forgot_pass_attempt = --$result['forgot_pass_attempt'];
                                if ($forgot_pass_attempt > 0) {
                                    $forgot_attempt_update = "update user_details set forgot_pass_attempt='$forgot_pass_attempt' where username='$username';";
                                    $conn->query($forgot_attempt_update);
                                    echo '<h6 style="text-align: center; color:red;">Invalid OTP and ' . $forgot_pass_attempt . 'attempt remaining</h6>';
                                } else {
                                    header("location:token_verify.php");
                                }
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>