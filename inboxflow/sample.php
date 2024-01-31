<?php
// $a=strtoupper("d1c2947a254c4cdfad04345a9fb807dc");
// $b=strtoupper(substr('0xd1c2947a254c4cdfad04345a9fb807dc',2));
// if($a==$b){
//     echo "true";
// }else{
//     echo "false";
// }
// $link = "";
// $page = $_GET['page']; // your current page
// $pages = 13; // Total number of pages
// $limit = 5; // May be what you are looking for
// if ($pages >= 1 && $page <= $pages) {
//     $counter = 1;
//     $link = "";
//     if ($page > ($limit / 2)) {
//         $link .= "<a href=\"?page=1\">1 </a> ... ";
//     }
//     for ($x = $page; $x <= $pages; $x++) {
//         if ($counter < $limit)
//             $link .= "<a href=\"?page=" . $x . "\">" . $x . " </a>";
//         $counter++;
//     }
//     if ($page < $pages - ($limit / 2)) {
//         $link .= "... " . "<a href=\"?page=" . $pages . "\">" . $pages . " </a>";
//     }
// }
// echo $link;

$limit = 4;
$page = isset($_GET['page_no']) ? $_GET['page_no'] : 1;
$pages = $pagination_output[1];
if ($pages >= 1 && $page <= $pages) {
    $counter = 1;
    if ($page > ($limit / 2)) {
        echo '<button ><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=1">1 </a></button>...';
    }
    for ($page_no = $page; $page_no <= $pagination_output[1]; $page_no++) {
        if ($counter < $limit) {
            echo '<button ><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . $page_no . '">' .  $page_no . ' </a></button>';
        }
        $counter++;
    }
    if ($page < $pages - ($limit / 2)) {
        echo '...<button ><a href = "admin_dashboard.php?' . $query_parameter . '&page_no=' . $pagination_output[1] . '">' .  $pagination_output[1] . ' </a></button>';
    }
}

