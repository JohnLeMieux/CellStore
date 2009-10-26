<?php
   /**
    * CIS-165PH Asn 9
    * form.php
    * Purpose: Collects user input
    *
    * @author John Le Mieux
    * @version 1.1 05/04/09
    */
   ob_start();
   include "includes/util.php";
   include "includes/formverifier.php";
   main("A Well-Organized Page");
   function main($title = "") {
      $f = new FormVerifier();
      $errors = "";
      if (isset($_POST["submitTest"])) {
         $errors = checkForm($f);
         if (!$errors) {
            processData($f);
            echo "Thank you for your order<br />";
            print "Estimated delivery date is ".shipDate();
         }
      }
      include "includes/header.php";
      showContent($title, $f);
      include "includes/footer.php";
   }
   function checkForm($f) {
      $f->isEmpty('username', "Please enter your username.");
      $f->isNotAlpha('fname', "Please enter your first name.");
      $f->isNotAlpha('lname', "Please enter your last name.");
      $f->isNotAlpha('city', "Please enter your city.");
      $f->isNotAlpha('state', "Please enter your state.");
      $f->isNotInteger('zip', "Please enter your zip code.");
      return $error;
   }
   function processData($f) {
      require "includes/dbconvars.php";
      $dbCnx = mysql_connect($dbhost, $dbuser, $dbpwd)
         or die(mysql_error());
      mysql_select_db($dbname, $dbCnx)
         or die(mysql_error());
      $username = trim($_REQUEST["username"]);
      $fname = trim($_REQUEST["fname"]);
      $lname = trim($_REQUEST["lname"]);
      $address = trim($_REQUEST["address"]);
      $city = trim($_REQUEST["city"]);
      $state = trim($_REQUEST["state"]);
      $postal = trim($_REQUEST["postal"]);
      $sql = "
         INSERT INTO customers(username, fname, lname)
         VALUES ('$username', '$fname', '$lname')
         ";
      mysql_query($sql)
         or die("Query $sql failed: ".mysql_error());
      $sql = "
         INSERT INTO addresses(username, address, city, state, postal)
         VALUES ('$username', '$address', '$city', '$state', '$postal')
         ";
      mysql_query($sql)
         or die("Query $sql failed: ".mysql_error());
      mysql_close($dbCnx);
   }
   function showContent($title, $f) {
      echo "<h1>$title</h1>\n";
      if ($errors) {
         echo "$errors\n";
      }
?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
   <fieldset>
      <legend>Enter your shipping information</legend>
      <table>
         <tr>
            <td class="labelcell">
               <?php echo $f->formatOnError('username', 'Username') ?>
            </td>
            <td class="inputcell">
               <input type="text" name="username" size="35" 
                  value="<?php echo $f->getValue('username') ?>" />
               <?php echo $f->showMessageOnError('username') ?>
            </td>
         </tr>
         <tr>
            <td class="labelcell">
               <?php echo $f->formatOnError('fname', 'First Name') ?>
            </td>
            <td class="inputcell">
               <input type="text" name="fname" size="35" 
                  value="<?php echo $f->getValue('fname') ?>" />
               <?php echo $f->showMessageOnError('fname') ?>
            </td>
         </tr>
         <tr>
            <td class="labelcell">
               <?php echo $f->formatOnError('lname', 'Last Name') ?>
            </td>
            <td class="inputcell">
               <input type="text" name="lname" size="35" 
                  value="<?php echo $f->getValue('lname') ?>" />
               <?php echo $f->showMessageOnError('lname') ?>
            </td>
         </tr>
         <tr>
            <td class="labelcell">
               <?php echo $f->formatOnError('street', 'Street') ?>
            </td>
            <td class="inputcell">
               <textarea name="street" cols="30">
                  <?php echo $f->getValue('street') ?>
               </textarea>
            </td>
         </tr>
         <tr>
            <td class="labelcell">
               <?php echo $f->formatOnError('city', 'City') ?>
            </td>
            <td class="inputcell">
               <input type="text" name="city" size="35" 
                  value="<?php echo $f->getValue('city') ?>" />
               <?php echo $f->showMessageOnError('city') ?>
            </td>
         </tr>
         <tr>
            <td class="labelcell">
               <?php echo $f->formatOnError('state', 'State/Province') ?>
            </td>
            <td class="inputcell">
               <input type="text" name="state" size="3" 
                  value="<?php echo $f->getValue('state') ?>" />
               <?php echo $f->showMessageOnError('state') ?>
            </td>
            <td class="labelcell">
               <?php echo $f->formatOnError('zip', 'Zip/Postal Code') ?>
            </td>
            <td class="inputcell">
               <input type="text" name="postal" size="10" 
                  value="<?php echo $f->getValue('zip'); ?>" />
               <?php echo $f->showMessageOnError('zip') ?>
            </td>
         </tr>
      </table>
   </fieldset>
   <p id="formbuttons">
      <input type="button" name="prevb" value="Back" 
         onclick="history.back()" />
      <input type="reset" name="reset" value="Clear" />
      <input type="submit" name="submitTest" value="Submit" />
   </p>
</form>
<?php
   }
?>
