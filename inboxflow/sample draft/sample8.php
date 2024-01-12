<?php
function custom_record_display($days,$page)
{
    require "config.php";
    $custom_record_query = "select * from mail_list where date_of_sending between DATE_SUB(NOW(), INTERVAL $days DAY) AND CURDATE();";
    $custom_record_output = $conn->query($custom_record_query);
    login_activity($page,$custom_record_query);
}
