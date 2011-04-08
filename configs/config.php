<?php

if (!@Darkrulerz) {
    define("Darkrulerz", 1);                    // Anti-Hack measure
}
$db_name = "codedump";
$db_host = "localhost";
$db_username = "root";
$db_password = "mangos";
$required_rank = "1";               // Rank required to view the ban list (0 = user, 1 = moderator, 2 = admin)
$cookiename = "codedumper_login";   // Name of the cookie that will be used to store the username and sha1 of the pass
$cookietime = "60";                        // Time the cookie exists in minutes
$MaxFileSize = "10";                        // Filesize for the avatar in MB
$SaveDir = "./avatars/";                      // Directory in which the user's pictures end up
$MaxAvatarDimension = "50x50";              // Resolution of the image in X , Y
$AllowedFileTypes = array("image/png", "image/jpg", "image/jpeg", "image/bmp");
$LanguageDir = "./languages/";
?>