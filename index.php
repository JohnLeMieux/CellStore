<?php
/**
 * index.php
 *
 * The homepage for the FestiveGear website.
 *
 * @author John Le Mieux
 * @version 1.0 05/31/09
 */
   ob_start();
   
   require_once "includes/formverifier.php";
   require_once "includes/formlib.php";

   main("Festive Gear");

   /**
    * The structure of the page.
    *
    * @param $title The title of the page
    */
   function main($title = "") {
      $f = new FormLib("error", HORIZONTAL);
      require "includes/header.php";
      showContent($title, $f);
      require "includes/footer.php";
   }

   /**
    * The main content of the page.
    *
    * @param $title The title of the page
    * @param $f A FormLib object for adding secure form elements
    */
   function showContent($title = "", $f) {
?>
<h1><?php echo $title ?></h1>
<p>
   Welcome to Festive Gear, your one-stop shopping site for festival supplies.
   What category are you shopping for today?
</p>
<?php 
   $f->reportErrors();
?>
                  <form action="products.php" method="post" id="categories">

      <fieldset>
         <legend>Select a category</legend>
         <table>
            <tr>
               <td class="inputcell">
                  <?php
                     $list = array("All"=>"", "On the Lawn"=>"lawn", 
                        "Back at Camp"=>"camping", "Jammin'"=>"jam",
                        "At the Lake"=>"water", "Artists' CDs"=>"cd");
                     echo $f->makeSelect('category', $list);
                  ?>
               </td>
            </tr>
         </table>
      </fieldset>
      <p><input type="submit" value="Submit" /></p>
   </form>
   <p>
      Click here to <a href="login.php">login</a> or 
      <a href="register.php">register</a>.
   </p>
<?php
   }
?> 
