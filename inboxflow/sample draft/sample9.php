<?php
require_once "../email_function.php";
$token_id=random(8) . "29" . random(2) . "4" . random(3) . random_byte() . random(3) . random(14) . "23";
$token_id_2=hex2bin($token_id);
echo $token_id_2;
?>
