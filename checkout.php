<?php
/**
 * checkout.php
 *
 * Checks the user out of the Shopping Cart. The order is moved to the orders 
 * table. The database entry for the Shopping Cart is deleted. The cookies for 
 * the CartID and Username are deleted.
 *
 * @author John Le Mieux
 * @version 1.0 05/31/09
 */
   ob_start();
   require_once "includes/formlib.php";
   require_once "includes/db.php";
   if (!session_id()) session_start();
   main("Festive Gear Checkout");

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
    * Displays the main content of the page.
    *
    * @param $title The title of the page
    */
   function showContent($title) {
      $db = new DB();
      $cartID = getCartID();
      $sql = "SELECT ID, name, products.price, quantity, date 
         FROM shoppingcarts, products
         WHERE productID=ID AND CartID='$cartID'";
      $result = $db->query($sql);
      echo "<h1>$title</h1>\n";
      echo "<table>\n";
      showHeading();
      $user = isset($_SESSION['user']) ? $_SESSION['user'] : "";
      while ($row = mysql_fetch_row($result)) {
         list($productId, $prodName, $price, $qty, $date) = $row;
         $total += $price * $qty;
         showItem($productId, $prodName, $price, $qty);
         $sql = "INSERT INTO orders(username, date, status)
            VALUES ('$user', '$date', 'ordered')";
         $db->query($sql);
         $sql = "INSERT INTO orderItems(orderID, productID, quantity, status)
            VALUES ('LAST_INSERT_ID()', '$productId', '$qty', 'ordered')";
         $db->query($sql);
         $sql = "DELETE FROM shoppingcarts WHERE CartID='$cartID'";
         $db->query($sql);
      }
      $total = "$".number_format($total, 2);
      showFooter($total);
      echo "</table>\n";
      $sql = "SELECT fname, lname, address, city, state, zip, country
         FROM customers, addresses 
         WHERE customers.username=addresses.username 
         AND customers.username='$user'";
      $result = $db->query($sql);
      $row = mysql_fetch_row($result);
      list($fname, $lname, $address, $city, $state, $zip, $country) = $row;
      echo "<p>This order will be shipped to</p>";
      echo "<p>$fname $lname</p>";
      echo "<p>$address</p>";
      echo "<p>$city, $state $zip</p>";
      echo "<p>$country</p>";
      setcookie('cartID', '', time() - 86400, '/');
      session_destroy();
   }

   /**
    * Returns the Cart ID.
    *
    * @return The Cart ID
    */
   function getCartID() {
      if (isset($_COOKIE["cartId"])) {
         return $_COOKIE["cartId"];
      } else {
         redirect("login.php");
      }
   }

   /**
    * Redirects the page given an incomplete URL.
    *
    * @param $url A relative or absolute URL 
    */
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

   /**
    * Displays a single item in a row of the Shopping Cart.
    *
    * @param $id The Product ID
    * @param $prodName The name of the product
    * @param $price The individual selling price of the product
    * @param $qty The quantity of the product
    */
   function showItem($id, $prodName, $price, $qty) {
      echo "<tr>\n";
      echo "<td>$prodName</td>\n<td>$".number_format($price, 2)."</td>\n<td>";
      $f = new FormLib();
      $data = array();
      for ($i = 1; $i <= MAX_QTY; $i++) {
         $data[$i] = $i;
      }
      echo "</tr>\n";
   }

   /**
    * Displays a header for the table.
    */
   function showHeading() {
      echo<<<HTML
<tr style="font-family:verdana; font-size:1.5em; color:white">
   <td bgcolor="blue">
      &nbsp;<strong>Product</strong>&nbsp;
   </td>
   <td bgcolor="blue">
      &nbsp;<strong>Price</strong>&nbsp;
   </td>
   <td bgcolor="blue">
      &nbsp;<strong>Quantity</strong>&nbsp;
   </td>
   <td bgcolor="blue">
      &nbsp;<strong>Total</strong>&nbsp;
   </td>
</tr>
HTML;
   }

   /**
    * Displays a footer for the Shopping Cart table.
    *
    * @param $total The total price of the order
    */
   function showFooter($total) {
      echo "<tr>\n<td></td>\n<td></td>\n<td></td>\n";
      echo "<td>Cart Total<br />\n$total</td>\n</tr>\n";
   }
?>
