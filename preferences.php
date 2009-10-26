<?php
   /**
    * preferences.php
    * Allows customers to view and change account info
    *
    * @author John Le Mieux
    * @version 1.0 05/10/09
    */
   ob_start();
   require_once "includes/formlib.php";
   if (!session_id()) session_start();
   main("FestiveGear Preferences");
   /**
    * Controls the operation of the page
    *
    * @param $title Page title
    */
   function main($title = "") {
      $f = new FormLib();
      require_once("includes/dbconvars.php");
      $dbCnx = mysql_connect($dbhost, $dbuser, $dbpwd)
         or die(mysql_error());
      mysql_select_db($dbname, $dbCnx)
         or die(mysql_error());
      if (isset($_POST["submitTest"])) {
            processData($f);
            $refPage = $_SESSION['PHP_SELF'];
            if (isset($_SESSION["refPage"])) {
               $refPage = $_SESSION["refPage"];
            }
      }
      include("includes/header.php");
      showContent($title, $f);
      include("includes/footer.php");
      mysql_close($dbCnx);
   }
   /**
    * Displays the content of the page
    *
    * @param $title Page title
    * @param $f FormLib object
    */
   function showContent($title, $f) {
      $user = "";
      if (!isset($_SESSION["user"])) {
         redirect("login.php");
      } else {
         $user = $_SESSION["user"];
      }
      $sql = "SELECT fname, lname, email, address, city, state, zip, country 
         FROM customers, addresses 
         WHERE customers.username=addresses.username 
         AND customers.username = '$user'";
      $result = mysql_query($sql)
         or die(mysql_error());
      $row = mysql_fetch_row($result);
      echo $f->start();
?>
      <fieldset>
         <legend>Make changes to your user profile</legend>
         <table>
            <tr>
               <td class="labelcell">
                  <?php echo $f->formatOnError('username', 'Username'); ?>
               </td>
               <td class="labelcell">
                  <?php echo $user; ?>
               </td>
            </tr>
            <tr>
               <td class="labelcell">
                  <?php echo $f->formatOnError('password', 'Password'); ?>
               </td>
               <td class="inputcell">
                  <?php echo $f->makePassword("Password", 10); ?>
               </td>
            </tr>
            <tr>
               <td class="labelcell">
                  <?php echo $f->formatOnError('fname', 'First Name'); ?>
               </td>
               <td class="inputcell">
                  <?php echo $f->makeTextInput('FName', 20, $row[0]); ?> 
               </td>
            </tr>
            <tr>
               <td class="labelcell">
                  <?php echo $f->formatOnError('lname', 'Last Name'); ?>
               </td>
               <td class="inputcell">
                  <?php echo $f->makeTextInput('LName', 20, $row[1]); ?>
               </td>
            </tr>
            <tr>
               <td class="labelcell">
                  <?php echo $f->formatOnError('email', 'Email'); ?>
               </td>
               <td class="inputcell">
                  <?php echo $f->makeTextInput('Email', 20, $row[2]); ?>
               </td>
            </tr>
            <tr>
               <td class="labelcell">
                  <?php echo $f->formatOnError('address', 'Address'); ?>
               </td>
               <td class="inputcell">
                  <?php echo $f->makeTextInput('Address', 30, $row[3]); ?>
               </td>
            </tr>
            <tr>
               <td class="labelcell">
                  <?php echo $f->formatOnError('city', 'City'); ?>
               </td>
               <td class="inputcell">
                  <?php echo $f->makeTextInput('City', 20, $row[4]); ?>
               </td>
            </tr>
            <tr>
               <td class="labelcell">
                  <?php echo $f->formatOnError('state', 'State'); ?>
               </td>
               <td class="inputcell">
                  <?php echo $f->makeTextInput('State', 3, $row[5]); ?>
               </td>
            </tr>
            <tr>
               <td class="labelcell">
                  <?php echo $f->formatOnError('zip', 'Zip'); ?>
               </td>
               <td class="inputcell">
                  <?php echo $f->makeTextInput('Zip', 10, $row[6]); ?>
               </td>
            </tr>
            <tr>
               <td class="labelcell">
                  <?php echo $f->formatOnError('country', 'Country'); ?>
               </td>
               <td class="inputcell">
                  <?php echo $f->makeTextInput('Country', 20, $row[7]); ?>
               </td>
            </tr>
         </table>
      </fieldset>
      <p id="formbuttons">
      <?php echo $f->makeButton(); ?>
      </p>
<?php
      echo $f->finish();
   }
   /**
    * Enters changes into database
    *
    * @param $f FormLib object
    */
   function processData($f){
      $user = "";
      if ($_SESSION["user"]) {
         $user = $_SESSION["user"];
         $password = $f->getValue('password');
         $email = $f->getValue('email');
         $sql = "UPDATE `customers` 
                 SET `password` = '$password',`email` = '$email' 
                 WHERE `username` = '$user' LIMIT 1";
         mysql_query($sql)
            or die(mysql_error());
      }
      echo "Data entered.<br />";
   }

   function redirect($url) {
      $url = trim($url);
      $absURL = "Location: ";
      if (substr($url, 0, 1) == "/") {
         $absURL .= "http://".$_SERVER['HTTP_HOST'];
      } elseif (strtolower(substr($url, 0, 7)) != "http://") {
         $absURL .= "http://".$_SERVER['HTTP_HOST'];
         $absURL .= dirname($_SERVER['PHP_SELF'])."/";
      }
      $absURL .= $url;
      header($absURL);
      die("Could not redirect");
   }
?>
