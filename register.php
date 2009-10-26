<?php
/**
* register.php
* Registers a user for later authentication
*
* @author Ed Parrish
* @author John Le Mieux
* @version 1.4 5/10/09
*/
ob_start();
require_once "includes/formlib.php";
require_once "includes/redirect.php";
if (!session_id()) session_start();
define("MIN_USER", 6);
define("MIN_PWD", 6);
define("DEFAULT_PAGE", "/festivegear/index.php");

main("FestiveGear Registration");

// Form processing logic
function main($title = "") {
    $f = new FormLib();
    if (isset($_POST["submitTest"])) {
        require_once("includes/dbconvars.php");
        @$db = mysql_connect($dbhost, $dbuser, $dbpwd)
                or die("Could not connect");
        @mysql_select_db($dbname, $db)
                or die("Could not select database");
        checkForm($f, $db);
        if (!$f->isError()) { // data is OK
            processData($f, $db);
            $refPage = DEFAULT_PAGE;
            if (isset($_SESSION["refPage"])) {
                $refPage = $_SESSION["refPage"];
                // Session var no longer needed
                unset($_SESSION["refPage"]);
            }
            redirect($refPage);
        }
        mysql_close($db);
    }
    include("includes/header.php");
    showContent($title, $f);
    include("includes/footer.php");
}

// Check form for errors and return error messages
function checkForm(&$f, $db) {
    $f->isInvalidEmail('email',
            'Please enter a valid email');
    $f->isEmpty('username',
            'Please enter a username');
    $f->isEmpty('password',
            'Please enter a password');
    $f->isEmpty('password2',
            'Please enter a confirming password');
    if ($f->isError()) return;

    $user = $f->getValue('username');
    if (strlen($user) < MIN_USER) {
        $f->addError('username', $user,
            "User name must be at least ".MIN_USER
            ." characters.");
    }
    $pwd = $f->getValue('password');
    if (strlen($pwd) < MIN_PWD) {
        $f->addError('password', $pwd,
            "Password must be at least ".MIN_PWD
            ." characters.");
    }
    if ($pwd != $f->getValue('password2')) {
        $f->addError('password2', '',
            "Passwords do not match");
    }
    if ($f->isError()) return;

    // Check database
    $user = $f->getValue('username');
    $sql = "SELECT username, password FROM customers
            WHERE username='$user'";
    @$result = mysql_query($sql)
            or die("Query failed");
    $numRows = mysql_num_rows($result);
    if ($numRows !== 0) {
        $msg = "Username taken - "
               ."please choose another";
        $f->addError($user, '', $msg);
    }
    mysql_free_result($result);
}

// Process the data
function processData($f, $db) {
    // Save database data
    $user = $f->getValue('username');
    $pwd = $f->getValue('password');
    $email = $f->getValue('email');
    $sql = "INSERT INTO customers
            (username, password, email)
            VALUES ('$user','$pwd','$email')";
    @mysql_query($sql) or die("Query failed in save");

    // Save session data
    $_SESSION['user'] = $user;
}

// Display the content of the page
function showContent($title, $f) {
    echo "<h1>$title</h1>";
    echo $f->reportErrors();
    echo $f->start();
?>
<p><table cellpadding="6">
<tr>
  <td><?php echo $f->formatOnError('email',
    'Email address') ?></td>
  <td><?php echo $f->makeTextInput('email', 40) ?></td>
</tr>
<tr>
  <td><?php echo $f->formatOnError('username',
    'Username') ?></td>
  <td><?php echo $f->makeTextInput('username') ?></td>
</tr>
<tr>
  <td><?php echo $f->formatOnError('password',
    'Password') ?></td>
  <td><?php echo $f->makePassword('password') ?></td>
</tr>
<tr>
  <td><?php echo $f->formatOnError('password2',
    'Confirm password') ?></td>
  <td><?php echo $f->makePassword('password2') ?></td>
</tr>
<tr>
  <td><?php echo $f->makeButton("Save") ?></td>
  <td>&nbsp;</td>
</tr>
</table></p>
<?php
    echo $f->finish();
}
?>
