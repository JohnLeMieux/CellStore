<?php
/**
 * products.php
 *
 * Displays product catalogue by category or in entirety.
 *
 * @author John Le Mieux
 * @version 1.0 05/31/09
 */
   ob_start();
   require("includes/db.php");
   main("Festive Gear Products");
   
   /**
    * The structure of the page.
    *
    * @param $title The title of the page
    */
   function main($title = "") {
      include("includes/header.php");
      showContent($title);
      include("includes/footer.php");
   }

   /**
    * Displays the page.
    *
    * @param $title The title of the page
    */   
   function showContent($title) {
      $db = new DB();
      $category = trim($_REQUEST["category"]);
      $sql = "SELECT ID, image, name, price, description FROM products";
      if ($category) {
         $sql .= " WHERE category = '$category'";
      }
      $result = $db->query($sql);
      echo "<h1>$title</h1>\n";
      echo "<table border=\"1\">\n";
      showHeading();
      while ($row = mysql_fetch_row($result)) {
         list($id, $image, $name, $price, $description) = $row;
         $price = "$".number_format($price, 2);
         showItem($id, $name, $description, $image, $price);
      }
      echo "</table>\n";
    }

   /**
    * Displays the table headings.
    */
   function showHeading() {
      echo<<<HTML
<tr style="font-family:verdana; font-size:1.5em; color:white">
   <td style="background:blue;">
      &nbsp;<strong>Image</strong>&nbsp;
   </td>
   <td style="background:blue;">
      &nbsp;<strong>Product</strong>&nbsp;
   </td>
   <td style="background:blue;">
      &nbsp;<strong>Price</strong>&nbsp;
   </td>
   <td style="background:blue;">
      &nbsp;<strong>Description</strong>&nbsp;
   </td>
   <td style="background:blue;">
      &nbsp;<strong>Buy</strong>&nbsp;
   </td>
</tr>
HTML;
   }

   /**
    * Places item information into a table row.
    *
    * @param $id Product ID
    * @param $name The name of the product
    * @param $description A description of the product
    * @param $image An anchor with the absolute URL and size of a product image
    * @param $price The sale price of a single product
    */   
   function showItem($id, $name, $description, $image, $price) {
      echo<<<HTML
<tr style="font-family:verdana; font-size:.7em; color:black;">
   <td>$image</td>
   <td>$name</td>
   <td>$price</td>
   <td>$description</td>
   <td><a href="cart.php?add=$id&amp;qty=1">Add Item</a></td>
</tr>
HTML;
   }
?>
