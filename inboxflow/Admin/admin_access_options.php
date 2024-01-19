<?php
//grant access submit function
if (isset($_POST['grant_access'])) {
    //checks for ticked checkbox and assigns the value
    $ibox_access = isset($_POST['ibox_access']) ? $_POST['ibox_access'] : array();
    $admin_list_access = isset($_POST['admin_list_access']) ? $_POST['admin_list_access'] : array();
    $user_list_access = isset($_POST['user_list_access']) ? $_POST['user_list_access'] : array();
    $login_activity_access = isset($_POST['login_activity_access']) ? $_POST['login_activity_access'] : array();
    $access_page_access = isset($_POST['access_page_access']) ? $_POST['access_page_access'] : array();
    //gives access to ibox dashboard
    if (!empty($ibox_access)) {
        foreach ($ibox_access as $token) {
            $access_update_query = "update admin_details set ibox_dashboard=1, access_given_by='{$admin_details['username']}' where emp_id='$token';";
            $access_update_output = $conn->query($access_update_query);
        }
    }
    //gives access to admin list
    if (!empty($admin_list_access)) {
        foreach ($admin_list_access as $token) {
            $access_update_query = "update admin_details set admin_list=1, access_given_by='{$admin_details['username']}' where emp_id='$token';";
            $access_update_output = $conn->query($access_update_query);
        }
    }
    //gives access to user list
    if (!empty($user_list_access)) {
        foreach ($user_list_access as $token) {
            $access_update_query = "update admin_details set user_list=1, access_given_by='{$admin_details['username']}' where emp_id='$token';";
            $access_update_output = $conn->query($access_update_query);
        }
    }
    //gives access to login activity
    if (!empty($login_activity_access)) {
        foreach ($login_activity_access as $token) {
            $access_update_query = "update admin_details set login_activity=1, access_given_by='{$admin_details['username']}' where emp_id='$token';";
            $access_update_output = $conn->query($access_update_query);
        }
    }
    //gives access to access page
    if (!empty($access_page_access)) {
        foreach ($access_page_access as $token) {
            $access_update_query = "update admin_details set access_page=1, access_given_by='{$admin_details['username']}' where emp_id='$token';";
            $access_update_output = $conn->query($access_update_query);
        }
    }
}
if (isset($_POST['restrict_access'])) {
    //checks for ticked checkbox and assigns the value
    $ibox_access = isset($_POST['ibox_access']) ? $_POST['ibox_access'] : array();
    $admin_list_access = isset($_POST['admin_list_access']) ? $_POST['admin_list_access'] : array();
    $user_list_access = isset($_POST['user_list_access']) ? $_POST['user_list_access'] : array();
    $login_activity_access = isset($_POST['login_activity_access']) ? $_POST['login_activity_access'] : array();
    $access_page_access = isset($_POST['access_page_access']) ? $_POST['access_page_access'] : array();
    //restricts access to ibox dashboard
    if (!empty($ibox_access)) {
        foreach ($ibox_access as $token) {
            $access_update_query = "update admin_details set ibox_dashboard=0, access_given_by='{$admin_details['username']}' where emp_id='$token';";
            $access_update_output = $conn->query($access_update_query);
        }
    }
    //restricts access to admin list
    if (!empty($admin_list_access)) {
        foreach ($admin_list_access as $token) {
            $access_update_query = "update admin_details set admin_list=0, access_given_by='{$admin_details['username']}' where emp_id='$token';";
            $access_update_output = $conn->query($access_update_query);
        }
    }
    //restricts access to user list
    if (!empty($user_list_access)) {
        foreach ($user_list_access as $token) {
            $access_update_query = "update admin_details set user_list=0, access_given_by='{$admin_details['username']}' where emp_id='$token';";
            $access_update_output = $conn->query($access_update_query);
        }
    }
    //restricts access to login activity
    if (!empty($login_activity_access)) {
        foreach ($login_activity_access as $token) {
            $access_update_query = "update admin_details set login_activity=0, access_given_by='{$admin_details['username']}' where emp_id='$token';";
            $access_update_output = $conn->query($access_update_query);
        }
    }
    //restricts access to access page
    if (!empty($access_page_access)) {
        foreach ($access_page_access as $token) {
            $access_update_query = "update admin_details set access_page=0, access_given_by='{$admin_details['username']}' where emp_id='$token';";
            $access_update_output = $conn->query($access_update_query);
        }
    }
}
if (isset($_GET['query_no'])) {
    $query_search =  !empty($_GET['query_no']) ? sanitizing($_GET['query_no']) : '';
    $admin_search_query = "select * from user_queries where id like '%$query_search%'";
    $admin_search_output = $conn->query($admin_search_query);
    if ($admin_search_output->num_rows > 0) {
        user_query($page, $admin_search_query);
    } else {
        user_query($page);
    }
} elseif (isset($_GET['id'])) {
    $id = sanitizing($_GET['id']);
    $get_complaint_query = "select * from user_queries where id='$id';";
    $get_complaint_output = $conn->query($get_complaint_query);
    if ($get_complaint_output->num_rows > 0) {
        $complaint = $get_complaint_output->fetch_assoc();
    }
}
