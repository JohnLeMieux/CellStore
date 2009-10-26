<?php
/**
 * cart.php
 *
 * The shopping cart for FestiveGear.
 *
 * @author John Le Mieux
 * @version 1.0 05/31/09
 */
   ob_start();
   require("includes/db.php");
   require("includes/formlib.php");
   define('CART_TIME', 2592000);
   define('MAX_QTY', 10);
   main("Festive Gear Shopping Cart");

   /**
    * The structure of the page.
    *
    * $title The title of the page
    */
   function main($title = "") {
      $db = new DB();
      if (isset($_REQUEST['add'])) {
         $pid = $_REQUEST['add'];
         addItem($db, $pid);
      } else if (isset($_REQUEST['del'])) {
         $pid = $_REQUEST['del'];
         deleteItem($db, $pid);
      } else if (isset($_REQUEST['update'])) {
         $pid = $_REQUEST['update'];
         updateItem($db, $pid);
      }
      $other = getJavaScript();
      include("includes/header.php");
      showContent($title, $db);
      include("includes/footer.php");
   }

   /**
    * Inserts JavaScript into the HTML header.
    */
   function getJavaScript() {
      return<<<SCRIPT
<script type="text/javascript">
   // Deletes or updates quantity of an item.
   function updateQty(item) {
      itemId = item.name;
      if (item.value == "delete") {
         location.href = 'cart.php?del='+itemId;
      } else {
         newQty = item.options[item.selectedIndex].text;
         location.href = 'cart.php?update='+itemId+'&amp;qty='+newQty;
      }
   }

   // Makes sure the user is logged in before checking out.
   function checkout(isLoggedIn) {
      location.href = (isLoggedIn) ? 'checkout.php' : 'login.php';
   }
</script>
SCRIPT;
   }

   /**
    * Returns the quantity of an item in the shopping cart.
    *
    * @return The quantity of the item
    */
   function getQuantity() {
      $qty = 1;
      if (isset($_REQUEST['qty'])) $qty = $_REQUEST['qty'];
      $qty = intval($qty);
      if ($qty < 0) $qty = 0;
      if ($qty > MAX_QTY) $qty = MAX_QTY;
      return $qty;
   }

   /**
    * Adds an item to the cart.
    *
    * @param $db A DB object for secure database queries
    * @param $pid The product ID
    */
   function addItem($db, $pid) {
      $cartID = getCartId();
      $qty = getQuantity();
      $sql = "SELECT * FROM shoppingcarts 
         WHERE CartID='$cartID' AND ProductID=$pid";
      $result = $db->query($sql);
      $numRows = mysql_num_rows($result);
      if ($numRows != 0) {
         $qty = mysql_result($result, 0, 'Quantity');
         $_REQUEST['qty'] = $qty + 1;
         updateItem($db, $pid);
      } else {
         $sql = "SELECT price FROM products WHERE ID=$pid";
         $result = $db->query($sql);
         if (mysql_num_rows($result) == 0) return;
         $price = mysql_result($result, 0, 0);
         $sql = "INSERT INTO shoppingcarts 
            VALUES('$cartID', $pid, NOW(), $price, $qty)";
         $db->query($sql);
      }
   }

   /**
    * Deletes an item from the shopping cart.
    *
    * $db A DB object for secure database operations
    * $pid The product ID
    */
   function deleteItem($db, $pid) {
      $cartID = getCartID();
      $sql = "DELETE FROM shoppingcarts 
         WHERE CartID='$cartID' AND ProductID=$pid";
      $db->query($sql);
   }

   /**
    * Updates the quantity of an item.
    * 
    * @param $db A DB object for secure database operations
    * @param $pid The product ID
    */
   function updateItem($db, $pid) {
      $cartID = getCartID();
      $qty = getQuantity();
      if ($qty <= 0) {
         deleteItem($db, $pid);
      } else {
         $sql = "UPDATE shoppingcarts SET Quantity=$qty 
            WHERE CartID='$cartID' AND ProductID=$pid";
         $db->query($sql);
      }
   }

   /**
    * Returns the Cart ID
    *
    * @return The Cart ID
    */
   function getCartId() {
      if (isset($_COOKIE["cartID"])) {
         return $_COOKIE["cartId"];
      } else {
         if (!session_id()) {
            session_start();
         }
         setcookie("cartId", session_id(), time() + CART_TIME);
         return session_id();
      }
   }

   /**
    * Displays the main content of the page.
    *
    * @param $title The title of the page
    * @param $db A DB object for secure database operations
    */
   function showContent($title, $db) {
      $cartID = getCartId();
      $sql = "SELECT ID, name, products.price, Quantity 
         FROM shoppingcarts, products 
         WHERE ID=ProductID AND CartID='$cartID'";
      $result = $db->query($sql);
      echo "<h1>$title</h1>\n";
      echo "<table>\n";
      showHeading();
      $total = 0;
      while ($row = mysql_fetch_row($result)) {
         list($id, $prodName, $price, $qty) = $row;
         $total += $price * $qty;
         showItem($id, $prodName, $price, $qty);
      }
      $total = "$".number_format($total, 2);
      showFooter($total);
      echo "</table>\n";
      $url = (isset($_SESSION['user'])) ? "true" : "false";
      echo "<button onclick=\"checkout($url)\">Checkout</button>\n";
      $f = new FormLib();
?>
<p>Keep shopping</p>
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
<?php
   }

   /**
    * Displays a single item in a row of the table.
    *
    * @param $id The product ID
    * @param $prodName The name of the product
    * @param $price The individual selling price of the item
    * @param $qty The quantity of this item in the shopping cart
    */
   function showItem($id, $prodName, $price, $qty) {
      echo "<tr>\n";
      echo "<td>$prodName</td>\n<td>$".number_format($price, 2)."</td>\n<td>";
      $f = new FormLib();
      $data = array();
      for ($i = 1; $i <= MAX_QTY; $i++) {
         $data[$i] = $i;
      }
      $other = 'onChange="updateQty(this)"';
      $opt = $f->makeSelect($id, $data, $qty, $other);
      echo "$opt</td>\n<td>$".number_format($price * $qty, 2)."</td>\n";
      echo "<td>";
      echo '<button name="'.$id.'" value="delete" onclick="updateQty(this)">';
      echo "Delete</button>";
      echo "</td>\n";
      echo "</tr>\n";
   }

   /**
    * Displays the table headers.
    */
   function showHeading() {
      echo<<<HTML
<tr style="font-family:verdana; font-size:1.5em; color:white">
   <td style="background:blue;">
      &nbsp;<strong>Product</strong>&nbsp;
   </td>
   <td style="background:blue;">
      &nbsp;<strong>Price</strong>&nbsp;
   </td>
   <td style="background:blue;">
      &nbsp;<strong>Quantity</strong>&nbsp;
   </td>
   <td style="background:blue;">
      &nbsp;<strong>Total</strong>&nbsp;
   </td>
   <td style="background:blue;">
      &nbsp;<strong>Delete Item?</strong>&nbsp;
   </td>
</tr>
HTML;
   }

   /**
    * Displays a footer for the table.
    *
    * @param $total The total selling price for this order
    */
   function showFooter($total) {
      echo "<tr>\n<td></td>\n<td></td>\n<td></td>\n";
      echo "<td>Cart Total<br />\n$total</td>\n</tr>\n";
   }
?>
