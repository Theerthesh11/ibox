<?php
$a=strtoupper("d1c2947a254c4cdfad04345a9fb807dc");
$b=strtoupper(substr('0xd1c2947a254c4cdfad04345a9fb807dc',2));
if($a==$b){
    echo "true";
}else{
    echo "false";
}