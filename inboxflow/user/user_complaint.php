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
</head>

<body>
    <div class="container">
        <div class="fraction_one">
            <div style="display: flex;justify-content: center;margin: 23%;">
                <img src="../Images/logo.png" alt="">
            </div>
        </div>
        <div class="fraction_two">
            <div class="nav-panel">
                <a href="../user/user_login.php">SIGN IN</a>
            </div>
            <div class="fraction_two_container">
                <h2 style="text-align: center;">SUPPORT</h2>
                <div class="form">
                    <form action="../user/user_complaint.php" method="post">
                        <label for="username">Username</label><br>
                        <input type="text" name="username" id="username" value="<?= isset($_POST['username']) && !isset($_POST['clear']) ? $_POST['username'] : '' ?>"><br><br>
                        <label for="query">QUERY</label><br>
                        <textarea name="query"></textarea><br><br>
                        <div class="form-buttons" style="text-align: center;">
                            <input type="submit" name="send" value="SEND">
                            <input type="submit" name="clear" value="CLEAR" class="clear-btn">
                        </div>
                    </form>
                    <?php
                    if (isset($_POST['send'])) {
                        if (preg_match("/^[a-zA-Z0-9 ]*$/", $_POST['username'])) {
                            $user_name = sanitizing($_POST['username']);
                            $get_query = "select email from user_details where username='{$user_name}';";
                            $get_query_output = $conn->query($get_query);
                            if ($get_query_output->num_rows > 0) {
                                $result = $get_query_output->fetch_assoc();
                                $from_email = $result['email'];
                            }
                        }
                        if (!empty($_POST['query'])) {
                            $user_complaint = sanitizing($_POST['query']);
                        } else {
                            $user_complaint = "";
                        }
                        if (!empty($user_name) && !empty($user_complaint)) {
                            $insert_query = "insert into user_queries (username,query,query_date) values ('$user_name','$user_complaint',current_timestamp);";
                            if ($conn->query($insert_query)) {
                                echo '<h6 style="text-align: center; color:black;">Complaint raised</h6>';
                            }
                        }else{
                            echo '<h6 style="text-align: center; color:black;">Fill the complaint boxes</h6>';
                        }
                    }
