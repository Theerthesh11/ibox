<?php
//fetches data according to search content
// if (isset($_POST['search-btn'])) {
//     if (!empty($_POST['search'])) {
//         $search_content = sanitizing($_POST['search']);
//         $search_query = "select * from admin_details where username like '%$search_content%'";
//         admin_details($page, $search_query);
//     }
// }
if (isset($_POST['activity-search-btn'])) {
    if (!empty($_POST['from_date']) && !empty(['to_date'])) {
        $from_date = sanitizing($_POST['from_date']);
        $to_date = isset($_POST['to_date']) ? sanitizing($_POST['to_date']) : "now()";
        $activity_search_query = "select * from login_activity where login_time between '$from_date' and '$to_date';";
        $activity_search_output = $conn->query($activity_search_query);
        if ($search_output->num_rows > 0) {
            echo "<div class=\"user_table\"><table>";
            echo "</table></div>";
        } else {
            echo '<div class="user-list"><div class="admin-alert-message"><p>No results found</p></div>';
        }
    } else {
        // echo "<div class=\"admin-alert-message\"><p>Type something to search..</p></div>";
    }
}
