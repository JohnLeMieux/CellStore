<?php
/**
* login.php
* Login page to authenticate users
*
* @author Ed Parrish
* @author John Le Mieux
* @version 1.4 5/10/09
*/
ob_start();
require_once "includes/formverifier.php";
require_once "includes/redirect.php";
if (!session_id()) session_start();
define('DAYS_30', 2592000);
define("DEFAULT_PAGE", "/festivegear/index.php");

main("FestiveGear Login Page");

// Form processing logic
function main($title = "") {
    $f = new FormVerifier();
    if (isset($_POST["submitTest"])) {
        checkForm($f);
        if (!$f->isError()) { // data is OK
            processData($f);
            $refPage = DEFAULT_PAGE;
            if (isset($_SESSION["refPage"])) {
                $refPage = $_SESSION["refPage"];
            }
            redirect($refPage); // return to sender
        }
    }
    include("includes/header.php");
    showContent($title, $f);
    include("includes/footer.php");
}

// Check the input form for errors
function checkForm(&$f) {
    $f->isEmpty('username',
            'Please enter a username');
    $f->isEmpty('password',
            'Please enter a password');
    if ($f->isError()) return;

    $user = $f->getValue('username');
    $pwd = $f->getValue('password');

    require_once("includes/dbconvars.php");
    @$db = mysql_connect($dbhost, $dbuser, $dbpwd)
            or die("Could not connect");
    @mysql_select_db($dbname, $db)
            or die("Could not select database");
    $sql = "SELECT username, password FROM customers
            WHERE username='$user'
            AND password='$pwd'";
    $result = mysql_query($sql);
    $row = mysql_fetch_row($result);
    if (!$row || mysql_num_rows($result) !== 1) {
        $f->addError('', '',
            "Invalid username or password");
    }
    mysql_free_result($result);
    mysql_close($db);
}

// Process the data
function processData($f) {
    // Save data in session variables
    $user = $f->getValue('username');
    $_SESSION['user'] = $user;
    // Save data in cookies
    if ($f->getValue('save') === 'y') {
        setcookie("user", $user, time() + DAYS_30);
    }
}

// Display the content of the page
function showContent($title, $f) {
    echo '<div style="margin:auto;">';
    echo "<h1>$title</h1>";
    echo $f->reportErrors();
    $user = $f->getValue('username');
    $pwd = $f->getValue('password');
    $uname = $f->formatOnError('username', 'User name');
    $pword = $f->formatOnError('password', 'Password');
    echo <<<HTML
<p>New users <a href="register.php">click here</a></p>
<form id="login" action="login.php" method="post">
<table cellpadding="3">
<tr>
<td>$uname</td>
<td>
  <p><input type="text" name="username" value="$user" /></p>
</td>
</tr>
<tr>
<td>$pword</td>
<td>
  <p><input type="password" name="password" /></p>
</td>
</tr>
<tr>
<td colspan="2">
  <input type="checkbox" name="save" value="y" checked="checked" />
  Remember my ID on this computer
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submitTest"
  value="Login" /></td>
</tr>
</table>
</form>
</div>
HTML;
}
?>
