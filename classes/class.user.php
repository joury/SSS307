<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author Dark
 */
class user {
    var $id;
    var $username;
    var $password;
    var $firstname;
    var $insertion;
    var $lastname;
    var $gender;
    var $email;
    var $language;
    var $country;
    var $state;
    var $city;
    var $birthdate;
    var $msn;
    var $skype;

    function __construct($id) {
        $result = mysql_query("SELECT * FROM `gebruikers` WHERE `id` = '".$id."';");
        if (mysql_num_rows($result) == 1) {   // Als we 1 gebruiker hebben met dit ID (Databases kunne fouten bevatten zoals meerdere personen met hetzelfde ID...gewoon er zeker van te zijn dus)
            while($fields = mysql_fetch_assoc($result)) {
                $this->id = $fields['id'];
                $this->username = $fields['gebruikersnaam'];
            }
        }
    }
}
?>
