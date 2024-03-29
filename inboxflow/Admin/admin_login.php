<?php
session_start();
//error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
//requiring database connection file
require_once '../config.php';
require_once 'admin_functions.php';
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
    <div class="overall-container">
        <div class="container">
            <div class="fraction_one">
                <div style="display: flex;justify-content: center;margin: 23%;">
                    <img src="../Images/logo.png" alt="">
                </div>
            </div>
            <div class="fraction_two">
                <div class="fraction_two_container">
                    <!--title of page-->
                    <h2 style="text-align: center;">ADMIN SIGN IN</h2>
                    <div class="form">
                        <!--login form-->
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <label for="username">Username</label><br>
                            <input type="text" name="username" id="username" value="<?= (!empty($_POST['username']) && !isset($_POST['clear'])) ? sanitizing($_POST['username']) : '' ?>"><br><br>
                            <label for="password">Password</label><br>
                            <input type="password" name="password" placeholder=""><br><br>
                            <div class="form-buttons">
                                <input type="submit" name="login" value="LOGIN">
                                <input type="submit" value="CLEAR" name="clear" class="clear-btn">
                                <h6>Click here to register as <a href="../Admin/admin_registeration_page.php">Admin</a></h6>
                        </form>
                        <?php
                        //when login button is clicked
                        if (isset($_POST['login'])) {
                            if (!empty(($_POST['username']))) {
                                //validate username
                                //assigning sanitized and validated username to username variable 
                                if (preg_match("/^[a-zA-Z0-9 ]*$/", $_POST['username'])) {
                                    $user_name = sanitizing($_POST['username']);
                                    $get_query = "select * from admin_details where username='{$user_name}';";
                                    $get_query_output = $conn->query($get_query);
                                    if ($get_query_output->num_rows == 0) {
                                        echo "<h6 style=\"text-align: center; color:red;\">Kindly check if the username is correct</h6>";
                                    } elseif (!empty(($_POST['password']))) {
                                        //validate password
                                        //assigning sanitized and validate password to password variable 
                                        if (preg_match("/^[a-zA-Z0-9 ]*$/", $_POST['password'])) {
                                            $password = sanitizing($_POST['password']);
                                            if ($get_query_output->num_rows > 0) {
                                                $result = $get_query_output->fetch_assoc();
                                                if ($result['admin_status'] == "enable") {
                                                    $emp_id = $result['emp_id'];
                                                    $username = $result['username'];
                                                    $_SESSION['username'] = $username;
                                                    $name = $result['name'];
                                                    $role = $result['role'];
                                                    $activity = "";
                                                    //setting the default timezone
                                                    date_default_timezone_set('Asia/Kolkata');
                                                    $login_time = date('y-m-d H:i:s');
                                                    //verifying with he hashed password
                                                    if (password_verify($password, $result['password'])) {
                                                        $_SESSION['admin_token_id'] = $result['token_id'];
                                                        $_SESSION['current_page'] = "I-Box Dashboard";
                                                        $_SESSION['current_option'] = "Solved";
                                                        $login_status = "success";
                                                        //inserting login info
                                                        $activity_insert_query = "insert into login_activity (emp_id, username, name, role, login_status, activity, login_time) values('$emp_id','$username','$name','$role','$login_status','$activity','$login_time')";
                                                        $activity_insert_output = $conn->query($activity_insert_query);
                                                        $login_update_query = "update admin_details set last_login=current_timestamp;";
                                                        $login_update_output = $conn->query($login_update_query);
                                                        $_SESSION['login_time'] = $login_time;
                                                        $user_login_query = "insert into user_login_log (login_id,username,login_status,ip_address,login_time,logout_time) values('{$_SESSION['login_id']}','$username','success','$ip_address',current_timestamp,NULL);";
                                                        $conn->query($user_login_query);
                                                        header("location:admin_dashboard.php?page=I-Box Dashboard");
                                                    } else {
                                                        $login_status = "Wrong password";
                                                        $activity_insert_query = "insert into login_activity (emp_id, username, name, role, login_status, activity, login_time) values('$emp_id','$username','$name','$role','$login_status','$activity','$login_time')";
                                                        $activity_insert_output = $conn->query($activity_insert_query);
                                                        $attempt = --$result['password_attempt'];
                                                        if ($attempt > 0 && $result['admin_status'] == 'enable') {
                                                            $attempt_update = "update admin_details set password_attempt='$attempt' where username='$username';";
                                                            $conn->query($attempt_update);
                                                            echo "<h6 style=\"text-align: center; color:red;\">Incorrect password and " . $attempt . " attempt remaining</h6>";
                                                        } else {
                                                            $otp = random(6);
                                                            $otp_update_query = "update admin_details set password_attempt=0, otp='$otp' where username='$username';";
                                                            $conn->query($otp_update_query);
                                                            header("location:admin_password_change.php");
                                                        }
                                                    }
                                                } else {
                                                    echo "<h6 style=\"text-align: center; color:red;\">Account freezed</h6>";
                                                }
                                            } else {
                                                echo "<h6 style=\"text-align: center; color:red;\">User not found</h6>";
                                            }
                                        } else {
                                            echo "<h6 style=\"text-align: center; color:red;\">Password not valid</h6>";
                                        }
                                    } else {
                                        echo "<h6 style=\"text-align: center; color:red;\">Enter the password</h6>";
                                    }
                                } else {
                                    echo "username must be a alphanumeric";
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>