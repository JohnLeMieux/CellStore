<?php
   /**
    * CIS-165PH Asn 8
    * select.php
    * Purpose: Product Selection for Festive Gear
    *
    * @author John Le Mieux
    * @version 1.0 04/19/09
    */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
   <head>
      <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
      <title>Product Selection</title>
   </head>
   <body>
      <h1>Product Selection</h1>
      <form action="report.php" method="POST">
         <p>
            Select a Product:
            <?php
               require_once("includes/dbconvars.php");
               $dbCnx = mysql_connect($dbhost, $dbuser, $dbpwd)
                  or die("Could no connect: " . mysql_error());
               mysql_select_db($dbname, $dbCnx)
                  or die("Could not select db: " . mysql_error());
               $sql = "SELECT ID, name FROM products";
               $result = mysql_query($sql)
                  or die("Query failed: " . mysql_error());
               echo "<select name=\"prodselect\">\n";
               while ($row = mysql_fetch_assoc($result)) {
                  echo '<option value="';
                  echo $row['ID'];
                  echo '">';
                  echo $row['name'];
                  echo "</option>\n";
               }
               echo "</select>";
               mysql_free_result($result);
               mysql_close($dbCnx);
            ?>
         </p>
         <p><input type="submit" value="Submit"></p>
      </form>
   </body>
</html>
