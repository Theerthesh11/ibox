<?php
session_start();
require_once "../user/email_function.php";
// require_once "../sample draft/welcome_mail.php";
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
            <div style="display: flex;
    justify-content: center;
    margin: 42%;">
                <img src="../Images/logo.png" alt="">
            </div>
        </div>
        <div class="fraction_two">
            <div class="nav-panel">
                <a href="../user/user_login.php">SIGN IN</a>
            </div>
            <div class="form">
                <h2 style="text-align: center;">SIGN UP</h2>
                <form action="../user/registeration_login_page.php" method="post">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="" value="<?= isset($_POST['email']) && !isset($_POST['clear']) ? $_POST['email'] : '' ?>"><br><br>
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" placeholder="" value="<?= isset($_POST['name']) && !isset($_POST['clear']) ? $_POST['name'] : '' ?>"><br><br>
                    <label for="phone_no">Phone number</label>
                    <input type="text" name="phone_no" id="phone_no" placeholder="" value="<?= isset($_POST['phone_no']) && !isset($_POST['clear']) ? $_POST['phone_no'] : '' ?>"><br><br>
                    <label for="dateofbirth">DOB</label>
                    <input type="date" name="dateofbirth" min="1960-01-01" max="2006-01-01" value="<?= isset($_POST['dateofbirth']) && !isset($_POST['clear']) ? $_POST['dateofbirth'] : '' ?>" required><br><br>
                    <label for="username">User name</label>
                    <input type="text" name="username" id="username" placeholder="" value="<?= isset($_POST['username']) && !isset($_POST['clear']) ? $_POST['username'] : '' ?>"><br><br>
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder=""><br><br>
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" name="confirm-password" id="password" placeholder=""><br><br>
                    <div class="form-buttons">
                        <input type="submit" value="REGISTER" name="register">
                        <input type="submit" name="clear" value="CLEAR" class="clear-btn"><br><br>
                    </div>
                    <?php
                    require_once '../config.php';
                    $name = $email = $password = $username = $phone_no = $dob = $created_by = $updated_by = "";

                    if (isset($_POST['register'])) {
                        if (!empty(($_POST['email']))) {
                            //validate email
                            //assigning sanitized and validate email to email variable 
                            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                                $email = sanitizing($_POST['email']);
                                if (preg_match('/@gmail\.com$/', $email)) {
                                    $_SESSION['email'] = $email;
                                    // Valid Gmail address, proceed with your logic
                                } else {
                                    // Invalid Gmail address, handle the error
                                }

                                // echo $_SESSION['email'];
                            } else {
                                echo "<h6 style=\"text-align: center; color:red;\">Enter a valid email</h6>";
                            }
                        }
                        if (!empty($_POST['name'])) {
                            //validate name
                            //assigning sanitized and validate name to name variable 
                            if (preg_match("/^[a-zA-Z ]*$/", $_POST['name'])) {
                                $name = sanitizing($_POST['name']);
                                $created_by = $name;
                                $updated_by = $name;
                            } else {
                                echo "<h6 style=\"text-align: center; color:red;\">Name is not valid</h6>";
                            }
                        }
                        if (!empty($_POST['phone_no'])) {
                            //validate phone_no
                            if (preg_match("/^[0-9]{10}$/", $_POST['phone_no'])) {
                                $phone_no = sanitizing($_POST['phone_no']);
                                // echo $phone_no;
                            } else {
                                echo "<h6 style=\"text-align: center; color:red;\">Mobile number is not valid</h6>";
                            }
                        }
                        if (!empty($_POST['username'])) {
                            //validate username
                            //assigning sanitized and validate username to username variable 
                            if (preg_match("/^[a-zA-Z0-9 @]*$/", $_POST['username'])) {
                                $username = sanitizing($_POST['username']);
                                $_SESSION['username'] = $username;
                                $get_query = "select * from user_details where username='{$username}';";
                                $get_query_output = $conn->query($get_query);
                                // print_r($get_query_output);
                                // echo $username;
                                if ($get_query_output->num_rows > 0) {
                                    echo "username already exist <br>";
                                    exit();
                                } else {
                                    echo "username is valid <br>";
                                }
                            } else {
                                echo "<h6 style=\"text-align: center; color:red;\">Invalid username</h6>";
                            }
                        }
                        if (!empty($_POST['password'])) {
                            //validate password
                            //assigning sanitized and validate password to password variable 
                            if (preg_match("/^[a-zA-Z0-9@]*$/", $_POST['password'])) {
                                $password = sanitizing($_POST['password']);
                                // echo $password;
                            } else {
                                echo "<h6 style=\"text-align: center; color:red;\">Enter a password containing alphanumeric values</h6>";
                            }
                        }
                        if (!empty($_POST['confirm-password'])) {
                            //validate password
                            //assigning sanitized and validate password to password variable
                            if (preg_match("/^[a-zA-Z0-9 @]*$/", $_POST['confirm-password'])) {
                                if ($password == sanitizing($_POST['confirm-password'])) {
                                    $hash_password = password_hash($password, PASSWORD_DEFAULT);
                                    // echo $password;
                                } else {
                                    echo "<h6 style=\"text-align: center; color:red;\">Password doesn't match</h6>";
                                }
                            } else {
                                echo "<h6 style=\"text-align: center; color:red;\">Enter a password containing alphanumeric values</h6>";
                            }
                        }
                        if (!empty($_POST['dateofbirth'])) {
                            if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $_POST['dateofbirth']) && strtotime($_POST['dateofbirth']) !== false) {
                                $dob = $_POST['dateofbirth'];
                                $year = substr($dob, 2, 2);
                                $date = substr($dob, 8, 2);
                                echo $dob;
                                $token_id = random(8) . $date . random(2) . "4" . random(3) . random_byte() . random(3) . random(14) . $year;
                                $_SESSION['token_id'] = $token_id;
                            } else {
                                echo "<h6 style=\"text-align: center; color:red;\">Date of Birth is not valid</h6>";
                            }
                        }
                        $user_default_ip = getIPAddress();
                        $_SESSION['login_id'] = random(8);
                        $register_query = "insert into user_details (token_id, email, name, date_of_birth, username, password, phone_no,last_login,login_id,ip_address, created_by, created_on, updated_by, updated_on) values(UNHEX('$token_id'), '{$_SESSION['email']}', '$name','$dob' ,'$username', '$hash_password', '$phone_no',current_timestamp,'{$_SESSION['login_id']}','$user_default_ip ', '$created_by', current_timestamp, '$updated_by', current_timestamp);";
                        if ($conn->query($register_query)) {
                            $fetch_token_query = "select token_id from user_details where username='$username';";
                            $fetch_token_output = $conn->query($fetch_token_query);
                            if ($fetch_token_output->num_rows > 0) {
                                $fetch_token_result = $fetch_token_output->fetch_assoc();
                                $_SESSION['token_id'] = $fetch_token_result['token_id'];
                            }
                            mkdir("../Attachments/" . bin2hex($_SESSION['token_id']));
                            // welcome_mail($result['email']);
                            header("location:email.php?page=Email&option=Inbox");
                        } else {
                            echo "<h6 style=\"text-align: center; color:red;\">Registeration unsuccessfull</h6>";
                        }
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>
</body>

</html>