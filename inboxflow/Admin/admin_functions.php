<?php
require_once "../config.php";
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
    $user_details_query =  "$query ORDER BY created_on LIMIT " . $page_first_result . ',' . $results_per_page;
    $user_details_output = $conn->query($user_details_query);
    while ($user_details_result = $user_details_output->fetch_assoc()) {
?>
        <tr>
            <td style="width:5%;text-align:center;"><input type="checkbox" name="delete_access[]" value="<?= $user_details_result['username'] ?>" form="user_access"></td>
            <td style="width:18%;margin-left:20px;"><?= $user_details_result['name'] ?></td>
            <td style="width:18%;"><?= $user_details_result['email'] ?></td>
            <td style="width:11%;text-align:center"><?= $user_details_result['user_status'] ?></td>
            <td style="width:11%;text-align:center"><?= $user_details_result['phone_no'] ?></td>
            <td style="width:11%;text-align:center"><?= $user_details_result['created_on'] ?></td>
            <td style="width:13%;text-align:center"><?= $user_details_result['last_login'] ?></td>
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
        echo "</div><br><hr><br>";
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
        $admin_details_query = "$query LIMIT " . $page_first_result . ',' . $results_per_page;
        $admin_details_output = $conn->query($admin_details_query);
        if ($admin_details_output->num_rows > 0) {
            while ($admin_details_result = $admin_details_output->fetch_assoc()) {
    ?>
        <tr>
            <td style="text-align: center;"><?= $admin_details_result['emp_id'] ?></td>
            <td><?= $admin_details_result['name'] ?></td>
            <td><?= $admin_details_result['email'] ?></td>
            <td style="text-align: center;"><?= $admin_details_result['role'] ?></td>
            <td style="text-align: center;"><?= $admin_details_result['phone_no'] ?></td>
            <td style="text-align: center;"><?= $admin_details_result['date_of_birth'] ?></td>
            <td style="text-align: center;"><?= $admin_details_result['created_on'] ?></td>
            <td style="text-align: center;"><?= $admin_details_result['last_login'] ?></td>
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
        echo "</div><br><hr><br>";
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
        $login_activity_query = "$query LIMIT " . $page_first_result . ',' . $results_per_page;
        $login_activity_output = $conn->query($login_activity_query);
        if ($login_activity_output->num_rows > 0) {
            while ($login_activity_result = $login_activity_output->fetch_assoc()) {
    ?>
        <tr>
            <td><?= $login_activity_result['emp_id'] ?></td>
            <td><?= $login_activity_result['username'] ?></td>
            <td><?= $login_activity_result['role'] ?></td>
            <td><?= $login_activity_result['login_time'] ?></td>
            <td><?= $login_activity_result['logout_time'] ?></td>
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
        echo "</div><br><hr><br>";
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
        $admin_details_query = "$query LIMIT " . $page_first_result . ',' . $results_per_page;
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
        echo "</div><br><hr><br>";
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
