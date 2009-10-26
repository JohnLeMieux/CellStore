<?php
function redirect($url) {
    $url = trim($url);
    $absURL = "Location: ";
    if (substr($url, 0, 1) == "/") {
        $absURL .= "http://".$_SERVER['HTTP_HOST'];
    } elseif (strtolower(substr($url, 0, 7)) != "http://") {
        $absURL .= "http://".$_SERVER['HTTP_HOST'];
        $absURL .= dirname($_SERVER['PHP_SELF']);
    }
    $absURL .= $url;
    header($absURL);
    // Make sure nothing else happens
    die("Could not redirect");
}
?>
