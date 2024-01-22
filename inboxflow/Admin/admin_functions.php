<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
require_once "../config.php";
function dateconvertion($numericdate, $output_format = "d M")
{
    if (!is_null($numericdate)) {
        $dateString = $numericdate;
        $date = new DateTime($dateString);
        $formattedDate = $date->format($output_format);
        return $formattedDate;
    }
    return;
}
//function returns the total mails sent using inboxflow
function total_ibox_mail($addition = "")
{
    // require "config.php";
    global $conn;
    $total_mail_query = "select count(id) from mail_list $addition;";
    $total_mail_output = $conn->query($total_mail_query);
    if ($total_mail_output->num_rows > 0) {
        $total_mail_result = $total_mail_output->fetch_assoc();
        echo $total_mail_result['count(id)'];
    }
}
//this function returns total number of users 
function total_users($addition = "")
{
    // require "config.php";
    global $conn;
    $total_user_query = "select count(id) from user_details $addition;";
    $total_user_output = $conn->query($total_user_query);
    if ($total_user_output->num_rows > 0) {
        $total_user_result = $total_user_output->fetch_assoc();
        echo $total_user_result['count(id)'];
    }
}
//this function returns total number of admins
function total_admins($addition = "")
{
    // require "config.php";
    global $conn;
    $total_admin_query = "select count(id) from admin_details $addition;";
    $total_admin_output = $conn->query($total_admin_query);
    if ($total_admin_output->num_rows > 0) {
        $total_admin_result = $total_admin_output->fetch_assoc();
        echo $total_admin_result['count(id)'];
    }
}

//this function returns user or admin details with pagination depending on condition
function user_list($page, $query = "select * from user_details", $search_content = '')
{
    global $conn;
    $results_per_page = 10;
    $result = $conn->query($query);
    $number_of_result = $result->num_rows;
    $number_of_page = ceil($number_of_result / $results_per_page);
    if (!isset($_GET['page_no'])) {
        $page_no = 1;
    } else {
        $page_no = $_GET['page_no'];
    }
    $page_first_result = ($page_no - 1) * $results_per_page;
    $user_details_query =  "$query ORDER BY created_on desc LIMIT " . $page_first_result . ',' . $results_per_page;
    $user_details_output = $conn->query($user_details_query);
    while ($user_details_result = $user_details_output->fetch_assoc()) {
?>
        <tr>
            <td style="width:5%;text-align:center;"><input type="checkbox" name="delete_access[]" value="<?= $user_details_result['username'] ?>" form="user_access"></td>
            <td style="width:18%;margin-left:20px;"><?= $user_details_result['name'] ?></td>
            <td style="width:18%;"><?= $user_details_result['email'] ?></td>
            <td style="width:11%;text-align:center"><?= $user_details_result['user_status'] ?></td>
            <td style="width:11%;text-align:center"><?= $user_details_result['phone_no'] ?></td>
            <td style="width:11%;text-align:center"><?= dateconvertion($user_details_result['created_on'], "d M y") ?></td>
            <td style="width:13%;text-align:center"><?= dateconvertion($user_details_result['last_login'], "d M y h:i") ?></td>
            <?php
            if (isset($_POST['view_count']) && $_POST['record_id'] == bin2hex($user_details_result['token_id'])) {
            ?>
                <td style="width: 8%;text-align:center"><?= total_mail("sender_email", $user_details_result['email'], "and mail_status='sent');");
                                                        total_mail("reciever_email", $user_details_result['email'], "and mail_status='sent');") ?></td>
        </tr>
    <?php
            } else {
    ?>
        <td style="width:8%;text-align:center">
            <form method='post' action='../Admin/admin_dashboard.php?page=<?= $page ?>&page_no=<?= $page_no ?>'>
                <input type='hidden' name='record_id' value="<?= bin2hex($user_details_result['token_id']) ?>">
                <input type='submit' name='view_count' value='View count'>
            </form>
        </td>
    <?php
            }
        }
        if (isset($_GET['user_list_search'])) {
            $query_parameter = 'user_list_search=' . $_GET['user_list_search'];
        } else {
            $query_parameter = 'page=User List';
        }
        echo '<div class="admin_page_numbers">';
        echo '<button style="width:40px"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=1"><<</a></button>';
        for ($page_no = 1; $page_no <= $number_of_page; $page_no++) {
            echo '<button ><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
        }
        echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . ($page_no - 1) . '">>>  </a></button>';
        echo "</div><br>";
    }

    //this function returns admin details
    function admin_details($page, $query = "select * from admin_details")
    {
        // require "config.php";
        global $conn;
        // $query = "select * from admin_details";
        $results_per_page = 10;
        $result = $conn->query($query);
        $number_of_result = $result->num_rows;
        $number_of_page = ceil($number_of_result / $results_per_page);
        if (!isset($_GET['page_no'])) {
            $page_no = 1;
        } else {
            $page_no = $_GET['page_no'];
        }
        $page_first_result = ($page_no - 1) * $results_per_page;
        $admin_details_query = "$query ORDER BY created_on desc LIMIT " . $page_first_result . ',' . $results_per_page;
        $admin_details_output = $conn->query($admin_details_query);
        if ($admin_details_output->num_rows > 0) {
            while ($admin_details_result = $admin_details_output->fetch_assoc()) {
    ?>
        <tr>
            <td style="text-align: center;"><input type="checkbox" name="admin_id[]" value="<?= $admin_details_result['emp_id'] ?>"></td>
            <td style="text-align: center;"><?= $admin_details_result['emp_id'] ?></td>
            <td><?= $admin_details_result['name'] ?></td>
            <td><?= $admin_details_result['email'] ?></td>
            <td style="text-align: center;"><?= $admin_details_result['role'] ?></td>
            <td style="text-align: center;"><?= $admin_details_result['phone_no'] ?></td>
            <td style="text-align: center;"><?= dateconvertion($admin_details_result['created_on'], "d M y") ?></td>
            <td style="text-align: center;"><?= dateconvertion($admin_details_result['last_login'], "d M y h:i") ?></td>
        </tr>
    <?php
            }
        }
        if (isset($_GET['admin_search'])) {
            $query_parameter = 'admin_search=' . $_GET['admin_search'];
        } else {
            $query_parameter = 'page=Access';
        }
        echo '<div class="admin_page_numbers">';
        echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=1"><<</a></button>';
        for ($page_no = 1; $page_no <= $number_of_page; $page_no++) {
            echo '<button ><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
        }
        echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . ($page_no - 1) . '">>>  </a></button>';
        echo "</div><br>";
    }
    //this function returns login activity of admin
    function login_activity($page, $query)
    {
        // require "config.php";
        global $conn;
        $results_per_page = 10;
        $result = $conn->query($query);
        $number_of_result = $result->num_rows;
        $number_of_page = ceil($number_of_result / $results_per_page);
        if (!isset($_GET['page_no'])) {
            $page_no = 1;
        } else {
            $page_no = $_GET['page_no'];
        }
        $page_first_result = ($page_no - 1) * $results_per_page;
        $login_activity_query = "$query ORDER BY login_time desc LIMIT " . $page_first_result . ',' . $results_per_page;
        $login_activity_output = $conn->query($login_activity_query);
        if ($login_activity_output->num_rows > 0) {
            while ($login_activity_result = $login_activity_output->fetch_assoc()) {
    ?>
        <tr>
            <td><?= $login_activity_result['emp_id'] ?></td>
            <td><?= $login_activity_result['username'] ?></td>
            <td><?= $login_activity_result['role'] ?></td>
            <td style="text-align:center;"><?= dateconvertion($login_activity_result['login_time'], "d M y h:i:s") ?></td>
            <td style="text-align:center;"><?= dateconvertion($login_activity_result['logout_time'], "d M y h:i:s") ?></td>
        <?php
            }
        } else {
            echo "No results found";
        }

        echo '<div class="admin_page_numbers">';
        echo '<button style="width:40px;"><a href = "admin_dashboard.php?page=' . $page . '&page_no=1"><<</a></button>';
        for ($page_no = 1; $page_no <= $number_of_page; $page_no++) {
            echo '<button><a href = "admin_dashboard.php?page=' . $page . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
        }
        echo '<button style="width:40px;"><a href = "admin_dashboard.php?page=' . $page . '&page_no=' . ($page_no - 1) . '">>>  </a></button>';
        echo "</div><br>";
    }
    function user_login_activity($page, $query)
    {
        global $conn;
        $results_per_page = 10;
        $result = $conn->query($query);
        $number_of_result = $result->num_rows;
        $number_of_page = ceil($number_of_result / $results_per_page);
        if (!isset($_GET['page_no'])) {
            $page_no = 1;
        } else {
            $page_no = $_GET['page_no'];
        }
        $page_first_result = ($page_no - 1) * $results_per_page;
        $user_login_query = "$query LIMIT " . $page_first_result . ',' . $results_per_page;
        $user_login_output = $conn->query($user_login_query);
        if ($user_login_output->num_rows > 0) {
            while ($user_login_result = $user_login_output->fetch_assoc()) {
        ?>
        <tr>
            <td><?= $user_login_result['username'] ?></td>
            <td><?= $user_login_result['login_status'] ?></td>
            <td style="text-align:center;"><?= dateconvertion($user_login_result['login_time'], "d M y h:i:s") ?></td>
            <td style="text-align:center;"><?= dateconvertion($user_login_result['logout_time'], "d M y h:i:s") ?></td>
        <?php
            }
        } else {
            echo "No results found";
        }

        echo '<div class="admin_page_numbers">';
        echo '<button style="width:40px;"><a href = "admin_dashboard.php?page=' . $page . '&page_no=1"><<</a></button>';
        for ($page_no = 1; $page_no <= $number_of_page; $page_no++) {
            echo '<button><a href = "admin_dashboard.php?page=' . $page . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
        }
        echo '<button style="width:40px;"><a href = "admin_dashboard.php?page=' . $page . '&page_no=' . ($page_no - 1) . '">>>  </a></button>';
        echo "</div><br>";
    }
    //this function returns individual sent and recieved mails
    function total_mail($column_name, $email, $addition = " ")
    {
        // require "config.php";
        global $conn;
        $count_query = "select count($column_name) from mail_list where ($column_name='$email' $addition ";
        $count_output = $conn->query($count_query);
        if ($count_output->num_rows > 0) {
            if ($result = $count_output->fetch_assoc()) {
                echo "<p>" . $result["count($column_name)"] . "</p>";
            } else {
                return 0;
            }
        } else {
            return;
        }
    }
    //this function displays admin access page to decide access to give
    function admin_access($page, $query = "select * from admin_details where role='admin'")
    {
        // require "config.php";
        global $conn;

        $results_per_page = 10;
        $result = $conn->query($query);
        $number_of_result = $result->num_rows;
        $number_of_page = ceil($number_of_result / $results_per_page);
        if (!isset($_GET['page_no'])) {
            $page_no = 1;
        } else {
            $page_no = $_GET['page_no'];
        }
        $page_first_result = ($page_no - 1) * $results_per_page;
        $admin_details_query = "$query ORDER BY created_on desc LIMIT " . $page_first_result . ',' . $results_per_page;
        $admin_details_output = $conn->query($admin_details_query);
        if ($admin_details_output->num_rows > 0) {
            while ($admin_details_result = $admin_details_output->fetch_assoc()) {
        ?>
        <tr style="text-align:center;">
            <td><?= $admin_details_result['emp_id'] ?></td>
            <td><?= $admin_details_result['username'] ?></td>
            <td><?= $admin_details_result['name'] ?></td>
            <td><input type="checkbox" name="ibox_access[]" value="<?= $admin_details_result['emp_id'] ?>" form="access_permission"></td>
            <td><input type="checkbox" name="admin_list_access[]" value="<?= $admin_details_result['emp_id'] ?>" form="access_permission"></td>
            <td><input type="checkbox" name="user_list_access[]" value="<?= $admin_details_result['emp_id'] ?>" form="access_permission"></td>
            <td><input type="checkbox" name="login_activity_access[]" value="<?= $admin_details_result['emp_id'] ?>" form="access_permission"></td>
            <td><input type="checkbox" name="access_page_access[]" value="<?= $admin_details_result['emp_id'] ?>" form="access_permission"></td>
        </tr>
    <?php
            }
        }
        if (isset($_GET['admin_access_search'])) {
            $query_parameter = 'admin_access_search=' . $_GET['admin_access_search'];
        } else {
            $query_parameter = 'page=Access';
        }
        echo '<div class="admin_page_numbers">';
        echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=1"><<</a></button>';
        for ($page_no = 1; $page_no <= $number_of_page; $page_no++) {
            echo '<button ><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
        }
        echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . ($page_no - 1) . '">>></a></button>';
        echo "</div><br>";
    }
    //function sanitizes the data 
    function sanitizing($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    //this function generates random alphanumeric value
    function random($length)
    {
        $result = substr(str_shuffle('1234567890ABCDEF'), 0, $length);
        return $result;
    }
    //this function generates a unique character from 89AB
    function random_byte()
    {
        return substr(str_shuffle('89AB'), 0, 1);
    }
    function getIPAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    function user_query($query = "select * from user_queries")
    {
        global $conn, $admin_details, $option;

        $results_per_page = 10;
        $result = $conn->query($query);
        $number_of_result = $result->num_rows;
        $number_of_page = ceil($number_of_result / $results_per_page);
        if (!isset($_GET['page_no'])) {
            $page_no = 1;
        } else {
            $page_no = $_GET['page_no'];
        }
        $page_first_result = ($page_no - 1) * $results_per_page;
        $user_query_query = "$query ORDER BY query_date desc LIMIT " . $page_first_result . ',' . $results_per_page;
        $user_query_output = $conn->query($user_query_query);
        if ($user_query_output->num_rows > 0) {
            while ($user_query_result = $user_query_output->fetch_assoc()) {
                if ($admin_details['role'] == "superadmin") {
                    $href_path = '<a href="admin_dashboard.php?page=Queries&page_no=' . $page_no . '&id=' . $user_query_result['id'] . '">';
                } else {
                    $href_path = '<a href="admin_dashboard.php?page=Queries&option=' . $option . '&page_no=' . $page_no . '&id=' . $user_query_result['id'] . '">';
                }
    ?>
        <tr style="text-align:center;">
            <?php
                if ($user_query_result['status'] == "REVIEWED") {
                    $readonly = "readonly";
                } else {
                    $readonly = "";
                }
            ?>
            <td><input type="checkbox" name="query_status[]" value="<?= $user_query_result['id'] ?>" form="query_status" <?= $readonly ?>></td>
            <td><?= $href_path ?><?= $user_query_result['username'] ?></a></td>
            <td><?= $href_path ?><?php
                                    echo substr($user_query_result['query'], 0, 25);
                                    if (strlen($user_query_result['query']) > 25) {
                                        echo "...";
                                    }
                                    ?></td>
            <td><?= $href_path ?><?= $user_query_result['status'] ?></a></td>
            <td><?= $href_path ?><?= $user_query_result['assigned_to'] ?></a></td>
            <td><?= $href_path ?><?= $user_query_result['assigned_by'] ?></a></td>
            <td><?= $href_path ?><?= dateconvertion($user_query_result['assigned_on']) ?></a></td>
            <td><?= $href_path ?><?= dateconvertion($user_query_result['reviewed_on']) ?></a></td>
            <td><?= $href_path ?><?= dateconvertion($user_query_result['query_date']) ?></a></td>
        </tr>
    <?php
            }
        }
        if (isset($_GET['query_no'])) {
            $query_parameter = 'query_no=' . $_GET['query_no'];
        } else {
            $query_parameter = 'page=Queries';
        }
        echo '<div class="admin_page_numbers">';
        echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=1"><<</a></button>';
        for ($page_no = 1; $page_no <= $number_of_page; $page_no++) {
            echo '<button ><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
        }
        echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . ($page_no - 1) . '">>></a></button>';
        echo "</div><br>";
    }
    function user_query_search($user_complaint_query = "select * from user_queries")
    {
        global $conn, $page_no, $username;
        if (isset($_GET['query_no'])) {
            $query_search =  !empty($_GET['query_no']) ? sanitizing($_GET['query_no']) : '';
            $admin_search_query = "select * from user_queries where id like '%$query_search%'";
            $admin_search_output = $conn->query($admin_search_query);
            if ($admin_search_output->num_rows > 0) {
                user_query($admin_search_query);
            } else {
                user_query($user_complaint_query);
            }
        } elseif (isset($_GET['id'])) {
            $id = sanitizing($_GET['id']);
            $get_complaint_query = "select * from user_queries where id='$id';";
            $get_complaint_output = $conn->query($get_complaint_query);
            if ($get_complaint_output->num_rows > 0) {
                $complaint = $get_complaint_output->fetch_assoc();
            }
    ?>
    <div>
        <form action="admin_dashboard.php?page=Queries&page_no=<?= $page_no ?>" method="post">
            <div class="complaint-view-form">
                <div class="complaint-textbox">
                    <label for="query_id">ID</label>
                    <input type="text" name="query_status[]" id="query_id" value="<?= $complaint['id'] ?>" readonly>
                </div>
                <div class="complaint-textbox">
                    <label for="username" style="padding: 8px 17px;">USERNAME</label>
                    <input type="text" name="username" id="username" value="<?= $complaint['username'] ?>" readonly>
                </div>
                <div class="complaint-textbox">
                    <label for="date" style="padding: 8px 36px;">DATE</label>
                    <input type="text" name="query_date" id="date" value="<?= dateconvertion($complaint['query_date'], "d M y") ?>" readonly>
                </div>
            </div>
            <div class="complaint-view-form">
                <div class="complaint-textbox">
                    <label for="assigned_by">ASSIGNED BY</label>
                    <input type="text" name="assigned_by" id="assigned_by" value="<?= $complaint['assigned_by'] ?>" readonly>
                </div>
                <div class="complaint-textbox">
                    <label for="assigned_on">ASSIGNED ON</label>
                    <input type="text" name="assigned_on" id="assigned_on" value="<?= dateconvertion($complaint['assigned_on'], "d M y") ?>" readonly>
                </div>
                <div class="complaint-textbox">
                    <label for="assigned_to">ASSIGNED TO</label>
                    <input type="text" name="assigned_to" id="assigned_to" value="<?= $complaint['assigned_to'] ?>" readonly>
                </div>
            </div>
            <div class="complaint-view-form">
                <div class="complaint-textbox">
                    <label for="status" style="padding: 8px 27px;">STATUS</label>
                    <input type="text" name="status" id="status" value="<?= $complaint['status'] ?>" readonly>
                </div>
                <div class="complaint-textbox">
                    <label for="reviewed_on">REVIEWED ON</label>
                    <input type="text" name="reviewed_on" id="reviewed_on" value="<?= dateconvertion($complaint['reviewed_on'], "d M y") ?>" readonly>
                </div>
                <div class="complaint-textbox">
                    <label for="reviewed_by">REVIEWED BY</label>
                    <input type="text" name="reviewed_by" id="reviewed_by" value="<?= $complaint['reviewed_by'] ?>" readonly>
                </div>
            </div>
            <div class="query">
                <label for="query">QUERY</label>
                <textarea name="complaint_query" id="query"><?= $complaint['query'] ?></textarea>
            </div>
        </form>
    </div>
<?php
        } else {
?>
    <tr style="text-align: center;color:white; background:hsla(246, 100%, 73%, 1);box-shadow:3px 3px 6px rgb(215, 212, 255);">
        <th style="width: 5%;"></th>
        <th style="width: 10%;">USERNAME</th>
        <th style="width: 25%;">QUERY</th>
        <th style="width: 10%;">STATUS</th>
        <th style="width: 10%;">ASSIGNED TO</th>
        <th style="width: 10%;">ASSIGNED BY</th>
        <th style="width: 10%;">ASSIGNED ON</th>
        <th style="width: 10%;">REVIEWED ON</th>
        <th style="width: 10%;">DATE</th>
    </tr>
<?php

            user_query($user_complaint_query);
        }
        $checkbox_value = !empty($_POST['query_status']) ? $_POST['query_status'] : array();
        if (isset($_POST['reviewed'])) {
            foreach ($checkbox_value as $id) {
                $reviewed_query = "update user_queries set reviewed_by='$username',reviewed_on=current_timestamp,status='REVIEWED' where id='$id' and assigned_to='$username';";
                $conn->query($reviewed_query);
            }
        }
    }
    function alert_message($message)
    {
        echo '<div class="alert-message"><p>' . $message . '</p></div>';
    }

    function admin_review_complaints($user_complaint_query = "select * from user_queries")
    {
        global $conn, $page_no, $username, $option;
        if (isset($_GET['query_no'])) {
            $query_search =  !empty($_GET['query_no']) ? sanitizing($_GET['query_no']) : '';
            $admin_search_query = "select * from user_queries where id like '%$query_search%'";
            $admin_search_output = $conn->query($admin_search_query);
            if ($admin_search_output->num_rows > 0) {
                user_query($admin_search_query);
            } else {
                user_query($user_complaint_query);
            }
        } elseif (isset($_GET['id'])) {
            $id = sanitizing($_GET['id']);
            $get_complaint_query = "select * from user_queries where id='$id';";
            $get_complaint_output = $conn->query($get_complaint_query);
            if ($get_complaint_output->num_rows > 0) {
                $complaint = $get_complaint_output->fetch_assoc();
            }
?>
    <div>
        <form action="admin_dashboard.php?page=Queries&option=<?= $option ?>&page_no=<?= $page_no ?>&id=<?= $id ?>" method="post">
            <div>
                <input type="submit" name="reviewed" value="Reviewed">
            </div>
            <div class="complaint-view-form">
                <div class="complaint-textbox">
                    <label for="query_id">ID</label>
                    <input type="text" name="complaint_id" id="query_id" value="<?= $complaint['id'] ?>" readonly>
                    <input type="checkbox" value="<?= $complaint['id'] ?>" name="query_status[]" style="margin: 0px 20px;">
                </div>
                <div class="complaint-textbox">
                    <label for="username" style="padding: 8px 17px;">USERNAME</label>
                    <input type="text" name="username" id="username" value="<?= $complaint['username'] ?>" readonly>
                </div>
                <div class="complaint-textbox">
                    <label for="date" style="padding: 8px 36px;">DATE</label>
                    <input type="text" name="query_date" id="date" value="<?= dateconvertion($complaint['query_date'], "d M y") ?>" readonly>
                </div>
            </div>
            <div class="complaint-view-form">
                <div class="complaint-textbox">
                    <label for="status" style="padding: 8px 27px;">STATUS</label>
                    <input type="text" name="status" id="status" value="<?= $complaint['status'] ?>" readonly>
                </div>
                <div class="complaint-textbox">
                    <label for="reviewed_on">REVIEWED ON</label>
                    <input type="text" name="reviewed_on" id="reviewed_on" value="<?= dateconvertion($complaint['reviewed_on'], "d M y") ?>" readonly>
                </div>
                <div class="complaint-textbox">
                    <label for="reviewed_by">REVIEWED BY</label>
                    <input type="text" name="reviewed_by" id="reviewed_by" value="<?= $complaint['reviewed_by'] ?>" readonly>
                </div>
            </div>
            <div class="query">
                <label for="query">QUERY</label>
                <textarea name="complaint_query" id="query" readonly><?= $complaint['query'] ?></textarea>
            </div><br><br>
            <div class="query">
                <label for="query">COMMENTS</label>
                <textarea name="admin_comments" id="query"><?= $complaint['comments'] ?></textarea>
            </div>
        </form>
    </div>
<?php
        } else {
?>
    <tr style="text-align: center;color:white; background:hsla(246, 100%, 73%, 1);box-shadow:3px 3px 6px rgb(215, 212, 255);">
        <th style="width: 5%;"></th>
        <th style="width: 10%;">USERNAME</th>
        <th style="width: 25%;">QUERY</th>
        <th style="width: 10%;">STATUS</th>
        <th style="width:20%;">COMMENTS</th>
        <th style="width: 10%;">ASSIGNED ON</th>
        <th style="width: 10%;">REVIEWED ON</th>
        <th style="width: 10%;">DATE</th>
    </tr>
    <?php

            user_query_admin_view($user_complaint_query);
        }
        $checkbox_value = !empty($_POST['query_status']) ? $_POST['query_status'] : array();
        $admin_comments = !empty($_POST['admin_comments']) ? sanitizing($_POST['admin_comments']) : NULL;
        if (isset($_POST['reviewed']) && !empty($admin_comments)) {
            foreach ($checkbox_value as $id) {
                $reviewed_query = "update user_queries set reviewed_by='$username',reviewed_on=current_timestamp,status='REVIEWED', comments='$admin_comments' where id='$id' and assigned_to='$username';";
                $conn->query($reviewed_query);
            }
        }
    }
    function user_query_admin_view($query = "select * from user_queries")
    {
        global $conn, $admin_details, $option;
        $results_per_page = 10;
        $result = $conn->query($query);
        $number_of_result = $result->num_rows;
        $number_of_page = ceil($number_of_result / $results_per_page);
        if (!isset($_GET['page_no'])) {
            $page_no = 1;
        } else {
            $page_no = $_GET['page_no'];
        }
        $page_first_result = ($page_no - 1) * $results_per_page;
        $user_query_query = "$query ORDER BY query_date desc LIMIT " . $page_first_result . ',' . $results_per_page;
        $user_query_output = $conn->query($user_query_query);
        if ($user_query_output->num_rows > 0) {
            while ($user_query_result = $user_query_output->fetch_assoc()) {
                if ($admin_details['role'] == "superadmin") {
                    $href_path = '<a href="admin_dashboard.php?page=Queries&page_no=' . $page_no . '&id=' . $user_query_result['id'] . '">';
                } else {
                    $href_path = '<a href="admin_dashboard.php?page=Queries&option=' . $option . '&page_no=' . $page_no . '&id=' . $user_query_result['id'] . '">';
                }
    ?>
        <tr style="text-align:center;">
            <?php
                if ($user_query_result['status'] == "REVIEWED") {
                    $readonly = "readonly";
                } else {
                    $readonly = "";
                }
            ?>
            <td><input type="checkbox" name="query_status[]" value="<?= $user_query_result['id'] ?>" form="query_status" <?= $readonly ?>></td>
            <td><?= $href_path ?><?= $user_query_result['username'] ?></a></td>
            <td><?= $href_path ?><?php
                                    echo substr($user_query_result['query'], 0, 25);
                                    if (strlen($user_query_result['query']) > 25) {
                                        echo "...";
                                    }
                                    ?></td>
            <td><?= $href_path ?><?= $user_query_result['status'] ?></a></td>
            <td><?= $href_path ?><?= $user_query_result['comments'] ?></td>
            <td><?= $href_path ?><?= dateconvertion($user_query_result['assigned_on'], "d M y") ?></a></td>
            <td><?= $href_path ?><?= dateconvertion($user_query_result['reviewed_on'], "d M y") ?></a></td>
            <td><?= $href_path ?><?= dateconvertion($user_query_result['query_date'], "d M y") ?></a></td>
        </tr>
<?php
            }
        }
        if (isset($_GET['query_no'])) {
            $query_parameter = 'query_no=' . $_GET['query_no'];
        } else {
            $query_parameter = 'page=Queries';
        }
        echo '<div class="admin_page_numbers">';
        echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=1"><<</a></button>';
        for ($page_no = 1; $page_no <= $number_of_page; $page_no++) {
            echo '<button ><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
        }
        echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . ($page_no - 1) . '">>></a></button>';
        echo "</div><br>";
    }

    function unsolved_count()
    {
        global $conn, $username;
        $unsolved_complaint_query = "select count(id) from user_queries where status='NOT REVIEWED' and assigned_to='$username';";
        $unsolved_complaint_output = $conn->query($unsolved_complaint_query);
        if ($unsolved_complaint_output->num_rows > 0) {
            $unsolved_complaint_result = $unsolved_complaint_output->fetch_assoc();
            if ($unsolved_complaint_result['count(id)'] == 0) {
                return;
            } else {
                return $unsolved_complaint_result['count(id)'];
            }
        }
    }
