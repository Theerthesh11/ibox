<?php
$file_name = $display_result['attachment_name'];
$file_ext = explode('.', $file_name);
$file_actual_ext = end($file_ext);

if ($file_actual_ext == "txt" || $file_actual_ext == "TXT") {
    echo '<img src="../icons/txt.png" alt="txt">';
} elseif ($file_actual_ext == "pdf" || $file_actual_ext == "PDF") {
    echo '<img src="../icons/pdf.jpg" alt="pdf">';
} elseif ($file_actual_ext == "docx" || $file_actual_ext == "DOCX") {
    echo '<img src="../icons/docx.png" alt="docx">';
} elseif ($file_actual_ext == "jpeg" || $file_actual_ext == "JPEG") {
    echo '<img src="../icons/jpeg.png" alt="jpeg">';
} elseif ($file_actual_ext == "png" || $file_actual_ext == "PNG") {
    echo '<img src="../icons/png.png" alt="png">';
} elseif ($file_actual_ext == "jpg" || $file_actual_ext == "JPG") {
    echo '<img src="../icons/jpg.png" alt="jpg">';
}
?>
