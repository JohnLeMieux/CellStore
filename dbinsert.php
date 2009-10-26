/**
* CIS-165PH  Asn 7
* dbinsert.php
* Purpose: Processes form data and saves it in
* my project database.
*
* @author John Le Mieux
* @version 1.0 04/02/09
*/
<?php
   $title = "Form Data";
   include "includes/header.php";
   print "<h1>$title</h1>";
   $uname = $_REQUEST["uname"];
   print "<p>User Name: $uname</p>";
   $email = $_REQUEST["email"];
   print "<p>Email: $email</p>";
   $passwd = $_REQUEST["passwd"];
   print "<p>Password: $passwd</p>";
   $addr = $_REQUEST["addr"];
   print "<p>Address: $addr</p>";
   $city = $_REQUEST["city"];
   print "<p>City: $city</p>";
   $state = $_REQUEST["state"];
   print "<p>State: $state</p>";
   $postal = $_REQUEST["postal"];
   print "<p>Postal: $postal</p>";
   $submit = $_REQUEST["submit"];
   print "<p>Button value: $submit</p>";
   require_once "includes/dbconvars.php";
   $dbCnx = mysql_connect($dbhost, $dbuser, $dbpwd)
      or die("Could not connect: " . mysql_error());
   mysql_select_db($dbname, $dbCnx)
      or die("Could not select db: " . mysql_error());
   $sql = "
      INSERT INTO customers(username, password, name, email)
      VALUES ('$uname', '$passwd', '$custname', '$email')
      ";
   print "<p>sql=$sql</p>\n";
   mysql_query($sql)
      or die("Query failed: " . mysql_error());
   $numRows = mysql_affected_rows();
   print "<p>Rows affected: $numRows</p>\n";
   $sql = "
      INSERT INTO addresses(username, address, city, state, postal, country)
      VALUES ('$uname', '$addr', '$city', '$state', '$postal', '$country')
      ";
   print "<p>sql=$sql</p>\n";
   mysql_query($sql)
      or die("Query failed: " . mysql_error());
   $numRows = mysql_affected_rows();
   print "<p>Rows affected: $numRows</p>\n";
   require "includes/footer.php";
?>

