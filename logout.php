<?php
/**
* logout.php
* Logout page removes session and cookie data
*
* @author Ed Parrish
* @author John Le Mieux
* @version 1.4 5/10/09
*/

ob_start();
if (!session_id()) session_start();

main("FestiveGear Logout");

// Control the operation of the page
function main($title = "") {
    $redirect = "login.php";
    $other = "<meta http-equiv=\"Refresh\"";
    $other .= "content=\"5;URL=$redirect\" />\n";
    $user = "";
    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
    }
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 86400, '/');
    }
    session_destroy();
    include("includes/header.php");
    showContent($title, $redirect, $user);
    include("includes/footer.php");
}

// Display the content of the page
function showContent($title, $redirect, $user) {
    $msg = $user;
    if (!$user) {
        $msg = "You are";
    }
    echo "<h1>$title</h1>";
    echo<<<HTML
<p>$msg logged out securely.</p>
<p>Click <a href="$redirect">here</a> to continue.</p>
HTML;
}
?>
