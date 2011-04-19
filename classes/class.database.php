<?php

Class database {

    var $connection;
    var $selected_db;

    function __construct() {
        require "configs/config.php";
        $this->connection = mysql_connect($db_host, $db_username, $db_password);
        if ($this->connection) {
            $this->selected_db = mysql_select_db($db_name);
            if ($this->selected_db) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function doQuery($sql) {
        $result = mysql_query($sql);
        if ($result && mysql_num_rows($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    function getRowCount($sql) {
        $result = mysql_query($sql);
        if ($result) {
            return mysql_num_rows($result);
        } else {
            return 0;
        }
    }

}

?>