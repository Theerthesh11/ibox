<?php
require_once "../config.php";
$user_details_query = "select * from user_details where token_id='$token_id'";
$output = $conn->query($user_details_query);
if ($output->num_rows > 0) {
    $result = $output->fetch_assoc();
    echo "<div>";
    if ($result['profile_status'] == 0) {
        echo '<a href="?page=User">';
        echo '<img src="' . $result['profile_path'] . '".jpg>';
    } else {
        echo "<a href=\"?page=User\">";
        echo "<img src='../Uploads/profiledefault.jpg'>";
    }
    echo "</a>";
    echo "</div>";
}
if (isset($_POST['save'])) {
    if (!empty($_FILES['file'])) {
        $file = $_FILES['file'];
        $file_array = array("file_name" => $file['name'], "file_type" => $file['type'], "file_tmp_name" => $file['tmp_name'], "file_error" => $file['error'], "file_size" => $file['size']);
        $file_ext = explode(".", $file_array["file_name"]);
        $file_actual_ext = strtolower(end($file_ext));
        $allowed_ext = array('jpg', 'jpeg', 'png', 'PNG', 'JPEG', 'JPG');
        if (in_array($file_actual_ext, $allowed_ext)) {
            if ($file_array['file_error'] == 0) {
                if ($file_array['file_size'] < 10000000) {
                    // Generate a unique name for the cropped image
                    $file_new_name = "profile" . bin2hex($token_id) . ".jpg";
                    // Set the path to save the uploaded image
                    $uploadedImagePath = "../Uploads/user_profiles/$file_new_name";
                    // Move the uploaded file to the specified path
                    move_uploaded_file($file_array['file_tmp_name'], $uploadedImagePath);
                    // Load the uploaded image
                    $originalImage = imagecreatefromjpeg($uploadedImagePath);
                    // Set the dimensions for WhatsApp profile picture (usually 640x640)
                    $whatsappSize = 640;
                    // Create a new square image with WhatsApp profile picture size
                    $newImage = imagecreatetruecolor($whatsappSize, $whatsappSize);
                    // Crop the original image to fit into the square
                    $originalWidth = imagesx($originalImage);
                    $originalHeight = imagesy($originalImage);
                    $cropSize = min($originalWidth, $originalHeight);
                    imagecopyresampled($newImage, $originalImage, 0, 0, ($originalWidth - $cropSize) / 2, ($originalHeight - $cropSize) / 2, $whatsappSize, $whatsappSize, $cropSize, $cropSize);
                    // Set the path to save the cropped image
                    $croppedImagePath = "../Uploads/user_profiles/cropped_$file_new_name";
                    // Save the cropped image
                    imagejpeg($newImage, $croppedImagePath);
                    // Free up memory
                    imagedestroy($originalImage);
                    imagedestroy($newImage);
                    // Update the database with the cropped image path
                    $image_query = "update user_details set profile_status=0,profile_path='$croppedImagePath' where token_id='$token_id'";
                    $image_query_output = $conn->query($image_query);
                    // Redirect to another page (you may change this as needed)
                    header("location:email.php?page=User");
                } else {
                    echo "Please upload a picture less than 1mb";
                }
            } else {
                echo "Upload unsuccessful!";
            }
        } else {
            echo "Image format must be jpg, jpeg, png";
        }
    }
}
?>
