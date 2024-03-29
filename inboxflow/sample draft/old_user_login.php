<?php
//author:Theertheshwaran T
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "../config.php";
require_once "../Admin/admin_functions.php";
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
                <h2 style="text-align: center;">SIGN IN</h2>
                <div class="form">
                    <form action="../user/user_login.php" method="post">
                        <label for="username">Username</label><br>
                        <input type="text" name="username" id="username"><br><br>
                        <label for="password">Password</label><br>
                        <input type="password" name="password"><br><br>
                        <div class="form-buttons" style="text-align: center;">
                            <input type="submit" name="login" value="LOGIN">
                            <input type="reset" value="CLEAR">
                            <h6>New user? click here to <a href="registeration_login_page.php">register</a></h6>
                        </div>
                    </form>
                    <?php
                    if (isset($_POST['login'])) {
                        if (!empty(($_POST['username']))) {
                            //validate username
                            //assigning sanitized and validate username to username variable 
                            if (preg_match("/^[a-zA-Z0-9 @]*$/", $_POST['username'])) {
                                $username = sanitizing($_POST['username']);
                                $get_query = "select * from user_details where username='{$username}';";
                                $get_query_output = $conn->query($get_query);
                                if ($get_query_output->num_rows == 0) {
                                    echo "<h6 style=\"text-align: center; color:red;\">Kindly check if the username is correct</h6>";
                                }
                            } else {
                                echo "<h6 style=\"text-align: center; color:red;\">Username must be a alphanumeric</h6>";
                            }
                        }
                        if (!empty(($_POST['password']))) {
                            //validate password
                            //assigning sanitized and validate password to password variable
                            if (preg_match("/^[a-zA-Z0-9@ ]*$/", $_POST['password'])) {;
                                $password = sanitizing($_POST['password']);
                                if ($get_query_output->num_rows > 0) {
                                    $result = $get_query_output->fetch_assoc();
                                    if (password_verify($password, $result['password'])) {
                                        if ($result['user_status'] == "enable") {
                                            $_SESSION['login_id'] = random(8);
                                            $_SESSION['token_id'] = $result['token_id'];
                                            $last_login = "update user_details set login_id='{$_SESSION['login_id']}',last_login=current_timestamp where token_id='{$_SESSION['token_id']}';";
                                            $last_login_update = $conn->query($last_login);
                                            $_SESSION['last_activity'] = time();
                                            $_SESSION['username'] = $username;
                                            // echo "username :".$username . "password:".$password."token_id:".$_SESSION['token_id'];
                                            header("location:email.php?page=Email&option=Inbox");
                                        } else {
                                            echo "<h6 style=\"text-align: center; color:red;\">Account Deactivated</h6>";
                                        }
                                    } else {
                                        $attempt = $result['password_attempt'];
                                        if ($attempt > 0) {
                                            $attempt = $result['password_attempt'] - 1;
                                            $attempt_update = "update user_details set password_attempt='$attempt' where username='$username';";
                                            $conn->$attempt_update;
                                            echo "<h6 style=\"text-align: center; color:red;\">Incorrect password and " . $attempt . " attempt remaining</h6>";
                                        } else {
                                            header("location:password_change.php");
                                        }
                                    }   
                                }
                            } else {
                                echo "<h6 style=\"text-align: center; color:red;\">password must be a alphanumeric value</h6>";
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