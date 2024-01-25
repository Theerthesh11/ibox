<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "../config.php";
//this function decides whether the html input type is readonly or hidden
function readonly_or_hidden($column1, $value1, $value2)
{
    if ($column1 == $value1) {
        $readonly = $value2;
    } else {
        $readonly = "";
    }
    return $readonly;
}

function readonly($column_name)
{
    if (!empty($column_name)) {
        $readonly = "readonly";
    } else {
        $readonly = "";
    }
    return $readonly;
}
//function for pagination
function pagination($query, $order_part = "", int $results_per_page = 10)
{
    global $conn;
    $result = $conn->query($query);
    $number_of_result = $result->num_rows;
    $number_of_page = ceil($number_of_result / $results_per_page);
    if (!isset($_GET['page_no'])) {
        $page_no = 1;
    } else {
        $page_no = $_GET['page_no'];
    }
    $page_first_result = ($page_no - 1) * $results_per_page;
    $pagination_query = "$query $order_part LIMIT " . $page_first_result . ',' . $results_per_page;
    $pagination_output = $conn->query($pagination_query);
    $output_array = array($pagination_output, $number_of_page, $page_no);
    return $output_array;
}
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
function user_list($page, $query = "select * from user_details")
{
    $pagination_result = pagination($query, "order by created_on");
    while ($user_details_result = $pagination_result[0]->fetch_assoc()) {
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
            <form method='post' action='../Admin/admin_dashboard.php?page=<?= $page ?>&page_no=<?= $pagination_result[2] ?>'>
                <input type='hidden' name='record_id' value="<?= bin2hex($user_details_result['token_id']) ?>">
                <!-- <input type='submit' name='view_count' value='View count'> -->
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
        echo '</table></div>';
        if ($pagination_result[1] > 1) {
            echo '<div class="admin_page_numbers">';
            echo '<button style="width:40px"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=1"><<</a></button>';
            for ($page_no = 1; $page_no <= $pagination_result[1]; $page_no++) {
                echo '<button><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
            }
            echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . ($page_no - 1) . '">>>  </a></button>';
            echo "</div><br>";
        }
    }

    //this function returns admin details
    function admin_details($query = "select * from admin_details")
    {
        $pagination_result = pagination($query, "order by created_on");
        while ($admin_details_result = $pagination_result[0]->fetch_assoc()) {
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
        if (isset($_GET['admin_search'])) {
            $query_parameter = 'admin_search=' . $_GET['admin_search'];
        } else {
            $query_parameter = 'page=Admin List';
        }
        echo '</table></div>';
        if ($pagination_result[1] > 1) {
            echo '<div class="admin_page_numbers">';
            echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=1"><<</a></button>';
            for ($page_no = 1; $page_no <= $pagination_result[1]; $page_no++) {
                echo '<button ><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
            }
            echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . ($page_no - 1) . '">>>  </a></button>';
            echo "</div><br>";
        }
    }
    //this function returns login activity of admin
    function login_activity($page, $query)
    {
        $pagination_output = pagination($query, "order by login_time desc");
        while ($login_activity_result =  $pagination_output[0]->fetch_assoc()) {
?>
    <tr>
        <td><?= $login_activity_result['emp_id'] ?></td>
        <td><?= $login_activity_result['username'] ?></td>
        <td><?= $login_activity_result['role'] ?></td>
        <td style="text-align:center;"><?= dateconvertion($login_activity_result['login_time'], "d M y h:i:s") ?></td>
        <td style="text-align:center;"><?= dateconvertion($login_activity_result['logout_time'], "d M y h:i:s") ?></td>
    <?php
        }
        if (isset($_GET['search'])) {
            $query_parameter = 'from_date=' . $_GET['from_date'] . '&to_date=' . $_GET['to_date'] . '&search=' . $_GET['search'];
        } else {
            $query_parameter = 'page=Login Activity';
        }
        echo '</table></div>';
        if ($pagination_output[1] > 1) {
            echo '<div class="admin_page_numbers">';
            echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=1"><<</a></button>';
            for ($page_no = 1; $page_no <=  $pagination_output[1]; $page_no++) {
                echo '<button><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
            }
            echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . ($page_no - 1) . '">>>  </a></button>';
            echo "</div><br>";
        }
    }
    function user_login_activity($query)
    {
        $pagination_output = pagination($query);
        while ($user_login_result = $pagination_output[0]->fetch_assoc()) {
    ?>
    <tr>
        <td><?= $user_login_result['username'] ?></td>
        <td><?= $user_login_result['login_status'] ?></td>
        <td style="text-align:center;"><?= dateconvertion($user_login_result['login_time'], "d M y h:i:s") ?></td>
        <td style="text-align:center;"><?= dateconvertion($user_login_result['logout_time'], "d M y h:i:s") ?></td>
    </tr>
<?php
        }
        if (isset($_GET['user_log'])) {
            $query_parameter = 'username=' . $_GET['username'] . '&from_date=' . $_GET['from_date'] . '&to_date=' . $_GET['to_date'] . '&user_log=' . $_GET['user_log'];
        } else {
            $query_parameter = 'page=User log';
        }
        echo '</table></div>';
        if ($pagination_output[1] > 1) {
            echo '<div class="admin_page_numbers">';
            echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=1"><<</a></button>';
            for ($page_no = 1; $page_no <= $pagination_output[1]; $page_no++) {
                echo '<button><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
            }
            echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . ($page_no - 1) . '">>>  </a></button>';
            echo "</div><br>";
        }
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
    function admin_access($query = "select * from admin_details where role='admin'")
    {
        $pagination_output = pagination($query, "order by created_on desc");
        while ($admin_details_result =  $pagination_output[0]->fetch_assoc()) {
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
        if (isset($_GET['admin_access_search'])) {
            $query_parameter = 'admin_access_search=' . $_GET['admin_access_search'];
        } else {
            $query_parameter = 'page=Access';
        }
        echo '</table></div>';
        if ($pagination_output[1] > 1) {
            echo '<div class="admin_page_numbers">';
            echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=1"><<</a></button>';
            for ($page_no = 1; $page_no <= $pagination_output[1]; $page_no++) {
                echo '<button ><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
            }
            echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . ($page_no - 1) . '">>></a></button>';
            echo "</div><br>";
        }
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
        global $admin_details, $option;
        $pagination_output = pagination($query, "order by complaint_date desc");
        while ($user_query_result = $pagination_output[0]->fetch_assoc()) {
            if ($admin_details['role'] == "superadmin") {
                $href_path = '<a href="admin_dashboard.php?page=Queries&page_no=' . $pagination_output[2] . '&complaint_no=' . $user_query_result['complaint_no'] . '">';
            } else {
                $href_path = '<a href="admin_dashboard.php?page=Queries&option=' . $option . '&page_no=' . $pagination_output[2] . '&complaint_no=' . $user_query_result['complaint_no'] . '">';
            }
?>
    <tr style="text-align:center;">
        <td><input type="checkbox" name="query_status[]" value="<?= $user_query_result['complaint_no'] ?>" form="query_status" <?= readonly_or_hidden($user_query_result['status'], "Reviewed", "readonly") ?>></td>
        <td><?= $href_path ?><?= $user_query_result['username'] ?></a></td>
        <td><?= $href_path ?><?= long_sentence_to_short($user_query_result['user_complaint']) ?></td>
        <td><?= $href_path ?><?= $user_query_result['status'] ?></a></td>
        <td><?= $href_path ?><?= $user_query_result['assigned_to'] ?></a></td>
        <td><?= $href_path ?><?= $user_query_result['assigned_by'] ?></a></td>
        <td><?= $href_path ?><?= dateconvertion($user_query_result['assigned_on']) ?></a></td>
        <td><?= $href_path ?><?= dateconvertion($user_query_result['reviewed_on']) ?></a></td>
        <td><?= $href_path ?><?= dateconvertion($user_query_result['complaint_date']) ?></a></td>
    </tr>
    <?php
        }
        if (isset($_GET['query_no'])) {
            $query_parameter = 'query_no=' . $_GET['query_no'];
        } elseif ($admin_details['role'] == "admin") {
            $query_parameter = "page=Queries&option=$option";
        } elseif ($admin_details['role'] == "superadmin") {
            $query_parameter = 'page=Queries';
        }
        echo '</table></div>';
        if ($pagination_output[1] > 1) {
            echo '<div class="admin_page_numbers">';
            echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=1"><<</a></button>';
            for ($page_no = 1; $page_no <= $pagination_output[1]; $page_no++) {
                echo '<button ><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
            }
            echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . ($page_no - 1) . '">>></a></button>';
            echo "</div><br>";
        }
    }
    function user_query_search($user_complaint_query = "select * from user_queries")
    {
        global $conn, $username;
        if (isset($_GET['query_no'])) {
            $query_search =  !empty($_GET['query_no']) ? $_GET['query_no'] : '';
            $admin_search_query = "select * from user_queries where complaint_no like '%$query_search%'";
            $admin_search_output = $conn->query($admin_search_query);
            if ($admin_search_output->num_rows > 0) {
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
                user_query($admin_search_query);
            }
        } elseif (isset($_GET['complaint_no'])) {
            $complaint_no = sanitizing($_GET['complaint_no']);
            $get_complaint_query = "select * from user_queries where complaint_no='$complaint_no';";
            $get_complaint_output = $conn->query($get_complaint_query);
            if ($get_complaint_output->num_rows > 0) {
                $complaint = $get_complaint_output->fetch_assoc();
            }
    ?>
    <div>
        <div class="complaint-view-form">
            <div class="complaint-textbox">
                <input type="checkbox" value="<?= $complaint['complaint_no'] ?>" name="query_status[]" <?= readonly_or_hidden($complaint['status'], "Reviewed",  "hidden") ?> form="query_status" style="margin:0px 20px;">
                <label for="query_id">Complaint no</label>
                <input type="text" name="query_status[]" id="query_id" value="<?= $complaint['complaint_no'] ?>" readonly style="width: 37%;">
            </div>
            <div class="complaint-textbox">
                <label for="username" style="padding: 8px 16px;">Username</label>
                <input type="text" name="username" id="username" value="<?= $complaint['username'] ?>" readonly>
            </div>
            <div class="complaint-textbox">
                <label for="date" style="padding: 8px 32px;">Date</label>
                <input type="text" name="complaint_date" id="date" value="<?= dateconvertion($complaint['complaint_date'], "d M y") ?>" readonly>
            </div>
        </div>
        <div class="complaint-view-form">
            <div class="complaint-textbox">
                <label for="assigned_by" style="padding: 8px 10px;">Assigned by</label>
                <input type="text" name="assigned_by" id="assigned_by" value="<?= $complaint['assigned_by'] ?>" readonly>
            </div>
            <div class="complaint-textbox">
                <label for="assigned_on" style="padding: 8px 10px;">Assigned on</label>
                <input type="text" name="assigned_on" id="assigned_on" value="<?= dateconvertion($complaint['assigned_on'], "d M y") ?>" readonly>
            </div>
            <div class="complaint-textbox">
                <label for="assigned_to" style="padding: 8px 11px;">Assigned to</label>
                <input type="text" name="assigned_to" id="assigned_to" value="<?= $complaint['assigned_to'] ?>" readonly>
            </div>
        </div>
        <div class="complaint-view-form">
            <div class="complaint-textbox">
                <label for="status" style="padding: 8px 27px;">Status</label>
                <select name="complaint_type" id="complaint_type" class="status-dropdown">
                    <option value="Reviewed">Reviewed</option>
                    <option value="Pending">Pending</option>
                    <option value="Processing">Processing</option>
                </select>
            </div>
            <div class="complaint-textbox">
                <label for="reviewed_on">Reviewed on</label>
                <input type="text" name="reviewed_on" id="reviewed_on" value="<?= dateconvertion($complaint['reviewed_on'], "d M y") ?>" readonly>
            </div>
            <div class="complaint-textbox">
                <label for="reviewed_by">Reviewed by</label>
                <input type="text" name="reviewed_by" id="reviewed_by" value="<?= $complaint['reviewed_by'] ?>" readonly>
            </div>
        </div>
        <div class="query">
            <table class="query" style="overflow-y: scroll;height:200px;">
                <tr>
                    <th>User Complaint</th>
                    <th>Support Reply</th>
                </tr>
                <tr>
                    <td><textarea name="user_complaint" readonly maxlength="100"><?= $complaint['user_complaint'] ?></textarea></td>
                    <td><textarea name="support_reply" maxlength="100"><?= $complaint['support_reply'] ?></textarea></td>
                </tr>
                <?php
                if ($complaint['user_reply_1'] != NULL || $complaint['support_reply_1'] != NULL) {
                ?>
                    <tr>
                        <td><textarea name="user_reply_1" readonly maxlength="100"><?= $complaint['user_reply_1'] ?></textarea></td>
                        <td><textarea name="support_reply_1" maxlength="100"><?= $complaint['support_reply_1'] ?></textarea></td>
                    </tr>
                <?php
                }
                if ($complaint['user_reply_2'] != NULL || $complaint['support_reply_2'] != NULL) {
                ?>
                    <tr>
                        <td><textarea name="user_reply_2" readonly maxlength="100"><?= $complaint['user_reply_2'] ?></textarea></td>
                        <td><textarea name="support_reply_2" maxlength="100"><?= $complaint['support_reply_2'] ?></textarea></td>
                    </tr>
                <?php
                }
                if ($complaint['user_reply_3'] != NULL || $complaint['support_reply_3'] != NULL) {
                ?>
                    <tr>
                        <td><textarea name="user_reply_3" readonly maxlength="100"><?= $complaint['user_reply_3'] ?></textarea></td>
                        <td><textarea name="support_reply_3" maxlength="100"><?= $complaint['support_reply_3'] ?></textarea></td>
                    </tr>
                <?php
                }
                if ($complaint['user_reply_4'] != NULL || $complaint['support_reply_4'] != NULL) {
                ?>
                    <tr>
                        <td><textarea name="user_reply_4" readonly maxlength="100"><?= $complaint['user_reply_4'] ?></textarea></td>
                        <td><textarea name="support_reply_4" maxlength="100"><?= $complaint['support_reply_4'] ?></textarea></td>
                    </tr>
                <?php
                }
                ?>
            </table>
            <br><br>
        </div>
        <div class="admin-comments">
            <label for="admin_comments">Comments</label>
            <textarea name="admin_comments" id="admin_comments"><?= $complaint['comments'] ?></textarea>
        </div>
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
        global $conn, $page_no, $username, $option, $admin_details;
        if (isset($_GET['query_no']) && $admin_details['role'] != "superadmin") {
            $query_search =  !empty($_GET['query_no']) ? $_GET['query_no'] : "z";
            $admin_search_query = "select * from user_queries where complaint_no like '%$query_search%' and assigned_to='$username'";
            $admin_search_output = $conn->query($admin_search_query);

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
            if ($admin_search_output->num_rows > 0) {
                user_query_admin_view($admin_search_query);
            } else {
                echo "<table>";
                alert_message("No results found");
            }
        } elseif (isset($_GET['complaint_no'])) {
            $complaint_no = sanitizing($_GET['complaint_no']);
            $get_complaint_query = "select * from user_queries where complaint_no='$complaint_no';";
            $get_complaint_output = $conn->query($get_complaint_query);
            if ($get_complaint_output->num_rows > 0) {
                $complaint = $get_complaint_output->fetch_assoc();
            }
    ?>
    <div>
        <form action="admin_dashboard.php?page=Queries&option=<?= $option ?>&page_no=<?= $page_no ?>&complaint_no=<?= $complaint_no ?>" method="post">
            <div class="access-options">
                <div class="back-btn">
                    <button><a href="<?php echo $_SERVER['HTTP_REFERER'] ?>">Back</a></button>
                </div>
                <div>
                    <input type="submit" name="reviewed" value="Reviewed">
                </div>
            </div>
            <div class="complaint-view-form">
                <div class="complaint-textbox">
                    <label for="query_id">Complaint No</label>
                    <input type="text" name="complaint_id" id="query_id" value="<?= $complaint['complaint_no'] ?>" readonly>
                    <input type="checkbox" value="<?= $complaint['complaint_no'] ?>" name="query_status[]" <?= readonly_or_hidden($complaint['status'], "Reviewed", "hidden") ?>>
                </div>
                <div class="complaint-textbox">
                    <label for="username" style="padding: 8px 17px;">Username</label>
                    <input type="text" name="username" id="username" value="<?= $complaint['username'] ?>" readonly>
                </div>
                <div class="complaint-textbox">
                    <label for="date" style="padding: 8px 36px;">Date</label>
                    <input type="text" name="complaint_date" id="date" value="<?= dateconvertion($complaint['complaint_date'], "d M y") ?>" readonly>
                </div>
            </div>
            <div class="complaint-view-form">
                <div class="complaint-textbox">
                    <label for="status" style="padding: 8px 27px;">Status</label>
                    <?php
                    if ($complaint['status'] != "Reviewed") { ?>
                        <select name="complaint_status" id="complaint_status" class="status-dropdown">
                            <option value="Reviewed">Reviewed</option>
                            <option value="Pending">Pending</option>
                            <option value="Processing">Processing</option>
                        </select>
                    <?php
                    } else {
                    ?>
                        <select name="complaint_status" id="complaint_status" class="status-dropdown">
                            <option value="<?= $complaint['status'] ?>"><?= $complaint['status'] ?></option>
                        </select>
                    <?php
                    }
                    ?>
                </div>
                <div class="complaint-textbox">
                    <label for="reviewed_on">Reviewed on</label>
                    <input type="text" name="reviewed_on" id="reviewed_on" value="<?= dateconvertion($complaint['reviewed_on'], "d M y") ?>" readonly>
                </div>
                <div class="complaint-textbox">
                    <label for="Complaint_sub">Complaint sub</label>
                    <input type="text" name="Complaint sub" id="Complaint_sub" value="<?= $complaint['complaint_type'] ?>" readonly>
                </div>
            </div>
            <div class="query">
                <table class="query" style="overflow-y: scroll;height:200px;">
                    <tr>
                        <th>User Complaint</th>
                        <th>Support Reply</th>
                    </tr>
                    <tr>
                        <td><textarea name="user_complaint" readonly maxlength="100"><?= $complaint['user_complaint'] ?></textarea></td>
                        <td><textarea name="support_reply" maxlength="100" <?= readonly($complaint['support_reply']) ?>><?= $complaint['support_reply'] ?></textarea></td>
                    </tr>
                    <?php
                    if ($complaint['user_reply_1'] != NULL || $complaint['support_reply_1'] != NULL) {
                    ?>
                        <tr>
                            <td><textarea name="user_reply_1" readonly maxlength="100"><?= $complaint['user_reply_1'] ?></textarea></td>
                            <td><textarea name="support_reply_1" maxlength="100" <?= readonly($complaint['support_reply_1']) ?>><?= $complaint['support_reply_1'] ?></textarea></td>
                        </tr>
                    <?php
                    }
                    if ($complaint['user_reply_2'] != NULL || $complaint['support_reply_2'] != NULL) {
                    ?>
                        <tr>
                            <td><textarea name="user_reply_2" readonly maxlength="100"><?= $complaint['user_reply_2'] ?></textarea></td>
                            <td><textarea name="support_reply_2" maxlength="100" <?= readonly($complaint['support_reply_2']) ?>><?= $complaint['support_reply_2'] ?></textarea></td>
                        </tr>
                    <?php
                    }
                    if ($complaint['user_reply_3'] != NULL || $complaint['support_reply_3'] != NULL) {
                    ?>
                        <tr>
                            <td><textarea name="user_reply_3" readonly maxlength="100"><?= $complaint['user_reply_3'] ?></textarea></td>
                            <td><textarea name="support_reply_3" maxlength="100" <?= readonly($complaint['support_reply_3']) ?>><?= $complaint['support_reply_3'] ?></textarea></td>
                        </tr>
                    <?php
                    }
                    if ($complaint['user_reply_4'] != NULL || $complaint['support_reply_4'] != NULL) {
                    ?>
                        <tr>
                            <td><textarea name="user_reply_4" readonly maxlength="100"><?= $complaint['user_reply_4'] ?></textarea></td>
                            <td><textarea name="support_reply_4" maxlength="100" <?= readonly($complaint['support_reply_4']) ?>><?= $complaint['support_reply_4'] ?></textarea></td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
                <br><br>
            </div>
            <div class="admin-comments">
                <label for="admin_comments">Comments</label>
                <textarea name="admin_comments" id="admin_comments"><?= $complaint['comments'] ?></textarea>
            </div>
        </form>
    </div>
<?php
        } elseif ($admin_details['role'] != "superadmin") {
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
        $status = isset($_POST['complaint_status']) ? $_POST['complaint_status'] : "";
        $checkbox_value = !empty($_POST['query_status']) ? $_POST['query_status'] : array();
        $admin_comments = !empty($_POST['admin_comments']) ? sanitizing($_POST['admin_comments']) : NULL;
        $support_reply = !empty($_POST['support_reply']) ? sanitizing($_POST['support_reply']) : NULL;
        $support_reply_1 = !empty($_POST['support_reply_1']) ? sanitizing($_POST['support_reply_1']) : NULL;
        $support_reply_2 = !empty($_POST['support_reply_2']) ? sanitizing($_POST['support_reply_2']) : NULL;
        $support_reply_3 = !empty($_POST['support_reply_3']) ? sanitizing($_POST['support_reply_3']) : NULL;
        $support_reply_4 = !empty($_POST['support_reply_4']) ? sanitizing($_POST['support_reply_4']) : NULL;
        if (isset($_POST['reviewed'])) {
            foreach ($checkbox_value as $id) {
                $reviewed_query = "update user_queries set reviewed_by='$username',reviewed_on=current_timestamp,status='$status', comments='$admin_comments',support_reply='$support_reply',support_reply_1='$support_reply_1',support_reply_2='$support_reply_2',support_reply_3='$support_reply_3',support_reply_4='$support_reply_4' where complaint_no='$complaint_no' and assigned_to='$username';";
                $conn->query($reviewed_query);
            }
        }
    }
    function user_query_admin_view($query = "select * from user_queries")
    {
        global $admin_details, $option;
        $pagination_output = pagination($query, "order by complaint_date desc");
        while ($user_query_result = $pagination_output[0]->fetch_assoc()) {
            if ($admin_details['role'] == "superadmin") {
                $href_path = '<a href="admin_dashboard.php?page=Queries&page_no=' . $pagination_output[2] . '&complaint_no=' . $user_query_result['complaint_no'] . '">';
            } else {
                $href_path = '<a href="admin_dashboard.php?page=Queries&option=' . $option . '&page_no=' . $pagination_output[2] . '&complaint_no=' . $user_query_result['complaint_no'] . '">';
            }
?>
    <tr style="text-align:center;">
        <td><input type="checkbox" name="query_status[]" value="<?= $user_query_result['complaint_no'] ?>" form="query_status" <?= readonly_or_hidden($user_query_result['status'], "Reviewed",  "readonly") ?>></td>
        <td><?= $href_path ?><?= $user_query_result['username'] ?></a></td>
        <td><?= $href_path ?><?= long_sentence_to_short($user_query_result['user_complaint']) ?></td>
        <td><?= $href_path ?><?= $user_query_result['status'] ?></a></td>
        <td><?= $href_path ?><?= long_sentence_to_short($user_query_result['comments']) ?></td>
        <td><?= $href_path ?><?= dateconvertion($user_query_result['assigned_on'], "d M y") ?></a></td>
        <td><?= $href_path ?><?= dateconvertion($user_query_result['reviewed_on'], "d M y") ?></a></td>
        <td><?= $href_path ?><?= dateconvertion($user_query_result['complaint_date'], "d M y") ?></a></td>
    </tr>
<?php
        }
        if (isset($_GET['query_no'])) {
            $query_parameter = 'query_no=' . $_GET['query_no'];
        } elseif ($admin_details['role'] == "admin") {
            $query_parameter = "page=Queries&option=$option";
        } elseif ($admin_details['role'] == "superadmin") {
            $query_parameter = 'page=Queries';
        }
        echo '</table></div>';
        if ($pagination_output[1] > 1) {
            echo '<div class="admin_page_numbers">';
            echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=1"><<</a></button>';
            for ($page_no = 1; $page_no <= $pagination_output[1]; $page_no++) {
                echo '<button ><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
            }
            echo '<button style="width:40px;"><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . ($page_no - 1) . '">>></a></button>';
            echo "</div><br>";
        }
    }

    function unsolved_count()
    {
        global $conn, $username;
        $unsolved_complaint_query = "select count(id) from user_queries where assigned_to='$username' and (status='Pending' or status='Processing') ;";
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

    function long_sentence_to_short($result)
    {
        echo substr($result, 0, 25);
        if (strlen($result) > 25) {
            echo "...";
        }
    }
    function support_reply($username, $content, $admin_username)
    {
        global $conn;
        if (!empty($content)) {
            $get_user_name = "select name from user_details where username='$username'";
            $get_user_name_output = $conn->query($get_user_name);
            if ($get_user_name_output->num_rows > 0) {
                $result = $get_user_name_output->fetch_assoc();
                $user_name = $result['name'];
            }
            echo "Dear " . $user_name . ",\r\n";
            echo "     " . $content . "\r\n";
            echo "Thank you\r\n";
            $get_admin_name = "select name from admin_details where username='$admin_username'";
            $get_admin_name_output = $conn->query($get_admin_name);
            if ($get_admin_name_output->num_rows > 0) {
                $result = $get_admin_name_output->fetch_assoc();
                $admin_name = $result['name'];
            }
            echo $admin_name . "\r\n(Inboxflow support Team)";
        } else {
            return;
        }
    }

    function button_css($page_name)
    {
        if ((isset($_GET['page']) && $_GET['page'] === $page_name) || ($_SESSION['current_page'] == $page_name && !isset($_GET['page']))) {
            echo 'class="active"';
        } else {
            return;
        }
    }
