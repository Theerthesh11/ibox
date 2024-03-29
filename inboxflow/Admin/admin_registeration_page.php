<?php
session_start();
//error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
//requiring admin_functions.php
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
    <div class="container">
        <div class="fraction_one">
            <div style="display: flex;justify-content: center;margin: 42%;">
                <img src="../Images/logo.png" alt="">
            </div>
        </div>
        <div class="fraction_two">
            <!--navigation panel-->
            <div class="nav-panel">
                <a href="../Admin/admin_login.php">ADMIN SIGN IN</a>
            </div>
            <!--title of page-->
            <h2 style="text-align: center;">ADMIN SIGN UP</h2>
            <div class="form">
                <!--registeration form-->
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    <label for="employee_id">Employee ID</label>
                    <input type="text" name="emp_id" id="employee_id" placeholder=""><br><br>
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" placeholder=""><br><br>
                    <label for="role">Role</label>
                    <select name="role" id="role">
                        <option value="admin">ADMIN</option>
                        <!-- <option value="superadmin">SUPERADMIN</option> -->
                    </select><br><br>
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" placeholder=""><br><br>
                    <label for="phone_no">Phone number</label>
                    <input type="text" name="phone_no" id="phone_no" placeholder=""><br><br>
                    <label for="dateofbirth">DOB</label>
                    <input type="date" name="dateofbirth" max="<?= date('Y-m-d') ?>" required><br><br>
                    <label for="username">User name</label>
                    <input type="text" name="username" id="username" placeholder=""><br><br>
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder=""><br><br>
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" name="confirm-password" id="password"><br><br>
                    <div class="form-buttons">
                        <input type="submit" value="REGISTER" name="register">
                        <input type="reset" value="CLEAR"><br><br>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    //requiring database connection
    require_once '../config.php';
    //declaring variables
    $name = $email = $password = $username = $phone_no = $dob = $created_by = $updated_by = "";
    //when register button is clicked
    if (isset($_POST['register'])) {
        if (!empty(($_POST['emp_id']))) {
            if (preg_match("/^[a-zA-Z0-9 ]*$/", $_POST['emp_id'])) {
                $emp_id = sanitizing($_POST['emp_id']);
            }
            if (!empty(($_POST['email']))) {
                //validate email
                //assigning validated email to email session variable 
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $_SESSION['admin_email'] = sanitizing($_POST['email']);
                } else {
                    echo "<h6 style=\"text-align: center; color:red;\">Enter a valid email</h6>";
                }
            }
            if (!empty(($_POST['role']))) {
                //validate name
                //assigning validate name to name variable 
                $role = sanitizing($_POST['role']);
            }
            if (!empty(($_POST['name']))) {
                //validate name
                //assigning validate name to name variable 
                if (preg_match("/^[a-zA-Z ]*$/", $_POST['name'])) {
                    $name = sanitizing($_POST['name']);
                    $created_by = $name;
                    $updated_by = $name;
                } else {
                    echo "<h6 style=\"text-align: center; color:red;\">Name is not valid</h6>";
                }
            }
            if (!empty(($_POST['phone_no']))) {
                //validate phone_no
                //assigning validate phone_no to phone_no variable 
                if (preg_match("/^[0-9]{10}$/", $_POST['phone_no'])) {
                    $phone_no = sanitizing($_POST['phone_no']);
                    // echo $phone_no;
                } else {
                    echo "<h6 style=\"text-align: center; color:red;\">Mobile number is not valid</h6>";
                }
            }
            if (!empty(($_POST['username']))) {
                //validate username
                //assigning validate username to username variable 
                if (preg_match("/^[a-zA-Z0-9 ]*$/", $_POST['username'])) {
                    $username = sanitizing($_POST['username']);
                    $get_query = "select * from user_details where username='{$username}';";
                    $get_query_output = $conn->query($get_query);
                    if ($get_query_output->num_rows > 0) {
                        echo "username already exist <br>";
                    } else {
                        echo "username is valid <br>";
                    }
                }
            }
            if (!empty(($_POST['password']))) {
                //validate password
                //assigning validate password to password variable 
                if (preg_match("/^[a-zA-Z0-9 ]*$/", $_POST['password'])) {
                    $password = sanitizing($_POST['password']);
                } else {
                    echo "<h6 style=\"text-align: center; color:red;\">Invalid password</h6>";
                }
            }
            if (!empty($_POST['confirm-password'])) {
                //validate password
                //assigning validate password to password variable
                if (preg_match("/^[a-zA-Z0-9 ]*$/", $_POST['confirm-password'])) {
                    $confirm_password = sanitizing($_POST['confirm-password']);
                    if ($password == $confirm_password) {
                        $password = $confirm_password;
                        $hash_password = password_hash($password, PASSWORD_DEFAULT);
                    } else {
                        echo "Password doesn't match";
                    }
                } else {
                    echo "<h6 style=\"text-align: center; color:red;\">Invalid password</h6>";
                }
            }
            //
            $get_query = "select token_id from user_details where email='{$email}';";
            $get_query_output = $conn->query($get_query);
            if ($get_query_output->num_rows > 0) {
                $result = $get_query_output->fetch_assoc();
                $token_id = $result['token_id'];
            } elseif (!empty($_POST['dateofbirth'])) {
                if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $_POST['dateofbirth']) && strtotime($_POST['dateofbirth']) !== false) {
                    $dob = $_POST['dateofbirth'];
                    $year = substr($dob, 2, 2);
                    $date = substr($dob, 8, 2);
                }
                $token_id = random(8) . $date . random(2) . "4" . random(3) . random_byte() . random(3) . random(14) . $year;
                // echo $_SESSION['token_id'];
            }
            $register_query = "insert into admin_details (token_id, emp_id, email, role, name, date_of_birth, username, password, phone_no,last_login, created_by, created_on, updated_by, updated_on) values(unhex('$token_id'), '$emp_id', '{$_SESSION['admin_email']}','$role','$name','$dob' ,'$username', '$hash_password', '$phone_no',current_timestamp ,'$created_by', current_timestamp, '$updated_by', current_timestamp);";
            if ($conn->query($register_query)) {
                $fetch_token_query = "select token_id from admin_details where username='$username';";
                $fetch_token_output = $conn->query($fetch_token_query);
                if ($fetch_token_output->num_rows > 0) {
                    $fetch_token_result = $fetch_token_output->fetch_assoc();
                    $_SESSION['admin_token_id'] = $fetch_token_result['token_id'];
                }
                // echo "Registeration successfull</h6>";
                date_default_timezone_set('Asia/Kolkata');
                $login_time = date('y-m-d H:i:s');
                $login_update_query = "update admin_details set last_login='$login_time';";
                $login_update_output = $conn->query($login_update_query);
                $login_activity_query = "update login_activity set login_time='$login_time';";
                $login_activity_output = $conn->query($login_activity_query);
                $_SESSION['login_time'] = $login_time;
                // echo $_SESSION['admin_token_id'];
                header("location:admin_dashboard.php?page=I-Box Dashboard");
            } else {
                echo "Registeration unsuccessfull";
            }
        }
    }

    ?>
</body>

</html>