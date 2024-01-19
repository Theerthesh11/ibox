<?php
session_start();
//error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
//including required files
require_once "../admin/admin_functions.php";
require_once "../config.php";
//checking for token in session
$token_id = isset($_SESSION['admin_token_id']) ? $_SESSION['admin_token_id'] : header("location:admin_login.php");
// $_SESSION['current_page']='';
//fetching record for that token id
$admin_details_query = "select * from admin_details where token_id='$token_id';";
$admin_details_output = $conn->query($admin_details_query);
if ($admin_details_output->num_rows > 0) {
    $admin_details = $admin_details_output->fetch_assoc();
    $emp_id = $admin_details['emp_id'];
    $username = $admin_details['username'];
    $login_time = isset($_SESSION['login_time']) ? $_SESSION['login_time'] : header("location:admin_login.php");
} else {
    header("location:admin_login.php");
}
if ($admin_details['role'] == "superadmin") {
    $path = "page=Queries";
} else {
    $path = "page=Queries&option=Solved";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- <meta http-equiv="refresh" content="5"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inboxflow</title>
    <link rel="stylesheet" href="../css/email.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <!--creating navigation panel-->
    <div class="navigation-bar">
        <div class="top-navigation-bar-logo">
            <!--site logo-->
            <div class="logo">
                <img src="../images/logo.png" alt="sitelogo">
            </div>
        </div>
        <!--displays current page -->
        <div class="top-navigation-content">
            <div class="path">
                <?php
                $page = isset($_GET['page']) ? $_GET['page'] : $_SESSION['current_page'];
                $option = isset($_GET['option']) ? $_GET['option'] : $_SESSION['current_option'];
                $page_no = empty($_GET['page_no']) ? '1' : $_GET['page_no'];
                ?>
                <a href="admin_dashboard.php?<?= $path ?>" style="color:rgb(114, 98, 255);">
                    <?= $page ?>
                </a>
                <?php
                if (isset($_GET['option'])) {
                ?>
                    <a href="admin_dashboard.php?page=<?= $page ?>&option=<?= $option ?>" style="color:rgb(114, 98, 255);">
                        <?= " | " . $option ?>
                    </a>
                <?php
                }
                ?>
            </div>
            <!--adding file that displays profile picture-->
            <div class="profile">
                <?php require_once "../admin/admin_profile_picture.php" ?>
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
                <li><a href="?page=I-Box Dashboard"><button <?= (isset($_GET['page']) && $_GET['page'] === 'I-Box Dashboard') || ($_SESSION['current_page'] == 'I-Box Dashboard' && !isset($_GET['page'])) ? 'class="active"' : '' ?>>I-Box Dashboard</button></a></li>
                <li><a href="?page=Admin List"><button <?= (isset($_GET['page']) && $_GET['page'] === 'Admin List') || ($_SESSION['current_page'] == 'Admin List' && !isset($_GET['page'])) ? 'class="active"' : '' ?>>Admin list</button></a></li>
                <li><a href="?page=User List"><button <?= (isset($_GET['page']) && $_GET['page'] === 'User List') || ($_SESSION['current_page'] == 'User List' && !isset($_GET['page'])) ? 'class="active"' : '' ?>>User list</button></a></li>
                <li><a href="?page=Login Activity"><button <?= (isset($_GET['page']) && $_GET['page'] === 'Login Activity') || ($_SESSION['current_page'] == 'Login Activity' && !isset($_GET['page'])) ? ' class="active"' : '' ?>>Login activity</button></a></li>
                <li><a href="?page=User log"><button <?= (isset($_GET['page']) && $_GET['page'] === 'User log') || ($_SESSION['current_page'] == 'User log' && !isset($_GET['page'])) ? ' class="active"' : '' ?>>User log</button></a></li>
                <li><a href="?page=Admin"><button <?= (isset($_GET['page']) && $_GET['page'] === 'Admin') || ($_SESSION['current_page'] == 'Admin' && !isset($_GET['page'])) ? ' class="active"' : '' ?>>Admin</button></a></li>
                <li><a href="?page=Access"><button <?= (isset($_GET['page']) && $_GET['page'] === 'Access') || ($_SESSION['current_page'] == 'Access' && !isset($_GET['page'])) ? ' class="active"' : '' ?>>Access</button></a></li>
                <li><a href="?<?= $path ?>"><button <?= (isset($_GET['page']) && $_GET['page'] === 'Queries') || ($_SESSION['current_page'] == 'Queries' && !isset($_GET['page'])) ? ' class="active"' : '' ?>>Queries</button></a>
                    <?php
                    if ($page == "Queries" && $admin_details['role'] == "admin") {
                    ?>
                        <ul>
                            <li><a href="?page=Queries&option=Solved"><button <?= (isset($_GET['option']) && $_GET['option'] === 'Solved') || ($_SESSION['current_option'] == 'Solved' && !isset($_GET['option'])) ? ' class="sub-active"' : '' ?>>Solved</button></a></li>
                            <li><a href="?page=Queries&option=Unsolved"><button <?= (isset($_GET['option']) && $_GET['option'] === 'Unsolved') || ($_SESSION['current_option'] == 'Unsolved' && !isset($_GET['option'])) ? ' class="sub-active"' : '' ?>>Unsolved</button></a></li>
                        </ul>
                    <?php
                    }
                    ?>
                </li>
            </ul>
        </div>
        <?php
        //php condition for each page using switch statement when each link is clicked
        switch ($page) {
                //when the active page is dashboard
            case 'I-Box Dashboard':
                $_SESSION['current_page'] = "I-Box Dashboard"
        ?>
                <div class="dashboard-container">
                    <div class="dashboard-content">
                        <?php
                        //checks whether the admin has access to this page
                        //1 means access is given 0 means access restricted
                        if ($admin_details['ibox_dashboard'] == 1) {
                        ?>
                            <div class="report">
                                <h4>OVERALL REPORT</h4><br>
                            </div>
                            <!--total mails sent using inbox flow-->
                            <div class="admin-dashboard-counts">
                                <div class="dashboard-count1">
                                    <h3>Total mails</h3>
                                    <?= total_ibox_mail() ?>
                                </div>
                                <!--total users registered in inboxflow-->
                                <div class="dashboard-count2">
                                    <a href="admin_dashboard.php?page=User List" style="display: block;padding: 10px 25px 40px 25px;margin: -10px;color:black">
                                        <h3>Total Users</h3>
                                        <?= total_users() ?>
                                    </a>
                                </div>
                                <!--total admins registered in inboxflow-->
                                <div class="dashboard-count3">
                                    <a href="admin_dashboard.php?page=Admin List" style="display: block;padding: 10px 25px 40px 25px;margin: -10px;color:black">
                                        <h3>Total Admins</h3>
                                        <?= total_admins() ?>
                                    </a>
                                </div>
                            </div>
                            <!--displays current month report-->
                            <div class="report">
                                <h4>MONTHLY REPORT (<?= date('M') ?>)</h4><br>
                            </div>
                            <div class="admin-dashboard-counts">
                                <!--displays total mails sent in current month-->
                                <div class="dashboard-count1">
                                    <h3>Total mails</h3>
                                    <?= total_ibox_mail("where MONTH(date_of_sending)=" . date('m')) ?>
                                </div>
                                <!--displays total users registered in current month-->
                                <div class="dashboard-count2">
                                    <a href="admin_dashboard.php?page=User List" style="display: block;padding: 10px 25px 40px 25px;margin: -10px;color:black">
                                        <h3>Total Users</h3>
                                        <?= total_users("where MONTH(created_on)=" . date('m')) ?>
                                    </a>
                                </div>
                                <!--displays total admins registered in current month-->
                                <div class="dashboard-count3">
                                    <a href="admin_dashboard.php?page=Admin List" style="display: block;padding: 10px 25px 40px 25px;margin: -10px;color:black">
                                        <h3>Total Admins</h3>
                                        <?= total_admins("where MONTH(created_on)=" . date('m')) ?>
                                    </a>
                                </div>
                            </div>
                            <!--displays the report of cuurent week-->
                            <div class="report">
                                <h4>WEEKLY REPORT</h4>
                                <br>
                            </div>
                            <!--displays total mails sent in current week-->
                            <div class="admin-dashboard-counts">
                                <div class="dashboard-count1">
                                    <h3>Total mails</h3>
                                    <?= total_ibox_mail("where WEEK(date_of_sending)=0") ?>
                                </div>
                                <!--displays total users registered in current week-->
                                <div class="dashboard-count2">
                                    <h3>Total Users</h3>
                                    <?= total_users("where WEEK(created_on)=0") ?>
                                </div>
                                <!--displays total admins registered in current week-->
                                <div class="dashboard-count3">
                                    <h3>Total Admins</h3>
                                    <?= total_admins("where WEEK(created_on)=0") ?>
                                </div>
                            </div>
                    </div>
                </div>
            <?php
                        } else {
            ?><!--if admin does not have access this part id displayed-->
                <div class="restricted-access">
                    <h3>Access restricted</h3>
                    <img src="../images/lock.jpg" alt="lock">
                </div>
            <?php
                        }
                        break;
                        //Admin list code
                    case 'Admin List':
                        $_SESSION['current_page'] = "Admin List";
            ?><div class="dashboard-container">
                <div class="dashboard-content">
                    <?php
                        //checks if admin has access to this page
                        //1 means access is given 0 means access restricted
                        if ($admin_details['admin_list'] == 1) {
                    ?>
                        <!--displays the total admin in inboxflow-->
                        <!-- <form action="admin_dashboard.php?page=Admin List" method="post"> -->
                        <div class="access-options">
                            <div class="search">
                                <form action="../admin/admin_dashboard.php" method="get">
                                    <input type="search" name="admin_search" placeholder="abc@gmail.com" value="<?= isset($_GET['admin_search']) ? $_GET['admin_search'] : "" ?>">
                                    <input type="submit" name="search-btn" value="Search">
                                </form>
                            </div>
                            <div>
                                <form action="../Admin/admin_dashboard.php?page=Admin List&page_no=<?= isset($_GET['page_no']) ? $_GET['page_no'] : 1 ?>" method="post" id="user_access">
                                    <input type="submit" name="enable_admin" value="Enable">
                                    <input type="submit" name="disable_admin" value="Disable">
                            </div>
                        </div>
                        <div class="user_table">
                            <table class="user_list">
                                <tr style="text-align: center;color:white; background:hsla(246, 100%, 73%, 1);box-shadow:3px 3px 6px rgb(215, 212, 255);">
                                    <th style="width: 5%;"></th>
                                    <th style="width: 10%;">EMP ID</th>
                                    <th style="width: 18%;">NAME</th>
                                    <th style="width: 20%;">EMAIL</th>
                                    <th style="width: 10%;">ROLE</th>
                                    <th style="width: 10%;">MOBILE NO</th>
                                    <th style="width: 10%;">JOINED</th>
                                    <th style="width: 17%;">LAST LOGIN</th>
                                </tr>
                                <!--admin_details displays the total admins-->
                                <!--this function code is in admin_function-->
                                <?php
                                if (isset($_GET['admin_search'])) {
                                    $search_content =  !empty($_GET['admin_search']) ? sanitizing($_GET['admin_search']) : "";
                                    $search_query = "select * from admin_details where username like '%$search_content%' or email like '%$search_content%' or name like '%$search_content%'";
                                    $search_output = $conn->query($search_query);
                                    if ($search_output->num_rows > 0) {
                                        admin_details($page, $search_query);
                                    } else {
                                        echo "No results found<br><br>";
                                    }
                                } elseif (!isset($_POST['search-btn']) || empty($_GET['admin_search'])) {
                                    admin_details($page);
                                }
                                if (isset($_POST['enable_admin'])) {
                                    $enable_admin = !empty($_POST['admin_id']) ? $_POST['admin_id'] : array();
                                    foreach ($enable_admin as $admin_id) {
                                        $enable_admin_query = "update admin_details set admin_status='enable' where emp_id='$admin_id';";
                                        $enable_admin_output = $conn->query($enable_admin_query);
                                    }
                                } elseif (isset($_POST['disable_admin'])) {
                                    $disable_admin = !empty($_POST['admin_id']) ? $_POST['admin_id'] : array();
                                    foreach ($disable_admin as $admin_id) {
                                        $disable_admin_query = "update admin_details set admin_status='disable' where emp_id='$admin_id';";
                                        $disable_admin_output = $conn->query($disable_admin_query);
                                    }
                                }
                                ?>
                        </div>
                </div>
            </div>
        <?php

                        } else {
        ?>
            <!--if admin does not have access this part id displayed-->
            <div class="restricted-access">
                <h3>Access restricted</h3>
                <img src="../images/lock.jpg" alt="lock">
            </div>
        <?php
                        }
                        break;
                        //user list code
                    case 'User List':
                        $_SESSION['current_page'] = "User List"
        ?><div class="dashboard-container">
            <div class="dashboard-content">
                <?php
                        //checks if admin has access to this page
                        //1 means access is given 0 means access restricted
                        if ($admin_details['user_list'] == 1) {
                ?>

                    <div class="access-options">
                        <div class="search">
                            <form action="admin_dashboard.php" method="get">
                                <input type="search" name="user_list_search" placeholder="abc@gmail.com" value="<?= isset($_GET['user_list_search']) ? $_GET['user_list_search'] : "" ?>">
                                <input type="submit" name="user_list_search_btn" value="Search">
                            </form>
                        </div>
                        <!--access buttons for delete button on user's trash page-->
                        <div class="grant-access">
                            <form action="admin_dashboard.php?page=User List&page_no=<?= isset($_GET['page_no']) ? $_GET['page_no'] : 1 ?>" method="post" id="user_access">
                                <input type="submit" name="enable_user" value="Enable User">
                                <input type="submit" name="disable_user" value="Disable User">
                                <input type="submit" name="grant_delete_access" value="Grant Access">
                                <input type="submit" name="block_delete_access" value="Block Access">
                            </form>
                        </div>
                    </div>
                    <table class="user_list">
                        <tr style="text-align: center;color:white; background:hsla(246, 100%, 73%, 1);">
                            <th></th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>STATUS</th>
                            <th>MOBILE NO</th>
                            <th>JOINED</th>
                            <th>LAST LOGIN</th>
                            <th>SENT/RECIEVED</th>
                        </tr>
                        <?php
                            if (isset($_GET['user_list_search'])) {
                                $user_search_content =  !empty($_GET['user_list_search']) ? sanitizing($_GET['user_list_search']) : "";
                                $user_search_query = "select * from user_details where username like '%$user_search_content%' or email like '%$user_search_content%' or name like '%$user_search_content%'";
                                $user_search_output = $conn->query($user_search_query);
                                if ($user_search_output->num_rows > 0) {
                                    user_list("User List", $user_search_query);
                                } else {
                                    user_list("User List");
                                }
                            } else {
                                user_list("User List");
                            }
                            if (isset($_POST['enable_user'])) {
                                $enable_user = !empty($_POST['delete_access']) ? $_POST['delete_access'] : array();
                                foreach ($enable_user as $username) {
                                    $enable_user_query = "update user_details set user_status='enable',password_attempt=4,forgot_pass_attempt=2,token_attempt=1 where username='$username';";
                                    $enable_user_output = $conn->query($enable_user_query);
                                }
                            } elseif (isset($_POST['disable_user'])) {
                                $disable_user = !empty($_POST['delete_access']) ? $_POST['delete_access'] : array();
                                foreach ($disable_user as $username) {
                                    $disable_user_query = "update user_details set user_status='disable',password_attempt=0,forgot_pass_attempt=0,token_attempt=0 where username='$username';";
                                    $disable_user_output = $conn->query($disable_user_query);
                                }
                            }
                            //access for delete changes to yes for user when grant access button is clicked
                            elseif (isset($_POST['grant_delete_access'])) {
                                $grant_delete_access = !empty($_POST['delete_access']) ? $_POST['delete_access'] : array();
                                foreach ($grant_delete_access as $username) {
                                    $grant_access_query = "update user_details set trash_delete='yes' where username='$username';";
                                    $grant_access_output = $conn->query($grant_access_query);
                                }
                            } //access for delete changes to no for user when grant access button is clicked
                            elseif (isset($_POST['block_delete_access'])) {
                                $block_delete_access = !empty($_POST['delete_access']) ? $_POST['delete_access'] : array();
                                foreach ($block_delete_access as $username) {
                                    $block_access_query = "update user_details set trash_delete='no' where username='$username';";
                                    $block_access_output = $conn->query($block_access_query);
                                }
                            }
                        } else {
                        ?>
                        <!--if admin does not have access this part id displayed-->
                        <div class="restricted-access">
                            <h3>Access restricted</h3>
                            <img src="../images/lock.jpg" alt="lock">
                        </div>
                    <?php
                        }
                        break;
                        //displays the logi activity of admins 
                    case 'Login Activity':
                        $_SESSION['current_page'] = "Login Activity";
                    ?>
                    <div class="dashboard-container">
                        <div class="dashboard-content">
                            <?php
                            //checks if admin has access to this page
                            //1 means access is given 0 means access restricted
                            if ($admin_details['login_activity'] == 1) {
                            ?>
                                <form action="admin_dashboard.php?page=Login%20Activity&page_no=<?= isset($_GET['page_no']) ? $_GET['page_no'] : 1 ?>" method="get">
                                    <div class="access-options">
                                        <div class="search">
                                            <Label for="from_date">FROM</Label>
                                            <input type="date" id="from_date" name="from_date">
                                            <Label for="to_date">TO</Label>
                                            <input type="date" id="to_date" name="to_date" max="<?= date('Y-m-d') ?>">
                                            <input type="submit" name="activity-search-btn" value="Search">
                                        </div>
                                        <!-- <div> -->
                                        <!--retrives login info for past 3 days-->
                                        <!-- <input type="submit" name="last_3_days" value="Last 3 days"> -->
                                        <!--retrives login info for past 7 days-->
                                        <!-- <input type="submit" name="last_7_days" value="Last 7 days"> -->
                                        <!-- retrives login info for past 15 days -->
                                        <!-- <input type="submit" name="last_15_days" value="Last 15 days"> -->
                                        <!--retrives login info for past 30 days-->
                                        <!-- <input type="submit" name="last_30_days" value="Last 30 days"> -->
                                        <!-- </div> -->
                                    </div>
                                </form>
                                <div class="user_table">
                                    <table class="user_list">
                                        <!--table headers for admin login activity-->
                                        <tr style="text-align: center;color:white; background:hsla(246, 100%, 73%, 1);box-shadow:3px 3px 6px rgb(215, 212, 255);">
                                            <th style="width: 10%;">EMP ID</th>
                                            <th style="width: 20%;">USERNAME</th>
                                            <th style="width: 20%;">ROLE</th>
                                            <th style="width: 20%;">LOGIN TIME</th>
                                            <th style="width: 20%;">LOGOUT TIME</th>
                                        </tr>
                                        <?php
                                        //when last 3 days button is clicked
                                        // if (isset($_POST['last_3_days'])) {
                                        //     $custom_record_query = "select * from login_activity where DATE(login_time) between DATE_SUB(NOW(), INTERVAL 3 DAY) AND CURDATE()";
                                        //     $custom_record_output = $conn->query($custom_record_query);
                                        //     login_activity($page, $custom_record_query);
                                        // } //when last 7 days button is clicked
                                        // elseif (isset($_POST['last_7_days'])) {
                                        //     $custom_record_query = "select * from login_activity where DATE(login_time) between DATE_SUB(NOW(), INTERVAL 7 DAY) AND CURDATE()";
                                        //     $custom_record_output = $conn->query($custom_record_query);
                                        //     login_activity($page, $custom_record_query);
                                        // } //when last 15 days button is clicked
                                        // elseif (isset($_POST['last_15_days'])) {
                                        //     $custom_record_query = "select * from login_activity where DATE(login_time) between DATE_SUB(NOW(), INTERVAL 15 DAY) AND CURDATE()";
                                        //     $custom_record_output = $conn->query($custom_record_query);
                                        //     login_activity($page, $custom_record_query);
                                        // } //when last 30 days button is clicked
                                        // elseif (isset($_POST['last_30_days'])) {
                                        //     $custom_record_query = "select * from login_activity where DATE(login_time) between DATE_SUB(NOW(), INTERVAL 30 DAY) AND CURDATE()";
                                        //     // $custom_record_output = $conn->query($custom_record_query);
                                        //     login_activity($page, $custom_record_query);
                                        // } //by default displays total login info
                                        if (isset($_GET['activity-search-btn'])) {
                                            if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
                                                $from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : "";
                                                $to_date = !empty($_GET['to_date']) ? $_GET['to_date'] : "curdate()";
                                                if ($from_date > $to_date && $to_date == "curdate()") {
                                                    $from_date = "curdate()";
                                                } elseif ($from_date > $to_date) {
                                                    $from_date = $to_date;
                                                }
                                                $activity_search_query = "select * from login_activity where login_time >= '$from_date' AND login_time < '$to_date' + INTERVAL 1 DAY";
                                                // echo "From-".$from_date."To-".$to_date;
                                                login_activity($page, $activity_search_query);
                                            } else {
                                                $login_activity_query = "select * from login_activity";
                                                login_activity($page, $login_activity_query);
                                            }
                                        } else {
                                            $login_activity_query = "select * from login_activity";
                                            login_activity($page, $login_activity_query);
                                        }
                                        ?>
                                    </table>
                                </div>
                            <?php
                            } else {
                                //if admin does not have access this part id displayed
                                echo ' <div class="restricted-access">
                        <h3>Access restricted</h3>
                        <img src="../images/lock.jpg" alt="lock">
                    </div>';
                            }
                            break;
                        case 'User log':
                            $_SESSION['current_page'] = "User log";
                            ?>
                            <div class="dashboard-container">
                                <div class="dashboard-content">
                                    <?php
                                    //checks if admin has access to this page
                                    //1 means access is given 0 means access restricted
                                    // if ($admin_details['user_log'] == 1) {
                                    ?>
                                    <form action="admin_dashboard.php?page=User log&page_no=<?= isset($_GET['page_no']) ? $_GET['page_no'] : 1 ?>" method="post">
                                        <div class="access-options">
                                            <div class="search">
                                                <input type="search" name="username_search" value="" placeholder="username@123">
                                                <Label for="from_date">FROM</Label>
                                                <input type="date" id="from_date" name="from_date">
                                                <Label for="to_date">TO</Label>
                                                <input type="date" id="to_date" name="to_date" max="<?= date('Y-m-d') ?>">
                                                <input type="submit" name="user-log-search" value="Search">
                                            </div>
                                        </div>
                                    </form>
                                    <div class="user_table">
                                        <table class="user_list">
                                            <!--table headers for admin login activity-->
                                            <tr style="text-align: center;color:white; background:hsla(246, 100%, 73%, 1);box-shadow:3px 3px 6px rgb(215, 212, 255);">
                                                <th style="width: 25%;">USERNAME</th>
                                                <th style="width: 25%;">LOGIN STATUS</th>
                                                <th style="width: 25%;">LOGIN TIME</th>
                                                <th style="width: 25%;">LOGOUT TIME</th>
                                            </tr>
                                        <?php
                                        // }
                                        $user_log_query = "select * from user_login_log";
                                        user_login_activity($page, $user_log_query);
                                        // echo "</table></div>";
                                        break;
                                        //admin profile code
                                    case 'Admin':
                                        $_SESSION['current_page'] = "Admin";
                                        ?>
                                            <div class="profile-container">
                                                <div class="profile_picture">
                                                    <div>
                                                        <h3>Profile</h3>
                                                    </div>
                                                    <?php
                                                    //profile picture of admin
                                                    require "../admin/admin_profile_picture.php";
                                                    ?>
                                                </div>
                                                <?php
                                                //when logout is clicked
                                                if (isset($_POST['logout'])) {
                                                    $logout_query = "update login_activity set logout_time=current_timestamp where login_time='$login_time';";
                                                    $logout_output = $conn->query($logout_query);
                                                    unset($_SESSION['admin_token_id']);
                                                    header("location:admin_login.php");
                                                }
                                                //fetching user details to display in profile 
                                                $user_details_query = "select * from admin_details where token_id='$token_id';";
                                                $output = $conn->query($user_details_query);
                                                if ($output->num_rows > 0) {
                                                    $result = $output->fetch_assoc();
                                                }
                                                //when edit button is not clicked
                                                if (!isset($_POST['edit'])) {

                                                ?>
                                                    <div class="user_details">
                                                        <form action="admin_dashboard.php?page=Admin" enctype="multipart/form-data" method="post">
                                                            <input type="submit" name="edit" value="Edit">
                                                            <input type="submit" name="logout" value="Log out"><br><br>
                                                            <label for="emp_id">emp_id</label>
                                                            <input type="text" id="emp_id" name="emp_id" value="<?= $result['emp_id'] ?>" readonly><br><br>
                                                            <label for="email">Email</label>
                                                            <input type="text" id="email" name="email" value="<?= $result['email'] ?>" readonly><br><br>
                                                            <label for="name">Name</label>
                                                            <input type="text" id="name" name="name" value="<?= $result['name'] ?>" readonly><br><br>
                                                            <label for="username">Username</label>
                                                            <input type="text" id="username" name="username" value="<?= $result['username'] ?>" readonly><br><br>
                                                            <label for="dob">Date of birth</label>
                                                            <input type="text" id="dob" name="dob" value="<?= dateconvertion($result['date_of_birth'], "d-m-y") ?>" readonly><br><br>
                                                            <label for="role">Role</label>
                                                            <input type="text" id="role" name="role" value="<?= $result['role'] ?>" readonly><br><br>
                                                            <label for="cell_number">Mobile number</label>
                                                            <input type="text" id="cell_number" name="cell_number" value="<?= $result['phone_no'] ?>" readonly><br><br>
                                                        </form>
                                                    </div>
                                                <?php
                                                }
                                                //when edit button is clicked
                                                if (isset($_POST['edit'])) {
                                                ?>
                                                    <div class="user_details">
                                                        <form action="admin_dashboard.php?page=Admin" enctype="multipart/form-data" method="post">
                                                            <input type="file" name="file" id="fileInput" style="display: none;">
                                                            <label for="fileInput" style="color:rgb(114, 98, 255)">Update profile
                                                                picture</label>
                                                            <input type="submit" name="save" value="Save"><br><br>
                                                            <label for="name">Name</label>
                                                            <input type="text" id="name" name="name" value="<?= $result['name'] ?>"><br><br>
                                                            <label for="dob">Date of birth</label>
                                                            <input type="text" id="dob" name="dob" value="<?= $result['date_of_birth'] ?>"><br><br>
                                                            <label for="cell_number">Mobile number</label>
                                                            <input type="text" id="cell_number" name="cell_number" value="<?= $result['phone_no'] ?>"><br><br>
                                                        </form>
                                                    </div>
                                            </div>
                                        <?php
                                                }

                                        ?>

                                        <?php
                                        //saves the changes made in profile
                                        if (isset($_POST['save'])) {
                                            $name = !empty($_POST['name']) ? sanitizing($_POST['name']) : "";
                                            $dob = !empty($_POST['dob']) ? sanitizing($_POST['dob']) : "";
                                            $dob = dateconvertion($dob, "y-m-d");
                                            $phone_number = !empty($_POST['cell_number']) ? sanitizing($_POST['cell_number']) : "";
                                            if (!empty($_POST)) {
                                                $update_details = "update admin_details set name='$name', date_of_birth='$dob', phone_no='$phone_number', updated_on = current_timestamp where email='{$result['email']}';";
                                                $conn->query($update_details);
                                            }
                                        }
                                        break;
                                        //access page code
                                    case 'Access':
                                        $_SESSION['current_page'] = "Access";
                                        ?>
                                        <div class="access-container">
                                            <div class="access-content">
                                                <?php
                                                //checks if admin has access to this page
                                                //1 means access is given 0 means access restricted
                                                require_once "../Admin/admin_access_options.php";
                                                if ($admin_details['access_page'] == 1) {
                                                ?>
                                                    <!--access options-->

                                                    <div class="access-options">
                                                        <div class="search">
                                                            <form action="admin_dashboard.php" method="get">
                                                                <input type="search" name="admin_access_search" placeholder="EST001" value=<?= isset($_GET['admin_access_search']) ? $_GET['admin_access_search'] : "" ?>>
                                                                <input type="submit" name="admin_access_search-btn" value="Search">
                                                            </form>
                                                        </div>
                                                        <!--grant access and restrict access buttons-->
                                                        <div class="grant-access">
                                                            <form action="admin_dashboard.php?page=Access&page_no=<?= $page_no ?>" method="post" id="access_permission">
                                                                <input type="submit" name="grant_access" value="Grant Access">
                                                                <input type="submit" name="restrict_access" value="Restrict Access">
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <!--displays the table headers-->
                                                    <div class="user_table">
                                                        <table class="user_list">
                                                            <tr style="text-align: center;color:white; background:hsla(246, 100%, 73%, 1);box-shadow:3px 3px 6px rgb(215, 212, 255);">
                                                                <th>EMP ID</th>
                                                                <th>USERNAME</th>
                                                                <th>NAME</th>
                                                                <th>IBOX DASHBOARD</th>
                                                                <th>ADMIN LIST</th>
                                                                <th>USER LIST</th>
                                                                <th>LOGIN ACTIVITY</th>
                                                                <th>ACCESS PAGE </th>
                                                            </tr>
                                                            <!--displays the admin name and checkboxes to give access to each page-->
                                                            <?php
                                                            if (isset($_GET['admin_access_search'])) {
                                                                $admin_search_content =  !empty($_GET['admin_access_search']) ? sanitizing($_GET['admin_access_search']) : "";
                                                                $admin_search_query = "select * from admin_details where role='admin' and (username like '%$admin_search_content%' or email like '%$admin_search_content%' or name like '%$admin_search_content%')";
                                                                $admin_search_output = $conn->query($admin_search_query);
                                                                if ($admin_search_output->num_rows > 0) {
                                                                    admin_access($page, $admin_search_query);
                                                                } else {
                                                                    admin_access($page);
                                                                }
                                                            } else {
                                                                admin_access($page);
                                                            }
                                                            ?>
                                                        </table>
                                                        </form>
                                                    <?php
                                                } else {
                                                    ?>
                                                        <!--if admin does not have access this part id displayed-->
                                                        <div class="restricted-access">
                                                            <h3>Access restricted</h3>
                                                            <img src="../images/lock.jpg" alt="lock">
                                                        </div>
                                                    <?php
                                                }
                                                    ?>
                                                    </div>
                                            </div>
                                        <?php
                                        break;
                                    case 'Queries':
                                        $_SESSION['current_page'] = "Queries";
                                        $option = isset($_GET['option']) ? sanitizing($_GET['option']) : "";
                                        ?>
                                            <div class="dashboard-container">
                                                <div class="dashboard-content">
                                                    <div class="access-options">
                                                        <div class="search">
                                                            <form action="admin_dashboard.php" method="get">
                                                                <input type="search" name="query_no" placeholder="1" value="<?= isset($_GET['query_no']) ? $_GET['query_no'] : "" ?>">
                                                                <input type="submit" name="query_search_btn" value="Search">
                                                            </form>
                                                        </div>

                                                        <?php
                                                        switch ($option) {

                                                            case 'Solved':
                                                        ?>
                                                                <div>
                                                                    <form action="admin_dashboard.php?page=Queries&option=<?= $option ?>&page_no=<?= $page_no ?>" method="post" id="query_status">
                                                                        <input type="submit" name="reviewed" value="Reviewed">
                                                                    </form>
                                                                </div>
                                                    </div>
                                                    <div class="user_table">
                                                        <table class="user_list">
                                                        <?php
                                                                $_SESSION['current_option'] = "Solved";
                                                                $solved_issue_query = "select * from user_queries where assigned_to='$username' and status='REVIEWED' ";
                                                                user_query_search($solved_issue_query);
                                                                break;
                                                            case 'Unsolved':
                                                        ?>
                                                            <div>
                                                                <form action="admin_dashboard.php?page=Queries&option=<?= $option ?>&page_no=<?= $page_no ?>" method="post" id="query_status">
                                                                    <input type="submit" name="reviewed" value="Reviewed">
                                                                </form>
                                                            </div>
                                                    </div>
                                                    <div class="user_table">
                                                        <table class="user_list">
                                                        <?php
                                                                $_SESSION['current_option'] = "Unsolved";
                                                                $unsolved_issue_query = "select * from user_queries where assigned_to='$username' and status='NOT REVIEWED' ";
                                                                user_query_search($unsolved_issue_query);
                                                                break;
                                                        }
                                                        if ($admin_details['role'] == "superadmin") {
                                                        ?>
                                                        <div>
                                                            <form action="admin_dashboard.php?page=Queries&page_no=<?= $page_no ?>" method="post" id="query_status">
                                                                <select name="admins" id="admins">
                                                                    <option value="none" selected disabled hidden>ADMINS</option>
                                                                    <?php
                                                                    $admin_fetch_query = "select username from admin_details;";
                                                                    $admin_output = $conn->query($admin_fetch_query);
                                                                    if ($admin_output->num_rows > 0) {
                                                                        while ($admin_result = $admin_output->fetch_assoc()) {
                                                                            echo '<label for="admins">Assign To</label>';
                                                                            echo '<option value="' . $admin_result['username'] . '">' . $admin_result['username'] . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <input type="submit" name="assign" value="ASSIGN">
                                                                <input type="submit" name="reviewed" value="Reviewed">
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="user_table">
                                                        <table class="user_list">
                                                <?php
                                                            user_query_search();
                                                            $checkbox_value = !empty($_POST['query_status']) ? $_POST['query_status'] : array();
                                                            if (isset($_POST['assign']) && !empty($_POST['admins'])) {
                                                                $assign_to = sanitizing($_POST['admins']);
                                                                foreach ($checkbox_value as $id) {
                                                                    $assign_query = "update user_queries set assigned_by='{$admin_details['username']}',assigned_to='$assign_to',assigned_on=current_timestamp where id='$id' ;";
                                                                    $conn->query($assign_query);
                                                                }
                                                            }
                                                        }
                                                        break;
                                                }
                                                ?>
                                                    </div>
                                                </div>
                                            </div>
</body>

</html>