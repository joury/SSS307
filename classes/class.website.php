<?php

Class website {

    var $mainConfigFile = "configs/config.php";
    var $languageDir = "languages/";
    var $user = "";
    var $correctLogin = true;
    var $db = "";

    function __construct($database) {
        $this->db = $database;
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
            echo '<font color="red">' . $this->translate("ErrorOccured") . '</font><br>';
            echo $error;
        }
        echo '
                <form name="Register" id="RegistrationForm" onSubmit="return CheckFields(this);" action="index.php" method="POST">
                <tr>
                    <td>Username:</td>
                    <td>
                        <input type="text" name="username" id="username" value="' . $_POST['username'] . '" onKeyup="return CheckUsername(this, false);">
                        <font color="RED">*</font>
                        <img src="images/ffffff.gif" id="usernameImage"></img>
                    </td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td>
                        <input type="password" name="password" id="password" value="' . $_POST['password'] . '" onKeyup="return CheckPass(this.form, false);">
                        <font color="RED">*</font>
                        <img src="images/info.gif" id="passwordImage" title="Must contain 6 characters or more of which 2 numbers or more"></img>
                    </td>
                </tr>
                <tr>
                    <td>Confirm password:</td>
                    <td>
                        <input type="password" name="confirmpassword" id="confirmpassword" value="' . $_POST['confirmpassword'] . '" onKeyup="return CheckPass(this.form, false);">
                        <font color="RED">*</font>
                        <img src="images/ffffff.gif" id="confirmpasswordImage"></img>
                    </td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>
                        <input type="text" name="email" id="email" value="' . $_POST['email'] . '" onKeyup="return CheckEmail(this, false, true);">
                        <font color="RED">*</font>
                        <img src="images/ffffff.gif" id="emailImage"></img>
                    </td>
                </tr>
                <tr>
                    <td>Job:</td>
                    <td>
                        <input type="hidden" name="job" value="0">
                        <input type="checkbox" name="job" value="1"> Yes, i have a job
                    </td>
                </tr>
                <tr>
                    <td>Site language:</td>
                    <td>
                        <select name="language">
        ';
        foreach ($this->getLanguageFiles() as $languagefile) {
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

    function translate($string) {
        require $this->mainConfigFile;
        $languagefile = "";
        if ($this->getCurrentUser()) {
            $languagefile = $LanguageDir . $this->getCurrentUser()->language . ".php";
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

    function getLanguageFiles() {
        require $this->mainConfigFile;
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

    function refreshCookie() {
        require $this->mainConfigFile;
        if ($_COOKIE[$cookiename] != "") {
            setcookie($cookiename, $_COOKIE[$cookiename], time() + ($cookietime * 60));      // Make a new cookie with the same name and same info
        }
    }

    function getQueryString() {
        $raw = $_SERVER['QUERY_STRING'];
        if ($raw != "") {
            return '?' . $raw;
        }
        return false;
    }

    function accountExists($username = "", $email = "") {
        if ($username != "" && $email != "") {
            return ($this->nameInUse($username) && $this->emailInUse($email));
        } else if ($username != "") {
            return $this->nameInUse($username);
        } else if ($email != "") {
            return $this->emailInUse($email);
        } else {
            return $this->translate("ErrorOccured") . "Both the username and the email parameters are empty!";
        }
    }

    function nameInUse($username) {
        if ($this->db->doQuery("SELECT `id` FROM `gebruikers` WHERE `gebruikersnaam` = '" . $username . "';") == false) {
            return false;
        } else {
            return true;
        }
    }

    function emailInUse($email) {
        if ($this->db->doQuery("SELECT `id` FROM `gebruikers` WHERE `email` = '" . $email . "';") == false) {
            return false;
        } else {
            return true;
        }
    }

    function checkFields($_POST) {
        $good = "";
        if ($_POST['username'] == "") {
            $good .= $this->translate("Username") . " " . $this->translate("FieldEmpty");
        }
        if ($_POST['password'] == "") {
            $good .= $this->translate("Password") . " " . $this->translate("FieldEmpty");
        }
        if ($_POST['confirmpassword'] == "") {
            $good .= $this->translate("ConfirmPassword") . " " . $this->translate("FieldEmpty");
        }
        if ($_POST['confirmpassword'] != $_POST['password']) {
            $good .= $this->translate("PasswordMatch");
        }
        if (!preg_match('/^(?=.*\d)(?=.*[A-Z]*[a-z]).{6,}$/', $_POST['password'])) {
            $good .= $this->translate("PasswordRules");
        }
        if ($_POST['email'] == "") {
            $good .= "Email " . $this->translate("FieldEmpty");
        }
        if (!preg_match('/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/', $_POST['email'])) {
            $good .= $this->translate("EmailInvalid");
        }
        if ($this->accountExists(stripslashes(mysql_real_escape_string($_POST['username'])), stripslashes(mysql_real_escape_string($_POST['email'])))) {
            $good .= $this->translate("AccountEmailUsername");
        }
        if ($good == "") {
            $good = true;
        }

        return $good;
    }

    function doRegister($_POST) {   // Begin the register function, get the $username and $password from the function call in index.php
        $check = $this->checkFields($_POST);
        if (is_bool($check)) {
            require $this->mainConfigFile;        // Get the connection variables for mysql from the config file
            if ($this->DB->makeConnection()) {
                $username = stripslashes(mysql_real_escape_string($_POST['username']));  // Make sure there are no weird tokens in the variables
                $password = stripslashes(mysql_real_escape_string($_POST['password']));
                $encryptedPass = sha1($password);
                $email = stripslashes(mysql_real_escape_string($_POST['email']));
                $raw_account_query = "INSERT INTO `gebruikers` (`gebruikersnaam`, `wachtwoord`, `email`, `taal`, `baan`) VALUES ('" . $username . "', '" . $encryptedPass . "', '" . $email . "', '" . $_POST['language'] . "', '" . $_POST['job'] . "');";
                $this->db->doQuery($raw_account_query); // Insert the account info
                $this->login($username, $password);     // Log in to the account
            } else {
                die($this->translate('NoDB'));  // If we had no connection, stop the script with the message "No DB connection"
            }
        } else {
            $this->showRegister($_POST, $check);
        }
    }

    function showLogin() {  // Show the login part (left top of index.php when not logged in)
        echo '
            <li class="me1">
                <form action="' . $_SERVER['PHP_SELF'] . $this->getQueryString() . '" name="login" method="POST">
                    <div>
                        <input type="text" name="username">
                        <input type="password" name="password">
                        <input type="submit" name="btnLogin" value="Log in">
                        <input type="submit" name="btnRegister" value="Register">
                    </div>
        ';
        if ($this->correctLogin == false) {
            echo '<font color="red">' . $this->translate("LoginFailed") . '</font>';
        }
        echo '
                </form>
            </li>
        ';
    }

    function login($username, $password) {  // Check if the variables sent are correct and set the cookie
        require $this->mainConfigFile;
        $username = stripslashes(mysql_real_escape_string($username));
        $password = sha1(stripslashes(mysql_real_escape_string($password)));
        $passwordInDB = $this->db->doQuery("SELECT `wachtwoord` FROM `gebruikers` WHERE `gebruikersnaam` = '" . $username . "';");
        if ($passwordInDB == false) {
            $this->correctLogin = false;
        } else {
            if (mysql_result($passwordInDB, 0) == $password) {   // If the Sha1 encrypted version of the posted password equals the entry in the database...
                $cookie = setcookie($cookiename, $username . "," . $password, time() + ($cookietime * 60));  // Set a cookie with "name,password" that is legit for the following 5 minutes
                echo '<meta http-equiv="refresh" content="0">';
            } else {
                $this->correctLogin = false;
            }
        }
    }

    function logout() {
        require $this->mainConfigFile;
        setcookie($cookiename, "1", time() - 3600);  // To delete a cookie, overwrite the cookie with an expiration time of "one hour ago"
        foreach (get_defined_vars() as $key) {  // Reset all variables (clear the session)
            unset($key);
        }
        echo '<meta http-equiv="refresh" content="0">';
    }

    function showLogout() {    // Show the logout button
        echo '
            <form name="LogOut" action="' . $_SERVER['PHP_SELF'] . $this->getQueryString() . '" method="POST">
                <input type="submit" name="LogOut" value="Log out">
            </form>
        ';
    }

    function getCategories($regexp = "") {
        if ($regexp == "") {
            $result = $this->db->doQuery("SELECT * FROM `talen`;");
        } else {
            $result = $this->db->doQuery("SELECT * FROM `talen` WHERE `naam` REGEXP '[" . $regexp . "]';");
        }
        return $result;
    }

    function getCategoryLinks($_GET = "", $regexp = "") {
        $categories = "";
        $result = $this->getCategories($regexp);
        if ($result == false) {
            $categories .= "<li>No results!</li>";
        } else {
            while ($fields = mysql_fetch_assoc($result)) {
                if ($_GET && isset($_GET['categoryid']) && $fields['id'] == $_GET['categoryid']) {
                    $categories .= '<li class="current">';
                    $categories .= '<a class="current" id="category_' . $fields['id'] . '" href="?categoryid=' . $fields['id'] . '" onclick="return loadQuestions(' . $fields['id'] . ');">' . $fields['naam'] . '</a>';
                    $categories .= '</li>';
                } else {
                    $categories .= '<li>';
                    $categories .= '<a id="category_' . $fields['id'] . '" href="?categoryid=' . $fields['id'] . '" onclick="return loadQuestions(' . $fields['id'] . ');">' . $fields['naam'] . '</a>';
                    $categories .= "</li>";
                }
            }
        }
        return $categories;
    }

    function getCategoriesAsOption() {
        $categories = "";
        $result = $this->db->doQuery("SELECT * FROM `talen`;");
        if ($result != false) {
            while ($fields = mysql_fetch_assoc($result)) {
                $categories .= '<option value="' . $fields['id'] . '">' . $fields['naam'] . '</option>';
            }
        }
        return $categories;
    }

    function showBanner($_GET) {
        echo '
            <div id="hd">
                <div id="ygma">
                    <div id="ygmaheader">
                        <div class="bd sp">
                            <div id="ymenu" class="ygmaclr">
                                <div id="mepanel">
                                    <ul id="mepanel-nav">
        ';
        if ($this->getCurrentUser()) {
            $this->showLogout();
        } else {
            $this->showLogin();
        }
        echo '
                                    </ul>
                                </div>
                            </div>
                            <div id="yahoo" class="ygmaclr">
                                <div id="ygmabot">
                                    <img id="ygmalogoimg" src="./images/logo.png" alt="CodeDump!" height="26" width="257">
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
                                        <input class="default" maxlength="110" id="banner-answer" name="query" type="text" onkeyup="handleSearch(this.form);">
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
        //  Code voor het "selecteren" van de tabs, vervangt de neutrale tabcode met een tab met class=current menu, css regelt de rest
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

    function getCurrentCategory($categoryid) {
        $categorycode = "";
        $categoryname = $this->getCategoryName($categoryid);
        if ($categoryname) {
            $categorycode .= '
                <li id="categoryindex">
                    <a href="?categoryid=' . $categoryid . '" onclick="return loadQuestions(' . $categoryid . ');">' . $categoryname . '</a> &gt
                </li>
            ';
        }
        return $categorycode;
    }

    function getCategoryName($categoryid) {
        $result = $this->db->doQuery("SELECT `naam` FROM `talen` WHERE `id` = '" . $categoryid . "';");
        if ($result != false) {
            return mysql_result($result, 0);
        } else {
            return false;
        }
    }

    function getQuestion($categoryid, $questionid) {
        $question = "";
        $result = $this->db->doQuery("SELECT * FROM `vragen` WHERE `id` = '" . $questionid . "' AND `taalid` = '" . $categoryid . "';");
        if ($result != false) {
            $fields = mysql_fetch_assoc($result);
            $question .= '
                <div id="profile" class="profile vcard">
                    <a href="index.php?userid=' . $fields['gebruikerid'] . '" onclick="return loadProfile(' . $fields['gebruikerid'] . ');" class="avatar">
                        <img class="photo" src="' . $this->getImage($fields['gebruikerid']) . '" width="50">
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
                    <h1 id="subject" class="subject">' . $fields['vraag'] . '</h1>
                    <div class="content">
                        ' . $fields['aanvulling'] . '
                    </div>
                    <ul class="meta">
                        <li>
                            <abbr title="">' . $this->getTimeDifference($fields['posttijd']) . '</abbr>
                        </li>
                    </ul>
            ';
            if ($this->getCurrentUser()) {
                $question .= '
                    <p class="cta">
                        <a href="?categoryid=' . $categoryid . '&amp;questionid=' . $questionid . '&amp;answer=1" onclick="return loadAnswerPoster(' . $categoryid . ', ' . $questionid . ');">
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
            $question .= '
                </div>
            ';
        }
        return $question;
    }

    public function getTimeDifference($date1) {
        $date1 = strtotime($date1);
        $date2 = time();
        $timeDiff = false;
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

                $timeDiff = array($days, $hours, $minutes, intval($diff));
            }
        }

        if ($timeDiff != false) {
            $i = array();
            list($d, $h, $m, $s) = (array) $timeDiff;

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

    function getCurrentQuestion($categoryid, $questionid) {
        $result = $this->db->doQuery("SELECT `vraag` FROM `vragen` WHERE `id` = '" . $questionid . "' AND `taalid` = '" . $categoryid . "';");
        if ($result != false) {
            return mysql_result($result, 0);
        } else {
            return false;
        }
    }

    function getQuestions($categoryid = "", $regexp = "") {
        if ($regexp == "") {
            if ($categoryid == "") {
                $query = "SELECT * FROM `vragen`;";
            } else {
                $query = "SELECT * FROM `vragen` WHERE `taalid` = '" . $categoryid . "';";
            }
        } else {
            if ($categoryid == "") {
                $query = "SELECT * FROM `vragen` WHERE `vraag` REGEXP '" . $regexp . "';";
            } else {
                $query = "SELECT * FROM `vragen` WHERE `taalid` = '" . $categoryid . "' AND `vraag` REGEXP '" . $regexp . "';";
            }
        }
        return $this->db->doQuery($query);
    }

    function getQuestionsMenu($categoryid = "", $regexp = "") {
        $questions = "";
        $result = $this->getQuestions($categoryid, $regexp);
        if ($result != false) {
            $questions .= "<ul>";
            while ($fields = mysql_fetch_assoc($result)) {
                $questions .= '
                    <li>
                        <a href="?categoryid=' . $fields['taalid'] . '&amp;questionid=' . $fields['id'] . '" onclick="return loadQuestion(' . $fields['taalid'] . ', ' . $fields['id'] . ');">
                            ' . $this->getCategoryName($fields['taalid']) . " - " . $fields['vraag'] . '
                        </a>
                    </li>
                ';
            }
            $questions .="</ul>";
        } else {
            $questions .= "<li>No results!</li>";
        }

        $questions .= $this->getNewQuestionButton($categoryid);
        return $questions;
    }

    function getNewQuestionButton($id = "") {
        $link = "";
        if ($id == "") {
            $link = '<a href="?answer=1" onclick="return loadAnswerPoster(null, null);">';
        } else {
            $link = '<a href="?categoryid=' . $id . '&amp;answer=1" onclick="return loadAnswerPoster(' . $id . ', null);">';
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

    function getCountries($country = "") {
        $countries = '<option value=""></option>';
        $result = $this->db->doQuery("SELECT * FROM `landen` ORDER BY `name`;");
        if ($result != false) {
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

    function getStates($country, $state = "") {
        $states = '<option value="" selected></option>';
        $result = $this->db->doQuery("SELECT `name` FROM `provincies` WHERE `country_id` = (SELECT `country_id` FROM `landen` WHERE `name` = '" . $country . "') ORDER BY `name`;");
        if ($result != false) {
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

    function getCities($country, $state, $city = "") {
        $states = '<option value="" selected></option>';
        $result = $this->db->doQuery("SELECT `name` FROM `plaatsen` WHERE `state_id` = 
            (SELECT `state_id` FROM `provincies` WHERE `name` = '" . $state . "' AND `country_id` =
                (SELECT `country_id` FROM `landen` WHERE `name` = '" . $country . "')
             ) ORDER BY `name`;");
        if ($result != false) {
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
        $result = $this->db->doQuery("SELECT * FROM `spreektalen` ORDER BY `name`;");
        if ($result != false) {
            while ($fields = mysql_fetch_assoc($result)) {
                $languages .= '<option value=' . $fields['name'] . '>' . $fields['name'] . '</option>';
            }
        } else {
            return '<option value="English">English</option>';
        }
        return $languages;
    }

    function getAnswerDiv($categoryid, $questionid) {
        return '
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

    function removeAnswer($answerid) {
        if ($this->getCurrentUser()) {
            $this->db->doQuery("DELETE FROM `antwoorden` WHERE `id` = '" . $answerid . "' AND `gebruikersid` = '" . $this->getCurrentUser()->id . "';");
        }
    }

    function getAmountOfAnswers($categoryid, $questionid) {
        return $this->db->getRowCount("SELECT * FROM `antwoorden` WHERE `taalid` = '" . $categoryid . "' AND `vraagid` = '" . $questionid . "';");
    }

    function getBadges($id) {
        $badges = "";
        $result = $this->db->doQuery("SELECT * FROM `skills` WHERE `id` = '" . $id . "';");
        if ($result != false) {      // Een of meerdere talen waar hij/zij goed in is
            while ($fields = mysql_fetch_assoc($result)) {
                $badges .= $fields['taalid'] . "_" . ( (int) ($fields['taalniveau'] / 25 ) ) . ".jpg";
            }
        }
        return $badges;
    }

    function getNegativeVotes($userID, $categoryID = "", $answerID = "") {
        $result = "";
        if ($categoryID != "") {
            $query = "SELECT SUM(`negative`) FROM `votes` WHERE `gebruikersid` = '" . $userID . "' AND `antwoordid` IN (SELECT `id` FROM `antwoorden` WHERE `taalid` = '" . $categoryID . "');";
            $result = mysql_result($this->db->doQuery($query), 0);
            if ($result == "") {
                $result = 0;
            }
        } else if ($answerID != "") {
            $result = mysql_result($this->db->doQuery("SELECT SUM(`negative`) FROM `votes` WHERE `antwoordid` = '" . $answerID . "';"), 0);
            if ($result == "") {
                $result = 0;
            }
        }
        return $result;
    }

    function getPositiveVotes($userID, $categoryID = "", $answerID = "") {
        $result = "";
        if ($categoryID != "") {
            $query = "SELECT SUM(`positive`) FROM `votes` WHERE `gebruikersid` = '" . $userID . "' AND `antwoordid` IN (SELECT `id` FROM `antwoorden` WHERE `taalid` = '" . $categoryID . "');";
            $result = mysql_result($this->db->doQuery($query), 0);
            if ($result == "") {
                $result = 0;
            }
        } else if ($answerID != "") {
            $result = mysql_result($this->db->doQuery("SELECT SUM(`positive`) FROM `votes` WHERE `antwoordid` = '" . $answerID . "';"), 0);
            if ($result == "") {
                $result = 0;
            }
        }
        return $result;
    }

    function getAnswers($categoryid, $questionid) {
        $result = $this->db->doQuery("SELECT * FROM `antwoorden` a WHERE a.taalid = '" . $categoryid . "' AND a.vraagid = '" . $questionid . "' ORDER BY (SELECT SUM(v.positive) - SUM(v.negative) FROM `votes` v WHERE v.antwoordid = a.id) DESC;");
        $answers = '';
        if ($result != false) {
            require $this->mainConfigFile;
            while ($fields = mysql_fetch_assoc($result)) {
                $user = $this->getUser($fields['gebruikersid']);
                $positive = $this->getPositiveVotes($user->id, "", $fields['id']);
                $negative = $this->getNegativeVotes($user->id, "", $fields['id']);
                $answers .= '
                    <div class="answer">
                        <div class="profile vcard">
                            <a href="index.php?userid=' . $user->id . '" class="avatar">
                                <img class="photo" src="' . $this->getImage($user->id) . '" width="50">
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
                            <div class="vote-container">
                ';
                if ($this->getCurrentUser() && $this->getCurrentUser()->id == $user->id) {
                    $answers .= '
                        <div class="answercontrol-container">
                            <a href="' . $this->getQueryString() . '&edit=' . $fields['id'] . '"><img src="images/edit.gif"></a>
                            <a href="' . $this->getQueryString() . '&remove=' . $fields['id'] . '"><img src="images/incorrect.gif"></a>
                        </div>
                    ';
                }
                $answers .= '
                    <table class="votepoint-container">
                        <tr>
                            <td class="votepoint"><center><font color="green"><b><div id="positive_' . $fields['id'] . '">' . $positive . '</div></b></font></center></td>
                            <td class="votepoint"><center><font color="red"><b><div id="negative_' . $fields['id'] . '">' . $negative . '</div></b></font></center></td>
                        </tr>
                ';
                if ($this->getCurrentUser() && $this->getCurrentUser()->id != $user->id) {
                    if (!$this->hasVotedOnAnswer($fields['id'])) {
                        $answers .= '
                            <tr id="votebuttons_' . $fields['id'] . '">
                                <form method="POST" action="' . $_SERVER['PHP_SELF'] . $this->getQueryString() . '" onSubmit="return Vote(this, this.vote.value);">
                                    <input type="hidden" name="answerid" id="answerid" value="' . $fields['id'] . '">
                                    <input type="hidden" name="questionid" id="questionid" value="' . $fields['vraagid'] . '">
                                    <input type="hidden" name="categoryid" id="categoryid" value="' . $fields['taalid'] . '">
                                    <input type="hidden" name="userid" id="userid" value="' . $user->id . '">
                                    <input type="hidden" name="vote" id="vote" value="">
                                    <td><input type="image" id="submit" name="submit" value="1" style="width:30px;" src="images/vote_up.gif" onClick="this.form.vote.value=1;"></td>
                                    <td><input type="image" id="submit" name="submit" value="-1" style="width:30px;" src="images/vote_down.gif" onClick="this.form.vote.value=-1;"></td>
                                </form>
                            </tr>
                        ';
                    }
                }
                $answers .= '
                                </table>
                            </div>
                            <div class="content">
                                ' . $fields['antwoord'] . '
                            </div>
                            <ul class="meta">
                                <li>
                                    <abbr title="' . $fields['posttijd'] . '">' . $this->getTimeDifference($fields['posttijd']) . '</abbr>
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
        $result = $this->db->doQuery("SELECT * FROM `votes` WHERE `antwoordid` = '" . $answerid . "' AND `gebruikersid`= '" . $this->getCurrentUser()->id . "';");
        return ($result != false);
    }

    function submitVote($answerid, $userid, $vote) {
        $negative = 0;
        $positive = 0;
        if ($vote < 0) {
            $negative = 1;
        } else {
            $positive = 1;
        }
        $this->db->doQuery("INSERT INTO `votes` VALUES ('" . $answerid . "', '" . $userid . "', '" . $positive . "', '" . $negative . "');");
    }

    function getAnswerPoster($title = "", $categoryid = "", $questionid = "") {
        $answerposter = "";
        if ($this->getCurrentUser()) {
            $answerposter .= '
                <div id="yan-main">
                    <div id="yan-question">
                        <div class="qa-container">
                            <center>
                                <form name="Answer" id="Answer" method="POST" action="' . $_SERVER['PHP_SELF'] . str_replace("&amp;answer=1", "", $this->getQueryString()) . '">
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
                </div>
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
            $_POST['text'] = stripslashes(mysql_real_escape_string(bb2html($_POST['text'])));

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
        $query = "SET `time_zone` = '+02:00';";
        $this->db->doQuery($query);
        $query = "INSERT INTO `vragen` (`taalid`, `gebruikerid`, `vraag`, `aanvulling`, `beantwoord`, `posttijd`)
            VALUES ('" . $_POST['categoryid'] . "', '" . $this->getCurrentUser()->id . "', '" . $_POST['title'] . "', '" . $_POST['text'] . "', '0', now());
        ";
        $this->db->doQuery($query);
    }

    function submitAnswer($_POST) {
        $query = "SET `time_zone` = '+02:00';";
        $this->db->doQuery($query);
        $query = "INSERT INTO `antwoorden` (`vraagid`, `taalid`, `gebruikersid`, `antwoord`, `posttijd`)
            VALUES ('" . $_POST['questionid'] . "', '" . $_POST['categoryid'] . "', '" . $this->getCurrentUser()->id . "', '" . $_POST['text'] . "', now());
        ";
        $this->db->doQuery($query);
    }

    function getUsers($regexp = "") {
        if ($regexp == "") {
            $query = "SELECT * FROM `gebruikers`;";
        } else {
            $query = "SELECT * FROM `gebruikers` WHERE `gebruikersnaam` REGEXP '" . $regexp . "';";
        }
        $result = $this->db->doQuery($query);
        if ($result != false) {
            while ($fields = mysql_fetch_assoc($result)) {
                return '
                    <li>
                        <a href="index.php?userid=' . $fields['id'] . '">
                            ' . ucfirst($fields['gebruikersnaam']) . '
                        </a>
                    </li>
                ';
            }
        } else {
            return "<li>None.</li>";
        }
    }

    function getUser($id) {
        if (!class_exists('user')) {
            require "class.user.php";
        }
        return new user($this->db, $id, "", "");
    }

    function getCurrentUser() {
        require $this->mainConfigFile;
        if ($this->user != "") {
            return $this->user;
        } else {
            if (isset($_COOKIE[$cookiename]) && $_COOKIE[$cookiename] != "") {
                $parts = explode(",", $_COOKIE[$cookiename]);
                if (!class_exists('user')) {
                    require "class.user.php";
                }
                $this->user = new user($this->db, "", $parts[0], $parts[1]);
                if ($this->user == "") {
                    return false;
                }
                return $this->user;
            } else {
                return false;
            }
        }
    }

    function submitAdditional($_POST) {
        $birthdate = "";
        if (isset($_POST['year']) && isset($_POST['month']) && $_POST['day']) {
            $currentyear = Date("Y");
            if ($_POST['year'] == "") {
                $_POST['year'] = "0000";
            }
            if ($_POST['month'] == "") {
                $_POST['month'] = "00";
            }
            if ($_POST['day'] == "") {
                $_POST['day'] = "00";
            }
            $birthdate = $_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['day'];
            if ($_POST['year'] < $currentyear - 100 || $_POST['year'] > $currentyear - 8) {
                echo $this->getUserInfo($this->getCurrentUser()->id, $_POST, '<font color="red">You can\'t be born in ' . $birthdate . '</font>');
            }
        }
        $query = "UPDATE `gebruikers` SET ";
        if (isset($_POST['firstname'])) {
            $query .= "`voornaam` = '" . $_POST['firstname'] . "', ";
        }
        if (isset($_POST['insertion'])) {
            $query .= "`tussenvoegsel` = '" . $_POST['insertion'] . "', ";
        }
        if (isset($_POST['lastname'])) {
            $query .= "`achternaam` = '" . $_POST['lastname'] . "', ";
        }
        if (isset($_POST['gender'])) {
            $query .= "`geslacht` = '" . $_POST['gender'] . "', ";
        }
        if ($birthdate != "") {
            $query .= "`geboortedatum` = '" . $birthdate . "', ";
        }

        if (strlen($query) > 24) {
            $query = substr($query, 0, strlen($query) - 2) . ";";
            $this->db->doQuery($query);
        }
        echo $this->getUserInfo($this->getCurrentUser()->id, $_POST, "");
    }

    function submitProfileEdit($_POST) {
        if ($_POST['country'] != "") {  // Als we wel een land hebben ingesteld
            if ($_POST['city'] != "") { // Dan moeten we ook een stad kiezen
                if ($this->getCurrentUser() && sha1($_POST['oldpassword']) == $this->getCurrentUser()->password) {    // Als we een gebruiker hebben en zijn ingevulde wachtwoord klopt
                    if (isset($_POST['password']) && isset($_POST['confirmpassword'])) {    //  Als beide password velden zijn ingevuld
                        if ($_POST['password'] != "" && $_POST['confirmpassword'] != "") {
                            if ($_POST['password'] == $_POST['confirmpassword']) {  // Als ze gelijk zijn
                                if (preg_match('/^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/', $_POST['email']) && // Email bestaat uit karakters a-z, A-Z, 0-9, _, en - | Het moet een punt en een @ bevatten
                                        preg_match('/^(?=.*\d)(?=.*[A-Z]*[a-z]).{6,}$/', $_POST['password'])) { // Wachtwoord bestaat uit A-Z, a-z, 0-9 en het minimum karakters is 6
                                    $this->doProfileEdit($_POST);
                                } else {
                                    echo $this->getUserInfo($this->getCurrentUser()->id, $_POST, '<font color="red">' . $this->translate("PasswordRules") . '</font>');  // Stuur de gebruiker terug naar de Profielpagina en stuur de error mee
                                }
                            } else {
                                echo $this->getUserInfo($this->getCurrentUser()->id, $_POST, '<font color="red">' . $this->translate("PasswordMatch") . '</font>');
                            }
                        } else {
                            $this->doProfileEdit($_POST);
                        }
                    } else {
                        echo $this->getUserInfo($this->getCurrentUser()->id, $_POST, '<font color="red">' . $this->translate("UnknownError") . '</font>');
                    }
                } else {
                    echo $this->getUserInfo($this->getCurrentUser()->id, $_POST, '<font color="red">' . $this->translate("PassChangeMatch") . '</font>');
                }
            } else {
                echo $this->getUserInfo($this->getCurrentUser()->id, $_POST, "");
            }
        } else {
            $this->doProfileEdit($_POST);
        }
    }

    function doProfileEdit($_POST) {
        if (!isset($_POST['msn'])) {
            $_POST['msn'] = "";
        }
        if (!isset($_POST['skype'])) {
            $_POST['skype'] = "";
        }
        if (isset($_POST['password'])) {
            $query = "UPDATE `gebruikers` SET `wachtwoord` = '" . sha1($_POST['password']) . "',
                        `email` = '" . $_POST['email'] . "', `land` = '" . $_POST['country'] . "', `provincie` = '" . $_POST['state'] . "',
                            `stad` = '" . $_POST['city'] . "', `baan` = '" . $_POST['job'] . "', `msn` = '" . $_POST['msn'] . "', `skype` = '" . $_POST['skype'] . "'
                                WHERE `id` = '" . $this->getCurrentUser()->id . "';";
        } else {
            if (isset($_POST['country'])) {
                $query = "UPDATE `gebruikers` SET  `email` = '" . $_POST['email'] . "', `land` = '" . $_POST['country'] . "', `provincie` = '" . $_POST['state'] . "',
                            `stad` = '" . $_POST['city'] . "', `baan` = '" . $_POST['job'] . "', `msn` = '" . $_POST['msn'] . "', `skype` = '" . $_POST['skype'] . "'
                                WHERE `id` = '" . $this->getCurrentUser()->id . "';";
            } else {
                $query = "UPDATE `gebruikers` SET  `email` = '" . $_POST['email'] . "', `baan` = '" . $_POST['job'] . "', `msn` = '" . $_POST['msn'] . "', `skype` = '" . $_POST['skype'] . "'
                                WHERE `id` = '" . $this->getCurrentUser()->id . "';";
            }
        }
        $this->db->doQuery($query);
        $result = $this->saveImage($_FILES);  // Stuur de $_FILES variabele door naar de functie die het plaatje gaat verwerken
        if ($result == true) {
            echo $this->getUserInfo($this->getCurrentUser()->id, "", true);
        } else {
            echo $this->getUserInfo($this->getCurrentUser()->id, "", '<font color="red">' . $result . '</font>');
        }
    }

    function saveImage($_FILES) {
        if ($this->getCurrentUser() && $this->getCurrentUser()->id != "") {
            if ($_FILES["imageFile"]["name"] != "") {
                require $this->mainConfigFile;
                $FileSize = round($_FILES["imageFile"]["size"] / 1024 / 1024, 2);
                if (!is_dir($SaveDir)) {
                    mkdir($SaveDir);
                }
                if ($FileSize <= $MaxFileSize) {    // Als het bestand niet groter is dan toegestaan
                    if (in_array($_FILES["imageFile"]["type"], $AllowedFileTypes)) {    // Als de bestandsextensie in de lijst met toegestane extensies staat
                        if ($_FILES["imageFile"]["error"] == 0) {
                            $FileNamePieces = explode(".", $_FILES["imageFile"]["name"]);
                            $FileName = $this->getCurrentUser()->id . "." . $FileNamePieces[1];
                            if (file_exists($SaveDir . $FileName)) {
                                unlink($SaveDir . $FileName);
                            }
                            if (@move_uploaded_file($_FILES["imageFile"]["tmp_name"], $SaveDir . $FileName)) {
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
                                //  Plaatje resizen tot de maximaal toegestane grootte met behoud van verhoudingen... zo min mogelijk kwaliteitsverlies
                                $imageTmp = imagecreatefromJPEG($SaveDir . $FileName);
                                $imageResized = imagecreatetruecolor($toWidth, $toHeight);
                                imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $toWidth, $toHeight, $width, $height);
                                imagejpeg($imageResized, $SaveDir . $FileName, 100);
                                return true;
                            } else {
                                return $this->translate('SaveError');
                            }
                        } else {
                            return $this->translate('ErrorCode') . ": " . $_FILES["imageFile"]["error"];
                        }
                    } else {
                        return $this->translate('FileType') . ": " . $_FILES["imageFile"]["type"];
                    }
                } else {
                    return $this->translate('FileBig') . $FileSize . " MB " . $this->translate('FileSize') . $MaxFileSize . " MB";
                }
            } else {
                return true;
            }
        } else {
            return $this->translate('UserLost');
        }
    }

    function getImage($id) {
        require $this->mainConfigFile;
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

    function getAdditionalInfoForm($_POST, $user) {
        if (!isset($_POST['firstname'])) {
            $_POST['firstname'] = $this->getCurrentUser()->firstname;
        }
        if (!isset($_POST['insertion'])) {
            $_POST['insertion'] = $this->getCurrentUser()->insertion;
        }
        if (!isset($_POST['lastname'])) {
            $_POST['lastname'] = $this->getCurrentUser()->lastname;
        }
        if (!isset($_POST['day'])) {
            $_POST['day'] = $this->getCurrentUser()->getDay();
        }
        if (!isset($_POST['month'])) {
            $_POST['month'] = $this->getCurrentUser()->getMonth();
        }
        if (!isset($_POST['year'])) {
            $_POST['year'] = $this->getCurrentUser()->getYear();
        }
        $formStart = '<form method="POST" id="AdditionalInfo" name="AdditionalInfo" action="' . $_SERVER['PHP_SELF'] . $this->getQueryString() . '" onSubmit="return CheckAdditional(this, ' . Date("Y") . ');">';
        $additionalInfoForm = "";
        if ($_POST['firstname'] == "") {
            $additionalInfoForm .= '
                <tr>
                    <td>Firstname:</td> <td><input type="text" value="' . $_POST['firstname'] . '" name="firstname" id="firstname" onKeyup="return CheckFirstname(this, false);"><img src="images/ffffff.gif" id="firstnameImage"></img></td>
                </tr>
            ';
        }
        if ($_POST['insertion'] == "" && ($_POST['firstname'] == "" || $_POST['lastname'] == "")) {
            $additionalInfoForm .= '
                <tr>
                    <td>Insertion:</td> <td><input type="text" value="' . $_POST['insertion'] . '" name="insertion"></td>
                </tr>
            ';
        }
        if ($_POST['lastname'] == "") {
            $additionalInfoForm .= '
                <tr>
                    <td>Lastname:</td> <td><input type="text" value="' . $_POST['lastname'] . '" name="lastname" id="lastname" onKeyup="return CheckLastname(this, false);"><img src="images/ffffff.gif" id="lastnameImage"></img></td>
                </tr>
            ';
        }
        if ($this->getCurrentUser()->gender == "") {
            $additionalInfoForm .= '
                <tr>
                    <td>Gender:</td>
                    <td>
                        <select name="gender">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </td>
                </tr>
            ';
        }
        if ($_POST['day'] == "" || $_POST['month'] == "" || $_POST['year'] == "") {
            $additionalInfoForm .= '
                <tr>
                    <td>Birthdate:</td>
                    <td>
                        <input type="text" id="day" value="' . $_POST['day'] . '" name="day" onkeyup="CheckBirthdate(this.form, ' . Date("Y") . ', false);" style="width:15px;" maxlength="2">
                            -
                        <input type="text" id="month" value="' . $_POST['month'] . '" name="month" onkeyup="CheckBirthdate(this.form, ' . Date("Y") . ', false);" style="width:15px;" maxlength="2">
                            -
                        <input type="text" id="year" value="' . $_POST['year'] . '" name="year" onkeyup="CheckBirthdate(this.form, ' . Date("Y") . ', false);" style="width:30px;" maxlength="4">
                        <img src="images/ffffff.gif" id="birthdateImage"></img>
                    </td>
                </tr>
            ';
        }
        $formEnd = '
            <tr>
                <td><input type="submit" name="btnAdditionalInfo" value="Save"></td>
            </tr>
            </form>
            <tr>
                <td>
                    <hr>
                </td>
                <td>
                    <hr>
                </td>
            </tr>
        ';
        if ($additionalInfoForm == "") {
            return "";
        } else {
            return $formStart . $additionalInfoForm . $formEnd;
        }
    }

    function getProfileEditForm($_POST, $user) {
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
        return '
            <form enctype="multipart/form-data" method="POST" id="ProfileEdit" name="ProfileEdit" action="' . $_SERVER['PHP_SELF'] . $this->getQueryString() . '" onSubmit="return CheckProfileEdit(this);">
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
                    <select id="country" name="country" onChange="handleCountryChange(this.form);">
                        ' . $this->getCountries($country) . '
                    </select>
                </td>
            </tr>
            <tr>
                <td>State/Province:</td>
                <td>
                    <select id="state" name="state" onChange="handleStateChange(this.form);">
                        ' . $this->getStates($country, $state) . '
                    </select>
                </td>
            </tr>
            <tr>
                <td>City:</td>
                <td>
                    <select id="city" name="city">
                        ' . $this->getCities($country, $state, $city) . '
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
    }

    function getStaticUserInfo($user) {
        $staticUserInfo = '
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
            </table>
            <br>
            <hr>
            <br>
            <b>Score per category:</b>
            <table>
        ';
        $result = $this->getCategories();
        if ($result != false) {
            while ($fields = mysql_fetch_assoc($result)) {
                $positive = $this->getPositiveVotes($user->id, $fields['id']);
                $negative = $this->getNegativeVotes($user->id, $fields['id']);
                $score = $positive - $negative;
                if ($score > 0) {
                    $score = '<font color="green">' . $score . '</font>';
                } else if ($score < 0) {
                    $score = '<font color="red">' . $score . '</font>';
                }
                $staticUserInfo .= '
                    <tr>
                        <td>' . $fields['naam'] . '</td> <td><b>' . $score . '</b></td>
                    </tr>
                ';
            }
        }
        return $staticUserInfo;
    }

    function getUserInfo($id, $_POST = "", $errors = "") {
        $userinfo = "";
        $user = $this->getUser($id);
        $owned = ($this->getCurrentUser() && $this->getCurrentUser()->id == $id);
        if ($errors != "") {
            if ($errors == true) {
                $userinfo .= $this->translate("SuccesfullyUpdated");
                $userinfo .= "<br>";
            } else {
                $userinfo .= $this->translate("ErrorOccured");
                $userinfo .= "<br>";
                $userinfo .= $errors;
                $userinfo .= "<br>";
            }
        }
        $userinfo .= '
            <b>' . $this->translate("ProfileInfo") . ':</b>
            <table>
        ';
        if ($owned) {
            if (isset($_GET['edit'])) {
                $userinfo .= $this->getAdditionalInfoForm($_POST, $user);
                $userinfo .= $this->getProfileEditForm($_POST, $user);
            } else {
                $userinfo .= $this->getStaticUserInfo($user);
                $userinfo .= '
                    <tr>
                        <td>
                            <a href="?userid=' . $id . '&edit=1">Edit your profile</a>
                        </td>
                    </tr>
                ';
            }
        } else {
            $userinfo .= $this->getStaticUserInfo($user);
        }
        $userinfo .= '
            </table>
        ';
        return $userinfo;
    }

    function getTools() {
        return '
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