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

    function __construct($id = "", $username = "", $password = "") {
        if ($id != "" || ($username != "" && $password != "")) {
            $result = "";
            if ($id != "") {
                $result = mysql_query("SELECT * FROM `gebruikers` WHERE `id` = '" . $id . "';");
            } else if ($username != "" && $password != "") {
                $result = mysql_query("SELECT * FROM `gebruikers` WHERE `gebruikersnaam` = '" . $username . "' AND `wachtwoord` = '" . $password . "';");
            }

            if ($result != "") {
                if (mysql_num_rows($result) == 1) {   // Als we 1 gebruiker hebben met dit ID (Databases kunne fouten bevatten zoals meerdere personen met hetzelfde ID...gewoon er zeker van te zijn dus)
                    $this->getVars($result);
                }
            }
        }
    }

    function getVars($result) {
        while ($fields = mysql_fetch_assoc($result)) {
            $this->id = $fields['id'];
            $this->username = $fields['gebruikersnaam'];
            $this->password = $fields['wachtwoord'];
            $this->firstname = $fields['voornaam'];
            $this->lastname = $fields['achternaam'];
            $this->insertion = $fields['tussenvoegsel'];
            $this->gender = $fields['geslacht'];
            $this->email = $fields['email'];
            $this->language = $fields['taal'];
            $this->country = $fields['land'];
            $this->state = $fields['provincie'];
            $this->city = $fields['stad'];
            $this->birthdate = $fields['geboortedatum'];
            $this->msn = $fields['msn'];
            $this->skype = $fields['skype'];
        }
    }

}

?>