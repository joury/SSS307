<?php

Class database {

    var $connection;
    var $selected_db;

    function __construct() {
        $this->makeConnection(true);
    }

    function makeConnection($new) {
        require "configs/config.php";
        $this->connection = @mysql_connect($db_host, $db_username, $db_password, $new);
        if ($this->connection) {
            $this->selected_db = @mysql_select_db($db_name);
            if ($this->selected_db) {
                return true;
            } else {
                echo "<center>" . mysql_error() . "</center><br>";
                return false;
            }
        } else {
            echo "<center>" . mysql_error() . "</center><br>";
            return false;
        }
    }

    function doQuery($sql) {
        if ($this->makeConnection(false)) {
            $result = mysql_query($sql);
            if ($result && mysql_num_rows($result) > 0) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getRowCount($sql) {
        if ($this->makeConnection(false)) {
            $result = mysql_query($sql);
            if ($result) {
                return mysql_num_rows($result);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}

?>