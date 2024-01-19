<?php
require_once "config.php";
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_details_query = "select * from user_details;";
    $user_details_output = $conn->query($user_details_query);

    if ($user_details_output->num_rows > 0) {
        $users = array(); // Initialize an array to store users

        while ($user = $user_details_output->fetch_assoc()) {
            $users[] = $user; // Add each user to the array
        }

        echo json_encode($users); // Encode the array of users as JSON
    } else {
        http_response_code(404);
        echo json_encode(array('status' => 'error', 'message' => 'Not found'));
    }
} else {
    http_response_code(405);
    echo json_encode(array('status' => 'error', 'message' => 'Only GET requests are allowed'));
}
?>
