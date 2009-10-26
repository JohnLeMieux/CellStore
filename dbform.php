/**
* CIS-165PH  Asn 7
* dbform.php
* Purpose: Takes user data for setting up a new account.
*
* @author John Le Mieux
* @version 1.0 04/02/09
*/
<?php
   $title = "Festive Gear - New Account";
   include "includes/header.php";
   echo "<h1>$title</h1>\n";
?>
<form action="dbinsert.php" method="POST" name="cust">
   <fieldset>
      <legend>Enter your contact information</legend>
      <table>
         <tr>
            <td class="labelcell">Username</td>
            <td class="inputcell">
               <input type="text" name="uname" size="10" />
            </td>
         </tr>
         <tr>
            <td class="labelcell">Name</td>
            <td class="inputcell">
               <input type="text" name="custname" size="30"/>
            </td>
         </tr>
         <tr>
            <td class="labelcell">Email</td>
            <td class="inputcell">
               <input type="text" name="email" size="30" />
            </td>
         </tr>
         <tr>
            <td class="labelcell">Password</td>
            <td class="inputcell">
               <input type="password" name="passwd" size="10" />
            </td>
         </tr>
         <tr>
            <td class="labelcell">Address</td>
            <td class="inputcell">
               <input type="text" name="addr" size="30" />
            </td>
         </tr>
         <tr>
            <td class="labelcell">City</td>
            <td class="inputcell">
               <input type="text" name="city" size="20" />
            </td>
         </tr>
         <tr>
            <td class="labelcell">State</td>
            <td class="inputcell">
               <input type="text" name="state" size="3" />
            </td>
         </tr>
         <tr>
            <td class="labelcell">Zip/Postal Code</td>
            <td class="inputcell">
               <input type="text" name="postal" size="10" />
            </td>
         </tr>
         <tr>
            <td class="labelcell">Country</td>
            <td class="inputcell">
               <input type="text" name="country" size="20" />
            </td>
         </tr>
      </table>
   </fieldset>
   <p id="formbuttons">
      <input type="submit" name="submit" />
      <input type="reset" name="clear" value="Clear" />
   </p>
</form>
<?php include "includes/footer.php"; ?>
