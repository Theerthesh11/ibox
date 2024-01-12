<?php
require_once "config.php";
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_details_query = "select * from user_details";
    $user_details_output = $conn->query($user_details_query);
    if ($user_details_output->num_rows > 0) {
        while ($user = $user_details_output->fetch_assoc()) {
            echo json_encode($user);
        }
    } else {
        http_response_code(404); // Method Not Allowed
        echo json_encode(array('status' => 'error', 'message' => 'Not found'));
    }
} else {
    http_response_code(405);
    echo json_encode(array('status' => 'error', 'message' => 'Only GET requests are allowed'));
}
?>
