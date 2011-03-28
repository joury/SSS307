<?php

require "classes/class.website.php";
require "classes/class.database.php";
$database = new database();
$website = new website($database);
$exists = "false";
if (isset($_GET['username'])) {
    $username = stripslashes(mysql_real_escape_string($_GET['username']));
    if ($website->AccountExists($username, "")) {
        $exists = "true";
    }
}
if (isset($_GET['email'])) {
    $email = stripslashes(mysql_real_escape_string($_GET['username']));
    if ($website->AccountExists("", $email)) {
        $exists = "true";
    }
}
echo $exists;
?>
