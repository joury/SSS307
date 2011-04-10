<?php

Class website {

    var $DB;
    var $MainConfigFile = "configs/config.php";
    var $LanguageDir = "languages/";
    var $User = "";
    var $correctLogin = true;

    function __construct($db) {
        $this->DB = $db;
        $this->getCurrentUser();
    }

    function showRegister($_POST, $error = "") {
        if (!isset($_POST['confirmpassword'])) {
            $_POST['confirmpassword'] = $_POST['password'];
        }
        if (!isset($_POST['email'])) {
            $_POST['email'] = "";
        }

        echo '
            <table>
        ';
        if ($error != "") {
            echo '<font color="red">' . $this->Translate("ErrorOccured") . '</font><br>';
            echo $error;
        }
        echo '
                <form name="Register" id="RegistrationForm" onSubmit="return CheckFields(this);" action="index.php" method="POST">
                <tr>
                    <td>Username:</td> <td><input type="text" name="username" id="username" value="' . $_POST['username'] . '" onKeyup="return CheckUsername(this, false);"><font color="RED">*</font><img src="images/ffffff.gif" id="usernameImage"></img></td>
                </tr>
                <tr>
                    <td>Password:</td> <td><input type="password" name="password" id="password" value="' . $_POST['password'] . '" onKeyup="return CheckPass(this.form, false);"><font color="RED">*</font><img src="images/info.gif" id="passwordImage" title="Must contain 6 characters or more of which 2 numbers or more"></img></td>
                </tr>
                <tr>
                    <td>Confirm password:</td> <td><input type="password" name="confirmpassword" id="confirmpassword" value="' . $_POST['confirmpassword'] . '" onKeyup="return CheckPass(this.form, false);"><font color="RED">*</font><img src="images/ffffff.gif" id="confirmpasswordImage"></img></td>
                </tr>
                <tr>
                    <td>Email:</td> <td><input type="text" name="email" id="email" value="' . $_POST['email'] . '" onKeyup="return CheckEmail(this, false, true);"><font color="RED">*</font><img src="images/ffffff.gif" id="emailImage"></img></td>
                </tr>
                <tr>
                    <td>Job:</td> <td><input type="hidden" name="job" value="0"><input type="checkbox" name="job" value="1"> Yes, i have a job</td>
                </tr>
                <tr>
                    <td>Site language:</td>
                    <td>
                        <select name="language">
        ';
        foreach ($this->GetLanguageFiles() as $languagefile) {
            echo '<option value="' . $languagefile . '">' . $languagefile . '</option>';
        }
        echo '
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="hidden" name="RegistrationForm" value="1">
                        <input type="submit" name="Register" value="Register!">
                    </td>
                </tr>
                <script type="text/javascript">
                    CheckEmail(document.getElementById(\'email\'), false, true);
                    CheckPass(document.getElementById(\'RegistrationForm\'), false);
                    CheckUsername(document.getElementById(\'username\'), false);
                </script>
                </form>
            </table>
        ';
    }

    function Translate($string) {
        require $this->MainConfigFile;
        if ($this->User != "") {
            $languagefile = $LanguageDir . $this->User->language . ".php";
        } else {
            $languagefile = "";
        }

        if (is_file($languagefile)) {
            require $languagefile;
        } else {
            require $LanguageDir . "English.php";
        }

        if (@$Language[$string] == "") {
            return "Error: " . $string . "\ncan't be translated...";
        }
        return $Language[$string];
    }

    function GetLanguageFiles() {
        require $this->MainConfigFile;
        $handle = opendir($LanguageDir);
        if ($handle) {
            $LanguageFiles = array();
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $file = explode(".", $file);
                    $LanguageFiles[] = $file[0];
                }
            }
        }
        closedir($handle);
        return $LanguageFiles;
    }

    function RefreshCookie() {
        require $this->MainConfigFile;
        if ($_COOKIE[$cookiename] != "") {
            setcookie($cookiename, $_COOKIE[$cookiename], time() + ($cookietime * 60));      // Make a new cookie with the same name and same info
        }
    }

    function GetQueryString($raw = "") {
        if ($raw != "") {
            $raw = '?' . $raw;
        }
        return $raw;
    }

    function AccountExists($username = "", $email = "") {
        if ($username != "" && $email != "") {
            return ($this->NameInUse($username) && $this->EmailInUse($email));
        } else if ($username != "") {
            return $this->NameInUse($username);
        } else if ($email != "") {
            return $this->EmailInUse($email);
        } else {
            return $this->Translate("ErrorOccured") . "Both the username and the email parameters are empty!";
        }
    }

    function NameInUse($username) {
        $check = mysql_query("SELECT `id` FROM `gebruikers` WHERE `gebruikersnaam` = '" . $username . "';");
        if (mysql_num_rows($check) == 0) {
            return false;
        }
        return true;
    }

    function EmailInUse($email) {
        $check = mysql_query("SELECT `id` FROM `gebruikers` WHERE `email` = '" . $email . "';");
        if (mysql_num_rows($check) == 0) {
            return false;
        }
        return true;
    }

    function EncryptPassword($password) {
        return sha1($password);
    }

    function checkFields($_POST) {
        $good = "";
        if ($_POST['username'] == "") {
            $good .= $this->Translate("Username") . " " . $this->Translate("FieldEmpty");
        }
        if ($_POST['password'] == "") {
            $good .= $this->Translate("Password") . " " . $this->Translate("FieldEmpty");
        }
        if ($_POST['confirmpassword'] == "") {
            $good .= $this->Translate("ConfirmPassword") . " " . $this->Translate("FieldEmpty");
        }
        if ($_POST['confirmpassword'] != $_POST['password']) {
            $good .= $this->Translate("PasswordMatch");
        }
        if (!preg_match('/^(?=.*\d)(?=.*[A-Z]*[a-z]).{6,}$/', $_POST['password'])) {
            $good .= $this->Translate("PasswordRules");
        }
        if ($_POST['email'] == "") {
            $good .= "Email " . $this->Translate("FieldEmpty");
        }
        if (!preg_match('/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/', $_POST['email'])) {
            $good .= $this->Translate("EmailInvalid");
        }
        if ($this->AccountExists(stripslashes(mysql_real_escape_string($_POST['username'])), stripslashes(mysql_real_escape_string($_POST['email'])))) {
            $good .= $this->Translate("AccountEmailUsername");
        }
        if ($good == "") {
            $good = true;
        }

        return $good;
    }

    function DoRegister($_POST) {   // Begin the register function, get the $username and $password from the function call in index.php
        $check = $this->checkFields($_POST);
        if (is_bool($check)) {
            require $this->MainConfigFile;        // Get the connection variables for mysql from the config file
            if ($this->DB->MakeConnection()) {
                $username = stripslashes(mysql_real_escape_string($_POST['username']));  // Make sure there are no weird tokens in the variables
                $password = stripslashes(mysql_real_escape_string($_POST['password']));
                $encryptedPass = $this->EncryptPassword($password);
                $email = stripslashes(mysql_real_escape_string($_POST['email']));
                $raw_account_query = "INSERT INTO `gebruikers` (`gebruikersnaam`, `wachtwoord`, `email`, `taal`, `baan`) VALUES ('" . $username . "', '" . $encryptedPass . "', '" . $email . "', '" . $_POST['language'] . "', '" . $_POST['job'] . "');";
                $account_query = mysql_query($raw_account_query); // Insert the account info
                $this->Login($username, $password);     // Log in to the account
            } else {
                die($this->Translate('NoDB'));  // If we had no connection, stop the script with the message "No DB connection"
            }
        } else {
            $this->showRegister($_POST, $check);
        }
    }

    function ShowLogin() {  // Show the login part (left top of index.php when not logged in)
        echo '
            <form action="' . $_SERVER['PHP_SELF'] . $this->GetQueryString($_SERVER['QUERY_STRING']) . '" name="login" method="POST">
                <li class="me1">
                    <input type="text" name="username">
                    <input type="password" name="password">
                    <input type="submit" name="btnLogin" value="Log in">
                    <input type="submit" name="btnRegister" value="Register">
        ';
        if ($this->correctLogin == false) {
            echo '<font color="red">' . $this->Translate("LoginFailed") . '</font>';
        }
        echo '
                </li>
            </form>
        ';
    }

    function Login($username, $password) {  // Check if the variables sent are correct and set the cookie
        require $this->MainConfigFile;
        $this->DB->MakeConnection();
        $username = mysql_real_escape_string($username);
        $password = $this->EncryptPassword(mysql_real_escape_string($password));
        $passwordInDB = "";
        $query = mysql_query("SELECT `wachtwoord` FROM `gebruikers` WHERE `gebruikersnaam` = '" . $username . "';");  // Get the password from the DB that's associated with this account name
        if (mysql_num_rows($query) != 0) {   // If the account exists
            $passwordInDB = mysql_result($query, 0);  // Get the password of the user with $username
        } else {
            $this->correctLogin = false;
        }

        if ($passwordInDB == $password) {   // If the Sha1 encrypted version of the posted password equals the entry in the database...
            $cookie = setcookie($cookiename, $username . "," . $password, time() + ($cookietime * 60));  // Set a cookie with "name,password" that is legit for the following 5 minutes
            echo '<meta http-equiv="refresh" content="0">';
        } else {
            $this->correctLogin = false;
        }
    }

    function Logout() {
        require $this->MainConfigFile;
        setcookie($cookiename, "1", time() - 3600);  // To delete a cookie, overwrite the cookie with an expiration time of "one hour ago"
        foreach (get_defined_vars () as $key) {  // Reset all variables (clear the session)
            unset($key);
        }
        echo '<meta http-equiv="refresh" content="0">';
    }

    function ShowLogout() {    // Show the logout button
        echo '
            <form name="LogOut" action="' . $_SERVER['PHP_SELF'] . $this->GetQueryString($_SERVER["QUERY_STRING"]) . '" method="POST">
                <input type="submit" name="LogOut" value="Log out">
            </form>
        ';
    }

    function showCategories($_GET = "") {
        echo $this->getCategories($_GET);
    }

    function getCategories($_GET = "") {
        $categories = "";
        $result = mysql_query("SELECT * FROM `talen`;");
        if (mysql_num_rows($result) == 0) {
            $categories .= "Query error when loading languages";
        } else {
            while ($fields = mysql_fetch_assoc($result)) {
                if ($_GET && isset($_GET['categoryid']) && $fields['id'] == $_GET['categoryid']) {
                    $categories .= '<li class="current">';
                    $categories .= '<a class="current" href="?categoryid=' . $fields['id'] . '">' . $fields['naam'] . '</a>';
                    $categories .= '</li>';
                } else {
                    $categories .= '<li>';
                    $categories .= '<a href="?categoryid=' . $fields['id'] . '">' . $fields['naam'] . '</a>';
                    $categories .= "</li>";
                }
            }
        }
        return $categories;
    }

    function getCategoriesAsOption() {
        $categories = "";
        $result = mysql_query("SELECT * FROM `talen`;");
        if (mysql_num_rows($result) > 0) {
            while ($fields = mysql_fetch_assoc($result)) {
                $categories .= '<option value="' . $fields['id'] . '">' . $fields['naam'] . '</option>';
            }
        }
        return $categories;
    }

    function showBanner($_GET) {
        echo '
            <div id="hd">
                <link type="text/css" rel="stylesheet" href="./css/answers.css">
                <div id="ygma">
                    <div id="ygmaheader">
                        <div class="bd sp">
                            <div id="ymenu" class="ygmaclr">
                                <div id="mepanel">
                                    <ul id="mepanel-nav">
        ';
        if ($this->getCurrentUser()) {
            $this->ShowLogout();
        } else {
            $this->ShowLogin();
        }
        echo '
                                    </ul>
                                </div>
                            </div>
                            <div id="yahoo" class="ygmaclr">
                                <div id="ygmabot">
                                    <a href="index.php" id="ygmalogo" target="_top">
                                        <img id="ygmalogoimg" src="./images/logo.png" alt="CodeDump!" height="26" width="257">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ' . $this->getTabs() . '
                <div id="yan-banner">
                    <ul class="short">
                        <li id="yan-banner-ask">
                            <form action="' . $_SERVER['PHP_SELF'] . '" method="GET" name="Ask">
                                <div>
                                    <div>
                                        <input type="hidden" name="answer" value="1">
                                        <input class="default" maxlength="110" id="banner-ask" name="question" type="text">
                                        <span class="cta">
                                            <button value="Continue" class="cta-button">
                                                <span>
                                                    <span>
                                                        <span>
                                                            <span>Ask!</span>
                                                        </span>
                                                    </span>
                                                </span>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </li>
                        <li id="yan-banner-answer">
                            <form action="' . $_SERVER['PHP_SELF'] . '" method="GET" name="Search">
                                <div>
                                    <div>
                                        <input class="default" maxlength="110" id="banner-answer" name="query" type="text">
                                        <span class="cta">
                                            <button value="Continue" class="cta-button">
                                                <span>
                                                    <span>
                                                        <span>
                                                            <span>Search!</span>
                                                        </span>
                                                    </span>
                                                </span>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </li>
                    </ul>
                </div>
                <div id="yan-header">
                </div>
            </div>
        ';
    }

    function getTabs() {
        $tabcode = '
            <div class="tabbed-content">
                <ul class="tabs" id="yan-nav">
        ';
        $tabcode .= '
            <li class="menu" id="yan-nav-home">
                <a href="index.php" onclick="return loadHome();">Home</a>
            </li>
            <li class="menu" id="yan-nav-browse">
                <a href="index.php?categories=1" onclick="return loadCategories();">Categories</a>
            </li>
        ';
        if ($this->getCurrentUser()) {
            $tabcode .= '
                <li class="menu" id="yan-nav-about">
                    <a href="index.php?userid=' . $this->getCurrentUser()->id . '" onclick="return loadProfile(' . $this->getCurrentUser()->id . ');">Profile</a>
                </li>
           ';
        }
        if (isset($_GET['categories']) || isset($_GET['categoryid'])) {
            $tabcode = str_replace('<li class="menu" id="yan-nav-browse">', '<li class="current menu" id="yan-nav-browse">', $tabcode);
        } else if ($this->getCurrentUser() && isset($_GET['userid']) && $this->getCurrentUser()->id == $_GET['userid']) {
            $tabcode = str_replace('<li class="menu" id="yan-nav-about">', '<li class="current menu" id="yan-nav-about">', $tabcode);
        } else {
            $tabcode = str_replace('<li class="menu" id="yan-nav-home">', '<li class="current menu" id="yan-nav-home">', $tabcode);
        }
        $tabcode .= '
                </ul>
            </div>
        ';
        return $tabcode;
    }

    function showCurrentCategory($id) {
        $categoryname = $this->getCategoryName($id);
        if ($categoryname) {
            echo "
                <li>
                    <a href=?categoryid=" . $id . ">" . $categoryname . "</a> &gt
                </li>"
            ;
        }
    }

    function getCategoryName($id) {
        $result = mysql_query("SELECT `naam` FROM `talen` WHERE `id` = '" . $id . "';");
        if (mysql_num_rows($result) == 1) {
            return mysql_result($result, 0);
        } else {
            return false;
        }
    }

    function showCurrentQuestion($categoryid, $questionid) {
        $result = mysql_query("SELECT * FROM `vragen` WHERE `id` = '" . $questionid . "' AND `taalid` = '" . $categoryid . "';");
        if (mysql_num_rows($result) == 1) {
            $fields = mysql_fetch_assoc($result);
            echo '
                <div id="profile" class="profile vcard">
                    <a href="index.php?userid=' . $fields['gebruikerid'] . '" onclick="return loadProfile(' . $fields['gebruikerid'] . ');" class="avatar">
                        <img class="photo" src="' . $this->GetImage($fields['gebruikerid']) . '" width="50">
                    </a>
                    <span class="user">
                        <a class="url" href="index.php?userid=' . $fields['gebruikerid'] . '" onclick="return loadProfile(' . $fields['gebruikerid'] . ');">
                            <span class="fn" title="">
                                ' . $this->getUser($fields['gebruikerid'])->username . '
                            </span>
                        </a>
                        </span>
                </div>
                <div class="qa-container">
                    <div class="hd">
                        <h2>Open Question</h2>
                    </div>
                    <h1 class="subject">' . $fields['vraag'] . '</h1>
                    <div class="content">
                        ' . $fields['aanvulling'] . '
                    </div>
                    <ul class="meta">
                        <li>
                            <abbr title="">' . $this->StringTimeDifference($fields['posttijd']) . '</abbr>
                        </li>
                    </ul>
            ';
            if ($this->getCurrentUser()) {
                echo '
                    <p class="cta">
                        <a href="?categoryid=' . $categoryid . '&questionid=' . $questionid . '&answer=1" onclick="return loadAnswerPoster(' . $categoryid . ', ' . $questionid . ');">
                            <span>
                                <span>
                                    <span>
                                        <span>
                                            Answer Question
                                        </span>
                                    </span>
                                </span>
                            </span>
                        </a>
                    </p>
                ';
            }
            echo '
                </div>
            ';
        }
    }

    public function StringTimeDifference($date1) {
        if ($this->TimeDifference($date1, time())) {
            $i = array();
            list($d, $h, $m, $s) = (array) $this->TimeDifference($date1, time());

            if ($d > 0) {
                $i[] = sprintf('%d Days', $d);
            }
            if ($h > 0) {
                $i[] = sprintf('%d Hours', $h);
            }
            if (($d == 0) && ($m > 0)) {
                $i[] = sprintf('%d Minutes', $m);
            }
            if (($h == 0) && ($s > 0)) {
                $i[] = sprintf('%d Seconds', $s);
            }

            return count($i) ? implode(' ', $i) . " ago" : 'Just Now';
        } else {
            return "Unknown";
        }
    }

    public function TimeDifference($date1, $date2) {
        $date1 = strtotime($date1);

        if (($date1 !== false) && ($date2 !== false)) {
            if ($date2 >= $date1) {
                $diff = ($date2 - $date1);

                $days = "";
                $hours = "";
                $minutes = "";
                if (intval((floor($diff / 86400)))) {
                    $days = intval((floor($diff / 86400)));
                    $diff %= 86400;
                }
                if (intval((floor($diff / 3600)))) {
                    $hours = intval((floor($diff / 3600)));
                    $diff %= 3600;
                }
                if (intval((floor($diff / 60)))) {
                    $minutes = intval((floor($diff / 60)));
                    $diff %= 60;
                }

                return array($days, $hours, $minutes, intval($diff));
            }
        }
        return false;
    }

    function getCurrentQuestion($categoryid, $questionid) {
        $result = mysql_query("SELECT `vraag` FROM `vragen` WHERE `id` = '" . $questionid . "' AND `taalid` = '" . $categoryid . "';");
        if (mysql_num_rows($result) == 1) {
            return mysql_result($result, 0);
        } else {
            return false;
        }
    }

    function showQuestions($id = "") {
        echo $this->getQuestions($id);
    }

    function getQuestions($id = "") {
        $questions = "";
        if ($id == "") {
            $result = mysql_query("SELECT * FROM `vragen`;");
        } else {
            $result = mysql_query("SELECT * FROM `vragen` WHERE `taalid` = '" . $id . "';");
        }
        if (mysql_num_rows($result) > 0) {
            while ($fields = mysql_fetch_assoc($result)) {
                $questions .= '<li><a href="?categoryid=' . $fields['taalid'] . '&questionid=' . $fields['id'] . '">' . $this->getCategoryName($fields['taalid']) . " - " . $fields['vraag'] . '</a></li>';
            }
        } else {
            $questions .= "No questions yet!";
        }

        $questions .= $this->getNewQuestionButton($id);
        return $questions;
    }

    function showNewQuestionButton($id = "") {
        echo $this->getNewQuestionButton($id);
    }

    function getNewQuestionButton($id = "") {
        $link = "";
        if ($id == "") {
            $link = '<a href="?answer=1" onclick="return loadAnswerPoster(null, null);">';
        } else {
            $link = '<a href="?categoryid=' . $id . '&answer=1" onclick="return loadAnswerPoster(' . $id . ', null);">';
        }
        $button = "";
        if ($this->getCurrentUser()) {
            $button = '
                <p class="cta">
                    ' . $link . '
                        <span>
                            <span>
                                <span>
                                    <span>
                                        New Question
                                    </span>
                                </span>
                            </span>
                        </span>
                    </a>
                </p>
            ';
        }
        return $button;
    }

    function showHeader($_GET) {
        if ($_GET && isset($_GET['questionid']) && isset($_GET['categoryid'])) {
            $question = $this->getCurrentQuestion($_GET['categoryid'], $_GET['questionid']);
            echo '
                <meta name="description" content="' . $question . '">
                <meta name="keywords" content="codedump, answers,  questions, programming, ' . $this->getCategoryName($_GET['categoryid']) . '">
                <meta name="title" content="' . $question . '">
                <title>' . $question . ' - CodeDump</title>
            ';
        } else {
            echo '
                <meta name="keywords" content="codedump, answers,  questions, programming">
                <title>CodeDump</title>
            ';
        }
    }

    function getCountries($country) {
        $countries = '<option value=""></option>';
        $result = mysql_query("SELECT * FROM `landen` ORDER BY `name`;");
        if (mysql_num_rows($result) > 0) {
            while ($fields = mysql_fetch_assoc($result)) {
                if ($fields['name'] == $country) {
                    $countries .= '<option value="' . $fields['name'] . '" selected>' . $fields['name'] . '</option>';
                } else {
                    $countries .= '<option value="' . $fields['name'] . '">' . $fields['name'] . '</option>';
                }
            }
        }
        return $countries;
    }

    function getStates($country, $state) {
        $states = '<option value="" selected></option>';
        $result = mysql_query("SELECT `name` FROM `provincies` WHERE `country_id` = (SELECT `country_id` FROM `landen` WHERE `name` = '" . $country . "') ORDER BY `name`;");
        if (mysql_num_rows($result) > 0) {
            while ($fields = mysql_fetch_assoc($result)) {
                if ($fields['name'] == $state) {
                    $states .= '<option value="' . $fields['name'] . '" selected>' . $fields['name'] . '</option>';
                } else {
                    $states .= '<option value="' . $fields['name'] . '">' . $fields['name'] . '</option>';
                }
            }
        }
        return $states;
    }

    function getCities($state, $city) {
        $states = '<option value="" selected></option>';
        $result = mysql_query("SELECT `name` FROM `plaatsen` WHERE `state_id` = (SELECT `state_id` FROM `provincies` WHERE `name` = '" . $state . "') ORDER BY `name`;");
        if (mysql_num_rows($result) > 0) {
            while ($fields = mysql_fetch_assoc($result)) {
                if ($fields['name'] == $city) {
                    $states .= '<option value="' . $fields['name'] . '" selected>' . $fields['name'] . '</option>';
                } else {
                    $states .= '<option value="' . $fields['name'] . '">' . $fields['name'] . '</option>';
                }
            }
        }
        return $states;
    }

    function getLanguages() {
        $languages = "";
        $result = mysql_query("SELECT * FROM `spreektalen` ORDER BY `name`;");
        if (mysql_num_rows($result) > 0) {
            while ($fields = mysql_fetch_assoc($result)) {
                $languages .= '<option value=' . $fields['name'] . '>' . $fields['name'] . '</option>';
            }
        } else {
            return '<option value="English">English</option>';
        }
        return $languages;
    }

    function showAnswers($categoryid, $questionid) {
        echo '
            <div id="yan-answers" class="mod">
            <div class="hd">
                <h3>
                    <strong>Answers</strong> (' . $this->getAmountOfAnswers($categoryid, $questionid) . ')
                </h3>
            </div>
            <div class="bd">
                <ul class="shown">
                    <li>
                        ' . $this->getAnswers($categoryid, $questionid) . '
                    </li>
                </ul>
            </div>
        </div>
        ';
    }

    function getAmountOfAnswers($categoryid, $questionid) {
        $result = mysql_query("SELECT * FROM `antwoorden` WHERE `taalid` = '" . $categoryid . "' AND `vraagid` = '" . $questionid . "';");
        return mysql_num_rows($result);
    }

    function getBadges($id) {
        $badges = "";
        $result = mysql_query("SELECT * FROM `skills` WHERE `id` = '" . $id . "';");
        if (mysql_num_rows($result) > 0) {      // Een of meerdere talen waar hij/zij goed in is
            while ($fields = mysql_fetch_assoc($result)) {
                $badges .= $fields['taalid'] . "_" . ( (int) ($fields['taalniveau'] / 25 ) ) . ".jpg";
            }
        }
        return $badges;
    }

    function getAnswers($categoryid, $questionid) {
        $result = mysql_query("SELECT * FROM `antwoorden` a WHERE a.taalid = '" . $categoryid . "' AND a.vraagid = '" . $questionid . "' ORDER BY (SELECT SUM(v.positive) - SUM(v.negative) FROM `votes` v WHERE v.antwoordid = a.id) DESC;");
        $answers = "";
        if (mysql_num_rows($result) > 0) {
            require $this->MainConfigFile;
            while ($fields = mysql_fetch_assoc($result)) {
                $user = $this->getUser($fields['gebruikersid']);
                $positive = mysql_result(mysql_query("SELECT SUM(`positive`) FROM `votes` WHERE `antwoordid` = '" . $fields['id'] . "';"), 0);
                $negative = mysql_result(mysql_query("SELECT SUM(`negative`) FROM `votes` WHERE `antwoordid` = '" . $fields['id'] . "';"), 0);
                if ($positive == "") {
                    $positive = 0;
                }
                if ($negative == "") {
                    $negative = 0;
                }
                $answers .= '
                    <div class="answer">
                        <div class="profile vcard">
                            <a href="index.php?userid=' . $user->id . '" class="avatar">
                                <img class="photo" src="' . $this->GetImage($user->id) . '" width="50">
                            </a>
                            <span class="user">
                                <a class="url" href="index.php?userid=' . $user->id . '">
                                    <span class="fn" title="' . $user->username . '">
                                        ' . $user->username . '

                                    </span>
                                </a>
                            </span>
                            <div class="user-badge top-contrib">
                                ' . $this->getBadges($user->id) . '
                            </div>
                        </div>
                        
                        <div class="qa-container">
                            <div style="text-align:center;float:right;">
                                <table style="width:60px;">
                                    <tr>
                                        <td style="width:30px;"><center><font color="green"><b><div id="positive_' . $fields['id'] . '">' . $positive . '</div></b></font></center></td>
                                        <td><center><font color="red"><b><div id="negative_' . $fields['id'] . '">' . $negative . '</div></b></font></center></td>
                                    </tr>
                ';
                if ($this->getCurrentUser() && $this->getCurrentUser()->id != $user->id && !$this->hasVotedOnAnswer($fields['id'])) {
                    $answers .= '
                                    <tr id="votebuttons_' . $fields['id'] . '">
                                        <form method="POST" action="' . $_SERVER['PHP_SELF'] . $this->GetQueryString($_SERVER['QUERY_STRING']) . '" onSubmit="return Vote(this, this.vote.value);">
                                            <input type="hidden" name="answerid" id="answerid" value="' . $fields['id'] . '">
                                            <input type="hidden" name="userid" id="userid" value="' . $user->id . '">
                                            <input type="hidden" name="vote" id="vote" value="">
                                            <td><input type="image" id="submit" name="submit" value="1" style="width:30px;" src="images/vote_up.gif" onClick="this.form.vote.value=1;"></td>
                                            <td><input type="image" id="submit" name="submit" value="-1" style="width:30px;" src="images/vote_down.gif" onClick="this.form.vote.value=-1;"></td>
                                        </form>
                                    </tr>
                    ';
                }
                $answers .= '
                                </table>
                            </div>
                            <div class="content">
                                ' . $fields['antwoord'] . '
                            </div>
                            <ul class="meta">
                                <li>
                                    <abbr title="' . $fields['posttijd'] . '">' . $this->StringTimeDifference($fields['posttijd']) . '</abbr>
                                </li>
                            </ul>
                        </div>
                    </div>
                ';
            }
        }
        return $answers;
    }

    function hasVotedOnAnswer($answerid) {
        $result = mysql_query("SELECT * FROM `votes` WHERE `antwoordid` = '" . $answerid . "' AND `gebruikersid`= '" . $this->getCurrentUser()->id . "';");
        if (mysql_num_rows($result) == 1) {
            return true;
        }
        return false;
    }

    function submitVote($answerid, $userid, $vote) {
        $negative = 0;
        $positive = 0;
        if ($vote < 0) {
            $negative = 1;
        } else {
            $positive = 1;
        }
        mysql_query("INSERT INTO `votes` VALUES ('" . $answerid . "', '" . $userid . "', '" . $positive . "', '" . $negative . "');");
    }

    function showAnswerPoster($title = "", $categoryid = "", $questionid = "") {
        echo $this->getAnswerPoster($title, $categoryid, $questionid);
    }

    function getAnswerPoster($title = "", $categoryid = "", $questionid = "") {
        $answerposter = "";
        if ($this->getCurrentUser()) {
            $answerposter .= '
                <div id="answerposter">
                <div id="yan-main">
                    <div id="yan-question">
                        <div class="qa-container">
                            <center>
                                <form name="Answer" id="Answer" method="POST" action="' . $_SERVER['PHP_SELF'] . str_replace("&answer=1", "", $this->GetQueryString($_SERVER["QUERY_STRING"])) . '">
                                    <table>
            ';
            if ($categoryid == "" || $questionid == "") {
                if ($categoryid == "") {
                    $answerposter .= '
                        <tr>
                            <td>
                                Category :
                                <select name="categoryid">
                                    ' . $this->getCategoriesAsOption() . '
                                </select>
                            </td>
                        </tr>
                    ';
                }
                $answerposter .= '
                    <tr>
                        <td>
                            Title :
                            <input type="text" name="title" size="50" value="' . $title . '" />
                        </td>
                    </tr>
                ';
            }
            $answerposter .= '
                <tr>
                    <td>
                        <textarea name="text" id=' . "'comment'" . ' cols=80 rows=10 style="resize:none"></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        <center>
                            <input type="button" onclick="bbcode_ins(' . "'comment'" . ', ' . "'b'" . ')" value="B" style="width:25px;font-weight:bold;" />
                            <input type="button" onclick="bbcode_ins(' . "'comment'" . ', ' . "'u'" . ')" value="_" style="width:20px;" />
                            <input type="button" onclick="bbcode_ins(' . "'comment'" . ', ' . "'i'" . ')" value="I" style="width:20px;font-style:italic;" />
                            <input type="button" onclick="bbcode_ins(' . "'comment'" . ', ' . "'img'" . ')" value="img" style="width:40px;" />
                            <input type="button" onclick="bbcode_ins(' . "'comment'" . ', ' . "'url'" . ')" value="url" style="width:40px;" />
                            <input type="button" onclick="bbcode_ins(' . "'comment'" . ', ' . "'code'" . ')" value="code" style="width:40px;" />
                            <input type="hidden" name="Answer" value="1" />
            ';
            if ($categoryid != "") {
                $answerposter .= '
                    <input type="hidden" name="categoryid" value="' . $categoryid . '">
                ';
            }
            if ($questionid != "") {
                $answerposter .= '
                    <input type="hidden" name="questionid" value="' . $questionid . '" />
                ';
            }
            $answerposter .= '
                                                </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <center>
                                                    <input type="submit" value="Submit" />
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </center>
                        </div>
                    </div>
                </div></div>
            ';
        } else {
            $answerposter .= '<script type="text/javascript">window.location = "index.php";</script>';
        }
        return $answerposter;
    }

    function submitPost($_POST) {
        if ($_POST['text'] != "" && str_replace(" ", "", $_POST['text']) != "") {
            if (!function_exists("bb2html")) {
                require "class.bbparser.php";
            }
            $_POST['text'] = mysql_real_escape_string(bb2html($_POST['text']));

            if (isset($_POST['categoryid'])) {
                if (isset($_POST['questionid']) && $_POST['questionid'] != "") {
                    $this->submitAnswer($_POST);
                } else {
                    $this->submitQuestion($_POST);
                }
            }
        } else {
            echo '<font color="red">Can\'t submit an empty question!</font>';
        }
    }

    function submitQuestion($_POST) {
        $query = "INSERT INTO `vragen` (`taalid`, `gebruikerid`, `vraag`, `aanvulling`, `beantwoord`, `posttijd`)
            VALUES ('" . $_POST['categoryid'] . "', '" . $this->getCurrentUser()->id . "', '" . $_POST['title'] . "', '" . $_POST['text'] . "', '0', now());
        ";
        mysql_query($query);
    }

    function submitAnswer($_POST) {
        $query = "INSERT INTO `antwoorden` (`vraagid`, `taalid`, `gebruikersid`, `antwoord`, `posttijd`)
            VALUES ('" . $_POST['questionid'] . "', '" . $_POST['categoryid'] . "', '" . $this->getCurrentUser()->id . "', '" . $_POST['text'] . "', now());
        ";
        mysql_query($query);
    }

    function getUser($id) {
        if (!class_exists('user')) {
            require "class.user.php";
        }
        return new user($id, "", "");
    }

    function getCurrentUser() {
        require $this->MainConfigFile;
        if ($this->User != "") {
            return $this->User;
        } else {
            if (isset($_COOKIE[$cookiename]) && $_COOKIE[$cookiename] != "") {
                $parts = explode(",", $_COOKIE[$cookiename]);
                if (!class_exists('user')) {
                    require "class.user.php";
                }
                $this->User = new user("", $parts[0], $parts[1]);
                if ($this->User == "") {
                    return false;
                }
                return $this->User;
            } else {
                return false;
            }
        }
    }

    function submitAdditional($_POST) {
        $errors = "";
        if (!isset($_POST['firstname'])) {
            $errors .= "Firstname field is empty.<br>";
        }
        if (!isset($_POST['lastname'])) {
            $errors .= "Lastname field is empty.<br>";
        }
        if (!isset($_POST['day']) || !isset($_POST['month']) || !isset($_POST['year'])) {
            $errors .= "One of the birthdate fields are empty";
        }
        if ($errors == "") {
            return true;
        } else {
            $this->getUserInfo($this->getCurrentUser()->id, $_POST, $errors);
        }
    }

    function submitEdit($_POST) {
        if ($_POST['country'] != "") {
            if ($_POST['city'] == "") {
                $this->getUserInfo($this->getCurrentUser()->id, $_POST, "");
            }
        } else {
            if ($this->getCurrentUser() && $this->EncryptPassword($_POST['oldpassword']) == $this->getCurrentUser()->password) {
                if (isset($_POST['password']) && isset($_POST['confirmpassword'])) {
                    if ($_POST['password'] != "" && $_POST['confirmpassword'] != "") {
                        if ($_POST['password'] == $_POST['confirmpassword']) {
                            if (preg_match('/^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/', $_POST['email']) &&
                                    preg_match('/^(?=.*\d)(?=.*[A-Z]*[a-z]).{6,}$/', $_POST['password'])) {
                                if (!isset($_POST['msn'])) {
                                    $_POST['msn'] = "";
                                }
                                if (!isset($_POST['skype'])) {
                                    $_POST['skype'] = "";
                                }
                                $query = "UPDATE `gebruikers` SET `wachtwoord` = '" . $this->EncryptPassword($_POST['password']) . "',
                                `email` = '" . $_POST['email'] . "', `land` = '" . $_POST['country'] . "', `provincie` = '" . $_POST['state'] . "',
                                    `stad` = '" . $_POST['city'] . "', `baan` = '" . $_POST['job'] . "', `msn` = '" . $_POST['msn'] . "', `skype` = '" . $_POST['skype'] . "'
                                        WHERE `id` = '" . $this->getCurrentUser()->id . "';";
                                mysql_query($query);
                                $this->saveImage($_FILES);
                                $this->showQuestions();
                            } else {
                                $this->getUserInfo($this->getCurrentUser()->id, $_POST, '<font color="red">' . $this->Translate("PasswordRules") . '</font>');
                            }
                        } else {
                            $this->getUserInfo($this->getCurrentUser()->id, $_POST, '<font color="red">' . $this->Translate("PasswordMatch") . '</font>');
                        }
                    } else {
                        if ($_POST['password'] != "" || $_POST['confirmpassword'] != "") {
                            $this->getUserInfo($this->getCurrentUser()->id, $_POST, '<font color="red">' . $this->Translate("PasswordEmpty") . '</font>');
                        } else {
                            if (!isset($_POST['msn'])) {
                                $_POST['msn'] = "";
                            }
                            if (!isset($_POST['skype'])) {
                                $_POST['skype'] = "";
                            }
                            $query = "UPDATE `gebruikers` SET  `email` = '" . $_POST['email'] . "', `land` = '" . $_POST['country'] . "', `provincie` = '" . $_POST['state'] . "',
                                    `stad` = '" . $_POST['city'] . "', `baan` = '" . $_POST['job'] . "', `msn` = '" . $_POST['msn'] . "', `skype` = '" . $_POST['skype'] . "'
                                        WHERE `id` = '" . $this->getCurrentUser()->id . "';";
                            mysql_query($query);
                            $this->saveImage($_FILES, $_POST);
                            $this->showQuestions();
                        }
                    }
                }
            } else {
                $this->getUserInfo($this->getCurrentUser()->id, $_POST, '<font color="red">' . $this->Translate("PassChangeMatch") . '</font>');
            }
        }
    }

    function saveImage($_FILES, $_POST = "") {
        if ($this->getCurrentUser() && $this->getCurrentUser()->id != "") {
            if ($_FILES["imageFile"]["name"] != "") {
                require $this->MainConfigFile;
                $FileType = $_FILES["imageFile"]["type"];
                $FileName = $_FILES["imageFile"]["name"];
                $FileSize = round($_FILES["imageFile"]["size"] / 1024 / 1024, 2);
                $FileError = $_FILES["imageFile"]["error"];
                if (!is_dir($SaveDir)) {
                    mkdir($SaveDir);
                }

                if ($FileSize <= $MaxFileSize) {
                    if (in_array($FileType, $AllowedFileTypes)) {
                        if (!($FileError > 0)) {
                            if (file_exists($SaveDir . $FileName)) {
                                unlink($SaveDir . $FileName);
                            }

                            $FileNamePieces = explode(".", $FileName);
                            $FileName = $this->getCurrentUser()->id . "." . $FileNamePieces[1];
                            $Moved = @move_uploaded_file($_FILES["imageFile"]["tmp_name"], $SaveDir . $FileName);

                            if ($Moved) {
                                $Dimensions = explode("x", $MaxAvatarDimension);
                                $toWidth = $Dimensions[0];
                                $toHeight = $Dimensions[1];

                                list($width, $height) = getimagesize($SaveDir . $FileName);
                                $xscale = $width / $toWidth;
                                $yscale = $height / $toHeight;
                                if ($yscale > $xscale) {
                                    $toWidth = round($width * (1 / $yscale));
                                    $toHeight = round($height * (1 / $yscale));
                                } else {
                                    $toWidth = round($width * (1 / $xscale));
                                    $toHeight = round($height * (1 / $xscale));
                                }
                                $imageTmp = imagecreatefromJPEG($SaveDir . $FileName);
                                $imageResized = imagecreatetruecolor($toWidth, $toHeight);
                                imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $toWidth, $toHeight, $width, $height);
                                imagejpeg($imageResized, $SaveDir . $FileName, 100);
                            } else {
                                $this->getUserInfo($this->getCurrentUser()->id, $_POST, '<font color="red">' . $this->Translate('SaveError') . '</font>');
                            }
                        } else {
                            $this->getUserInfo($this->getCurrentUser()->id, $_POST, '<font color="red">' . $this->Translate('ErrorCode') . ": " . $FileError . '</font>');
                        }
                    } else {
                        $this->getUserInfo($this->getCurrentUser()->id, $_POST, '<font color="red">' . $this->Translate('FileType') . ": " . $FileType . '</font>');
                    }
                } else {
                    $this->getUserInfo($this->getCurrentUser()->id, $_POST, '<font color="red">' . $this->Translate('FileBig') . $FileSize . " MB" . $this->Translate('FileSize') . " MB</font>");
                }
            }
        }
    }

    function GetImage($id) {
        require $this->MainConfigFile;
        $handle = @opendir($SaveDir);
        $File = "";
        if ($handle) {
            while (false !== ($testFile = readdir($handle))) {
                if ($testFile != "." && $testFile != "..") {
                    if (preg_match("/" . $id . "/", $testFile)) {
                        $File = $testFile;
                    }
                }
            }
            closedir($handle);
        }
        if ($File == "") {
            return "images/ffffff.gif";
        }
        return $SaveDir . $File;
    }

    function showUserInfo($id, $_POST = "", $errors = "") {
        echo $this->getUserInfo($id, $_POST, $errors);
    }

    function getUserInfo($id, $_POST = "", $errors = "") {
        $userinfo = "";
        $user = $this->getUser($id);
        $owned = ($this->getCurrentUser() && $this->getCurrentUser()->id == $id);
        if ($errors != "") {
            $userinfo .= $this->Translate("ErrorOccured");
            $userinfo .= "<br>";
            $userinfo .= $errors;
            $userinfo .= "<br>";
        }
        $userinfo .= '
            <b>' . $this->Translate("ProfileInfo") . ':</b>
            <table>
        ';
        if ($owned) {
            if ($this->getCurrentUser()->firstname == "") {
                if (!isset($_POST['firstname'])) {
                    $_POST['firstname'] = "";
                }
                if (!isset($_POST['insertion'])) {
                    $_POST['insertion'] = "";
                }
                if (!isset($_POST['lastname'])) {
                    $_POST['lastname'] = "";
                }
                if (!isset($_POST['day'])) {
                    $_POST['day'] = "";
                }
                if (!isset($_POST['month'])) {
                    $_POST['month'] = "";
                }
                if (!isset($_POST['year'])) {
                    $_POST['year'] = "";
                }
                $userinfo .= '
                    <form method="POST" id="AdditionalInfo" name="AdditionalInfo" action="index.php" onSubmit="return CheckAdditional(this, ' . Date("Y") . ');">
                    <tr>
                        <td>Firstname:</td> <td><input type="text" value="' . $_POST['firstname'] . '" name="firstname" id="firstname" onKeyup="return CheckFirstname(this, false);"><font color="RED">*</font><img src="images/ffffff.gif" id="firstnameImage"></img></td>
                    </tr>
                    <tr>
                        <td>Insertion:</td> <td><input type="text" value="' . $_POST['insertion'] . '" name="insertion"></td>
                    </tr>
                    <tr>
                        <td>Lastname:</td> <td><input type="text" value="' . $_POST['lastname'] . '" name="lastname" id="lastname" onKeyup="return CheckLastname(this, false);"><font color="RED">*</font><img src="images/ffffff.gif" id="lastnameImage"></img></td>
                    </tr>
                    <tr>
                        <td>Gender:</td>
                        <td>
                            <select name="gender">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Birthdate:</td>
                        <td>
                            <input type="text" id="day" value="' . $_POST['day'] . '" name="day" onChange="CheckBirthdate(this.form, ' . Date("Y") . ');" style="width:15px;" maxlength="2">
                                -
                            <input type="text" id="month" value="' . $_POST['month'] . '" name="month" onChange="CheckBirthdate(this.form, ' . Date("Y") . ');" style="width:15px;" maxlength="2">
                                -
                            <input type="text" id="year" value="' . $_POST['lastname'] . '" name="year" onChange="CheckBirthdate(this.form, ' . Date("Y") . ');" style="width:30px;" maxlength="4">
                            <font color="RED">*</font>
                            <img src="images/ffffff.gif" id="birthdateImage"></img>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="submit" name="btnAdditionalInfo" value="Save"></td>
                    </tr>
                    </form>
                    <tr>
                        <td><hr></td> <td><hr></td>
                    </hr>
                ';
            }
            $country = "";
            $state = "";
            $city = "";
            if (isset($_POST['country'])) {
                $country = $_POST['country'];
            } else {
                $country = $this->getCurrentUser()->country;
            }
            if (isset($_POST['state'])) {
                $state = $_POST['state'];
            } else {
                $state = $this->getCurrentUser()->state;
            }
            if (isset($_POST['city'])) {
                $city = $_POST['city'];
            } else {
                $city = $this->getCurrentUser()->city;
            }
            if (!isset($_POST['oldpassword'])) {
                $_POST['oldpassword'] = "";
            }
            if (!isset($_POST['password'])) {
                $_POST['password'] = "";
            }
            if (!isset($_POST['confirmpassword'])) {
                $_POST['confirmpassword'] = "";
            }
            if (!isset($_POST['msn'])) {
                $_POST['msn'] = $user->msn;
            }
            if (!isset($_POST['skype'])) {
                $_POST['skype'] = $user->skype;
            }
            $countries = $this->getCountries($country);
            $states = $this->getStates($country, $state);
            $cities = $this->getCities($state, $city);
            $userinfo .= '
                <form enctype="multipart/form-data" method="POST" id="ProfileEdit" name="ProfileEdit" action="' . $_SERVER['PHP_SELF'] . $this->GetQueryString($_SERVER['QUERY_STRING']) . '" onSubmit="return CheckProfileEdit(this);">
                <tr>
                    <td>Old password:</td> <td><input type="password" value="' . $_POST['oldpassword'] . '" id="oldpassword" name="oldpassword"><font color="RED">*</font></td>
                </tr>
                <tr>
                    <td>Password:</td> <td><input type="password" value="' . $_POST['password'] . '" id="password" name="password" onKeyup="return CheckPass(this.form, false);"><font color="RED">*</font><img src="images/info.gif" id="passwordImage" title="Must contain 6 characters or more of which 2 numbers or more"></img></td>
                </tr>
                <tr>
                    <td>Confirm password:</td> <td><input type="password" value="' . $_POST['confirmpassword'] . '" id="confirmpassword" name="confirmpassword" onKeyup="return CheckPass(this.form, false);"><font color="RED">*</font><img src="images/ffffff.gif" id="confirmpasswordImage"></img></td>
                </tr>
                <tr>
                    <td>Email:</td> <td><input type="text" id="email" name="email" value="' . $user->email . '" onKeyup="return CheckEmail(this, false, false);"><font color="RED">*</font><img src="images/ffffff.gif" id="emailImage"></img></td>
                </tr>
                <tr>
                    <td>Country:</td>
                    <td>
                        <select id="country" name="country" onChange="this.form.submit();">
                            ' . $countries . '
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>State/Province:</td>
                    <td>
                        <select id="state" name="state" onChange="this.form.submit();">
                            ' . $states . '
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>City:</td>
                    <td>
                        <select id="city" name="city">
                            ' . $cities . '
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>MSN:</td> <td><input type="text" name="msn" value="' . $_POST['msn'] . '"></td>
                </tr>
                <tr>
                    <td>Skype:</td> <td><input type="text" name="skype" value="' . $_POST['skype'] . '"></td>
                </tr>
                <tr>
                    <td>Job:</td> <td><input type="hidden" name="job" value="0"><input type="checkbox" name="job" value="' . $user->job . '"> Yes, i have a job</td>
                </tr>
                <tr>
                    <td>Image:</td> <td><input type="hidden" name="MAX_FILE_SIZE" value="2000000"><input type="file" name="imageFile">Maximum filesize is 2MB</td>
                </tr>
                <tr>
                    <td><input type="hidden" name="btnProfileEdit" value="1"><input type="submit" name="btnProfileEdit" value="Save"></td>
                </tr>
                <script type="text/javascript">
                    CheckPass(document.getElementById(\'ProfileEdit\'), false);
                    CheckEmail(document.getElementById(\'email\'), false, false);
                </script>
                </form>
            ';
        } else {
            $userinfo .= '
                <tr>
                    <td>Username:</td> <td>' . $user->username . '</td>
                </tr>
                <tr>
                    <td>Full name:</td> <td>' . $user->firstname . " " . $user->insertion . " " . $user->lastname . '</td>
                </tr>
                <tr>
                    <td>Email:</td> <td>' . $user->email . '</td>
                </tr>
                <tr>
                    <td>Country: </td> <td>' . $user->country . '</td>
                </tr>
                <tr>
                    <td>State: </td> <td>' . $user->state . '</td>
                </tr>
                <tr>
                    <td>City: </td> <td>' . $user->city . '</td>
                </tr>
                <tr>
                    <td>MSN: </td> <td>' . $user->msn . '</td>
                </tr>
                <tr>
                    <td>Skype: </td> <td>' . $user->skype . '</td>
                </tr>
                <tr>
                    <td>Job: </td> <td>' . $user->job . '</td>
                </tr>
            ';
        }
        $userinfo .= '
            </table>
        ';
        return $userinfo;
    }

    function showTools() {
        echo '
            <ul id="yan-question-tools">
                <li class="menu" id="yan-save-question">
                    <a href="" title="">
                        <span>Save code to my account.</span>
                    </a>
                </li>
                <li class="menu" id="yan-save-question">
                    <a href="" title="">
                        <span>Share code with another account.</span>
                    </a>
                </li>
            </ul>
        ';
    }

}

?>