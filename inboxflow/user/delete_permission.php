<?php
// require "config.php";
if (isset($_GET['option'])) {
    if ($user_details_result['trash_delete'] == 'yes' && $_GET['option'] == 'Trash') {
        //if option is trash displays delete when admin gives acces to delete button
        echo '<input type="submit" name="delete" value="Delete">';
    }else{
        echo '<input type="submit" name="mark_as_read" value="Mark as read" style="width: 100px;">';
    }
}else{
    echo '<input type="submit" name="mark_as_read" value="Mark as read" style="width: 100px;">';
}   
    