<?php
   /**
    * CIS-165PH Asn 9
    * util.php
    * Purpose: Utility library for form.php
    * 
    * @author John Le Mieux
    * @version 1.0 04/26/09
    */
   function shipDate() {
      $format = "l F jS, Y";
      $ts = mktime(date("H") + 96);
      return date($format, $ts);
   }
?>
