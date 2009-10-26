<?php
/**
  db.php
  Database class that handles daabase connections and queries

  @author Ed Parrish
  @version 1.2 05/08/09
*/
class DB {
    private $cnx = 0; // Result of mysql_connect()
    private $errLevel = "halt"; //"ignore", "halt", "warn"
    private $result = 0; // Result of most recent mysql_query()

    /**
        Constructor makes initial connection to the database
     */
    function __construct($errorLevel = false) {
        if ($errorLevel) $this->errLevel = $errorLevel;
        $this->connect();
    }

    /**
        Connects to and selects a database using dbconvars.php arguments
     */
    function connect() {
        if ($this->cnx == 0) {
            require("includes/dbconvars.php");
            @$this->cnx = mysql_connect($dbhost, $dbuser, $dbpwd);
            if (!$this->cnx) {
                $this->_handleError("Connect failed.");
                return false;
            }
            if (@!mysql_select_db($dbname, $this->cnx)) {
                $this->_handleError("Can not select database '$dbname'.");
                return false;
            }
        }
        return $this->cnx;
    }

    /**
        Perform a query based on the $sql argument.

        @param $sql The SQL statements to perform.
        @return The result set from the query.
    */
    function query($sql) {
        $sql = trim($sql);
        if (strlen($sql) == 0) return 0;
        if (!$this->connect()) return 0;  // connection problems
        $this->result = @mysql_query($sql, $this->cnx);
        if (mysql_errno()) $this->_handleError("Invalid SQL: ".$sql);
        return $this->result;
    }

    /**
        Show the result data from a SQL SELECT query

        @param $result The result set from the last SELECT query. (optional)
    */
    function showQuery($result = 0) {
        if (!$result) $result = $this->result;
        if (!$result) {
            $this->_handleError("No result set to show!");
            return;
        }

        // Convert column names to table headings
        $html = "<table border>\n";
        $html .= "<tr>\n";
        $count = mysql_num_fields($result);
        for ($i = 0; $i < $count; $i++) {
            $html .= "<th>".mysql_field_name($result, $i)."</th>\n";
        }
        $html .= "</tr>\n";

        // Convert data to table cells
        @mysql_data_seek($result, 0);
        $count = 0;
        while ($row = mysql_fetch_row($result)) {
            $html .= "<tr>\n";
            foreach ($row as $item) {
                $html .= "<td>&nbsp;$item</td>\n";
            }
            $html .= "</tr>\n";
            $count++;
        }
        echo $html;
    }

    /**
        Error handling
    */
    function _handleError($msg) {
        if ($this->errLevel == "ignore") return;
        echo "</td></tr></table></div>
              <b>Database error:</b> $msg<br>\n
              <b>DB Error</b>: ".mysql_errno()
              ." (".mysql_error().")<br>\n";
        if ($this->errLevel == "halt") die ("Session halted.");
    }
}
?>
