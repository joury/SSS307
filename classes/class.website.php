<?php

Class website {

    var $DB;
    var $MainConfigFile = "configs/config.php";
    var $LanguageDir = "languages/";
    var $User = "";

    function __construct($db) {
        $this->DB = $db;
        $this->getCurrentUser();
    }

    function IsLoggedIn() {
        if ($this->User != "") {
            return true;
        } else {
            return false;
        }
    }

    function fillRegisterPost($_POST) {
        if (!isset($_POST['confirmpassword'])) {
            $_POST['confirmpassword'] = $_POST['password'];
        }
        if (!isset($_POST['email'])) {
            $_POST['email'] = "";
        }
        if (!isset($_POST['country'])) {
            $_POST['country'] = "";
        }
        if (!isset($_POST['state'])) {
            $_POST['state'] = "";
        }
        if (!isset($_POST['city'])) {
            $_POST['city'] = "";
        }
        if (!isset($_POST['firstname'])) {
            $_POST['firstname'] = "";
        }
        if (!isset($_POST['insertion'])) {
            $_POST['insertion'] = "";
        }
        if (!isset($_POST['lastname'])) {
            $_POST['lastname'] = "";
        }
        if (!isset($_POST['msn'])) {
            $_POST['msn'] = "";
        }
        if (!isset($_POST['skype'])) {
            $_POST['skype'] = "";
        }
        if (!isset($_POST['job'])) {
            $_POST['job'] = "";
        }
        if (!isset($_POST['gender'])) {
            $_POST['gender'] = "";
        }
        if (!isset($_POST['language'])) {
            $_POST['language'] = "";
        }
        return $_POST;
    }

    function showRegister($_POST, $error = 0) {
        $days = "";
        $months = "";
        $years = "";
        for ($i = 1; $i <= 31; $i++) {
            if ($i < 10) {
                $i = "0" . $i;
            }
            $days .= "<option value='$i'>$i</option>";
        }
        for ($i = 1; $i <= 12; $i++) {
            if ($i < 10) {
                $i = "0" . $i;
            }
            $months .= "<option value='$i'>$i</option>";
        }
        for ($i = (date("Y") - 85); $i <= date("Y") - 4; $i++) {
            if ($i == (date("Y") - 4)) {
                $years .= "<option value='$i' SELECTED>$i</option>";
            } else {
                $years .= "<option value='$i'>$i</option>";
            }
        }

        $_POST = $this->fillRegisterPost($_POST);

        echo '
            <table>
        ';
        if ($error == 1) {
            echo 'Not all fields were filled in, please check if all fields with a <font color="red">*</font> are filled in.';
        }
        echo '
                <form name="Register" id="RegistrationForm" onSubmit="return CheckFields();" action="index.php" method="POST">
                <tr>
                    <td>Username:</td> <td><input type="text" name="username" id="username" value=' . $_POST['username'] . '><font color="RED">*</font></td>
                </tr>
                <tr>
                    <td>Password:</td> <td><input type="password" name="password" id="password" value=' . $_POST['password'] . '><font color="RED">*</font></td>
                </tr>
                <tr>
                    <td>Confirm password:</td> <td><input type="password" name="confirmpassword" id="confirmpassword" value=' . $_POST['confirmpassword'] . '><font color="RED">*</font></td>
                </tr>
                <tr>
                    <td>Email:</td> <td><input type="text" name="email" id="email">' . $_POST['email'] . '<font color="RED">*</font></td>
                </tr>
                <tr>
                    <td>Country:</td>
                    <td>
                        <select id="country" name="country" onChange="document.getElementById(\'RegistrationForm\').submit();">
                            ' . $this->getCountries($_POST['country']) . '
                        </select>
                        <font color="RED">*</font>
                    </td>
                </tr>
                <tr>
                    <td>State/Province:</td>
                    <td>
                        <select id="state" name="state" onChange="document.getElementById(\'RegistrationForm\').submit();">
                            ' . $this->getStates($_POST['country'], $_POST['state']) . '
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>City:</td>
                    <td>
                        <select id="city" name="city">
                            ' . $this->getCities($_POST['state'], $_POST['city']) . '
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Firstname:</td> <td><input type="text" name="firstname" id="firstname"><font color="RED">*</font></td>
                </tr>
                <tr>
                    <td>Insertion:</td> <td><input type="text" name="insertion"></td>
                </tr>
                <tr>
                    <td>Lastname:</td> <td><input type="text" name="lastname" id="lastname"><font color="RED">*</font></td>
                </tr>
                <tr>
                    <td>MSN:</td> <td><input type="text" name="msn"></td>
                </tr>
                <tr>
                    <td>Skype:</td> <td><input type="text" name="skype"></td>
                </tr>
                <tr>
                    <td>Job:</td> <td><input type="checkbox" name="job" value="1"> Yes, i have a job<font color="RED">*</font></td>
                </tr>
                <tr>
                    <td>Gender:</td>
                    <td>
                        <select name="gender">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <font color="RED">*</font>
                    </td>
                </tr>
                <tr>
                    <td>Birthdate:</td>
                    <td>
                        <select name="day">
                            ' . $days . '
                        </select>
                        <select name="month">
                            ' . $months . '
                        </select>
                        <select name="year">
                            ' . $years . '
                        </select>
                        <font color="RED">*</font>
                    </td>
                </tr>
                <tr>
                    <td>Primary language:</td>
                    <td>
                        <select name="language">
                            ' . $this->getLanguages() . '
                        </select>
                        <font color="RED">*</font>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="hidden" name="RegistrationForm" value="1">
                        <input type="submit" name="Register" value="Register!">
                    </td>
                </tr>
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
        if ($Language[$string] == "") {
            die("Error: " . $string . "\ncan't be translated...");
        }
        return $Language[$string];
    }

    function GetLanguageFiles() {
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
            setcookie($cookiename, $_COOKIE[$cookiename], time() + $cookietime);      // Make a new cookie with the same name and same info
        }
    }

    function checkFields($_POST) {
        $good = true;
        if ($_POST['username'] == "" ||
                $_POST['password'] == "" ||
                $_POST['email'] == "" ||
                $_POST['firstname'] == "" ||
                $_POST['lastname'] == "" ||
                !preg_match('/^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/', $_POST['email']) ||
                !preg_match('/^(?=.*\d)(?=.*[A-Z]*[a-z])\w{6,}$/', $_POST['password'])) {
            $good = false;
        }
        return $good;
    }

    function DoRegister($_POST) {   // Begin the register function, get the $username and $password from the function call in index.php
        if (!isset($_POST['state'])) {
            $_POST['state'] = "";
        }
        if (!isset($_POST['city'])) {
            $_POST['city'] = "";
        }
        if (!isset($_POST['job'])) {
            $_POST['job'] = 0;
        }
        if ($this->checkFields($_POST)) {
            $birthdate = $_POST['day'] . "-" . $_POST['month'] . "-" . $_POST['year'];
            require $this->MainConfigFile;        // Get the connection variables for mysql from the config file
            if ($this->DB->MakeConnection()) {
                $username = mysql_real_escape_string($_POST['username']);  // Make sure there are no weird tokens in the variables
                $password = mysql_real_escape_string($_POST['password']);
                $sha_pass = sha1($password);       // Encrypt the password with "Sha1"
                $check = mysql_query("SELECT `id` FROM `gebruikers` WHERE `gebruikersnaam` = '" . $username . "' OR `email` = '" . $_POST['email'] . "';"); // Query to check if the username isn't already in use
                if (mysql_num_rows($check) == 0) {      // If 0 results came back from the above query... (if the account name is free for usage)
                    $raw_account_query = "INSERT INTO `gebruikers` VALUES ('','" . $_POST['firstname'] . "','" . $_POST['insertion'] . "','" . $_POST['lastname'] . "','" . $username . "','" . $sha_pass . "','" . $_POST['email'] . "','" . $_POST['language'] . "','" . $_POST['country'] . "','" . $_POST['state'] . "','" . $_POST['city'] . "','" . $_POST['gender'] . "','" . $_POST['msn'] . "','" . $_POST['skype'] . "','" . $birthdate . "','" . $_POST['job'] . "','0');";
                    $account_query = mysql_query($raw_account_query); // Insert the account info
                    $this->Login($username, $password);     // Log in to the account
                } else {
                    echo '<font color="red">' . $this->Translate('AccountwName') . " <b>" . ucfirst($username) . "</b> " . $this->Translate('AlreadyExist') . '</font>';     // if the account already existed, show the part below
                }
            } else {
                die($this->Translate('NoDB'));  // If we had no connection, stop the script with the message "No DB connection"
            }
        } else {
            $this->showRegister($_POST, 1);
        }
    }

    function ShowLogin() {  // Show the login part (left top of index.php when not logged in)
        echo '
            <form action="' . $_SERVER['PHP_SELF'] . '?' . $_SERVER["QUERY_STRING"] . '" name="login" method="POST">
                <li class="me1">
                    <input type="text" name="username">
                    <input type="password" name="password">
                    <input type="submit" name="btnLogin" value="Log in">
                    <input type="submit" name="btnRegister" value="Register">
                </li>
            </form>
        ';
    }

    function Login($username, $password) {  // Check if the variables sent are correct and set the cookie
        require $this->MainConfigFile;
        $this->DB->MakeConnection();
        $username = mysql_real_escape_string($username);
        $password = mysql_real_escape_string($password);
        $sha_pass = sha1($password);
        $query = mysql_query("select * FROM `gebruikers` WHERE `gebruikersnaam` = '" . $username . "';");  // Get the password from the DB that's associated with this account name
        if (mysql_num_rows($query) != 0) {   // If the account exists
            $fields = mysql_fetch_assoc($query);
            $password = $fields['wachtwoord'];  // Get the password of the user with $username
        } else {
            die("Account does not exist in the DB.");  // If the password query returned nothing, the account doesn't exist
        }

        if ($sha_pass == $password) {   // If the Sha1 encrypted version of the posted password equals the entry in the database...
            $cookie = setcookie($cookiename, "$username,$sha_pass", time() + $cookietime);  // Set a cookie with "name,password" that is legit for the following 5 minutes
        } else {
            die("Invalid password entered.");      // If they don't match, the entered pass wasn't correct
        }
        echo '<meta http-equiv="refresh" content="0">';
    }

    function Logout() {
        require $this->MainConfigFile;
        setcookie($cookiename, "1", time() - 3600);  // To delete a cookie, overwrite the cookie with an expiration time of "one hour ago"
        foreach (get_defined_vars () as $key) {  // Reset all variables (clear the session)
            unset($GLOBALS[$key]);
        }
        echo '<meta http-equiv="refresh" content="0">';
    }

    function ShowLogout() {    // Show the logout button
        echo '
            <form name="LogOut" action="' . $_SERVER['PHP_SELF'] . '?' . $_SERVER["QUERY_STRING"] . '" method="POST">
                <input type="submit" name="LogOut" value="Log out">
            </form>
        ';
    }

    function SaveImage($_FILES) {
        if ($this->GetID() != "") {
            if ($_FILES["file"]["name"] != "") {
                $FileType = $_FILES["file"]["type"];
                $FileName = $_FILES["file"]["name"];
                $FileSize = round($_FILES["file"]["size"] / 1024 / 1024, 2);
                if (!is_dir($SaveDir))
                    mkdir($SaveDir);
                if ((($FileType == "image/png") || ($FileType == "image/jpg") || ($FileType == "image/jpeg") || ($FileType == "image/bmp")) && ($FileSize < $MaxFileSize)) {
                    if ($_FILES["file"]["error"] > 0) {
                        echo $this->Translate('ErrorCode') . ": " . $_FILES["file"]["error"] . "<br />";
                    } else {
                        if (file_exists($SaveDir . $FileName)) {
                            $ExistingFile = @fopen($SaveDir . $FileName, 'w');
                            unlink($SaveDir . $FileName);
                        }

                        if ($this->GetID() != "") {
                            $FileNamePieces = explode(".", $FileName);
                            $FileName = $this->GetID() . "." . $FileNamePieces[1];
                            $Moved = @move_uploaded_file($_FILES["file"]["tmp_name"], $SaveDir . $FileName);
                            $Picture = 1;
                            $ImageHandler = new Image();
                            $ImageHandler->CreateDisplayPicture($User);
                        } else {
                            $this->ShowLogin();
                        }
                    }
                } else {
                    if ($FileSize > $MaxFileSize) {
                        echo $this->Translate('FileBig') . $FileSize . " MB" . $this->Translate('FileSize') . " MB";
                    }
                    else
                        echo $this->Translate('FileType') . $FileType;
                }
            }
        }
    }

    function GetImage($Thumb = "") {
        require $this->MainConfigFile;
        if (!is_dir($SaveDir)) {
            mkdir($SaveDir);
        }

        $handle = opendir($SaveDir);
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (preg_match("/$this->GetID()/", $file)) {
                        if ($Thumb != "" && preg_match("/thumb/i", $file)) {
                            $originalImage = $SaveDir . $file;
                        } else if ($Thumb == "" && !preg_match("/thumb/i", $file)) {
                            $originalImage = $SaveDir . $file;
                        }
                    }
                }
            }
        }
        closedir($handle);
        if ($originalImage == "") {
            $this->DeleteImage();
            return false;
        }
        return '<img src="' . $originalImage . '"/>';
    }

    function showCategories($_GET) {
        $result = mysql_query("SELECT * FROM `talen`;");
        if (mysql_num_rows($result) == 0) {
            die("Query error when loading languages");
        } else {
            while ($fields = mysql_fetch_assoc($result)) {
                if ($_GET && isset($_GET['categoryid']) && $fields['id'] == $_GET['categoryid']) {
                    echo '<li class="current">';
                    echo '<a class="current" href="?categoryid=' . $fields['id'] . '">' . $fields['naam'] . '</a>';
                    echo '</li>';
                } else {
                    echo '<li>';
                    echo '<a href="?categoryid=' . $fields['id'] . '">' . $fields['naam'] . '</a>';
                    echo "</li>";
                }
            }
        }
    }

    function showBanner($_GET) {
        echo '
            <div id="hd">
                <link type="text/css" rel="stylesheet" href="./css/ygma1.css">
                <link type="text/css" rel="stylesheet" href="./css/ygma2.css">
                <div id="ygma">
                    <div id="ygmaheader">
                        <div class="bd sp">
                            <div id="ymenu" class="ygmaclr">
                                <div id="mepanel">
                                    <ul id="mepanel-nav">
        ';
        if ($this->IsLoggedIn()) {
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
                ' . $this->showTabs() . '
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

    function showTabs() {
        $tabcode = '
            <div class="tabbed-content">
                <ul class="tabs" id="yan-nav">
        ';
        $tabcode .= '
            <li class="menu" id="yan-nav-home">
                <a href="index.php">Home</a>
            </li>
            <li class="menu" id="yan-nav-browse">
                <a href="index.php?categories=1">Categories</a>
            </li>
        ';
        if ($this->IsLoggedIn()) {
            $tabcode .= '
                <li class="menu" id="yan-nav-about">
                    <a href="index.php?userid=' . $this->User->id . '">Profile</a>
                </li>
           ';
        }
        if (isset($_GET['categories']) || isset($_GET['categoryid'])) {
            $tabcode = str_replace('<li class="menu" id="yan-nav-browse">', '<li class="current menu" id="yan-nav-browse">', $tabcode);
        } else if ($this->IsLoggedIn() && isset($_GET['userid']) && $this->User->id == $_GET['userid']) {
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
        } else {
            die("Error: unknown category parsed");
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
                    <a href="index.php?userid=' . $fields['gebruikerid'] . '" class="avatar">
                        <img class="photo" src="" width="48">    <!-- ToDo : Link invoegen naar user plaatje -->
                    </a>
                    <span class="user">
                        <a class="url" href="index.php?userid=' . $fields['gebruikerid'] . '">
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
            if ($this->IsLoggedIn()) {
                echo '
                    <p class="cta">
                        <a href="?categoryid=' . $categoryid . '&questionid=' . $questionid . '&answer=1">
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
        } else {
            die("Error: unknown question parsed");
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

    function showHomePage() {
        $this->showQuestions();
    }

    function showQuestions($id = "") {
        $link = '<a href="?answer=1">';
        if ($id == "") {
            $result = mysql_query("SELECT * FROM `vragen`;");
        } else {
            $result = mysql_query("SELECT * FROM `vragen` WHERE `taalid` = '" . $id . "';");
            $link = '<a href="?categoryid=' . $id . '&answer=1">';
        }
        if (mysql_num_rows($result) > 0) {
            while ($fields = mysql_fetch_assoc($result)) {
                echo '<li><a href="?categoryid=' . $fields['taalid'] . '&questionid=' . $fields['id'] . '">' . $this->getCategoryName($fields['taalid']) . " - " . $fields['vraag'] . '</a></li>';
            }
        } else {
            die("No questions yet!");
        }

        if ($this->IsLoggedIn()) {
            echo '
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
        $countries = "";
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
        $result = mysql_query("SELECT * FROM `antwoorden` WHERE `taalid` = '" . $categoryid . "' AND `vraagid` = '" . $questionid . "';");
        $answers = "";
        if (mysql_num_rows($result) > 0) {
            require $this->MainConfigFile;
            while ($fields = mysql_fetch_assoc($result)) {
                $user = $this->getUser($fields['gebruikersid']); //aanpassen
                $answers .= '
                    <div class="answer">
                        <div class="profile vcard">
                            <a href="index.php?userid=' . $fields['gebruikersid'] . '" class="avatar">
                                <img class="photo" src="' . $SaveDir . $fields['gebruikersid'] . '.jpg" width="48">
                            </a>
                            <span class="user">
                                <span class="by">by </span>
                                <a class="url" href="index.php?userid=' . $fields['gebruikersid'] . '">
                                    <span class="fn" title="' . $user->username . '">
                                        ' . $user->username . '

                                    </span>
                                </a>
                            </span>
                            <div class="user-badge top-contrib">
                                ' . $this->getBadges($fields['gebruikersid']) . '
                            </div>
                        </div>
                        <div class="qa-container">
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

    function getCategories() {
        $categories = "";
        $result = mysql_query("SELECT * FROM `talen`;");
        if (mysql_num_rows($result) > 0) {
            while ($fields = mysql_fetch_assoc($result)) {
                $categories .= '<option value="' . $fields['id'] . '">' . $fields['naam'] . '</option>';
            }
        }
        return $categories;
    }

    function showAnswerPoster($title = "", $categoryid = "", $questionid = "") {
        if ($this->IsLoggedIn()) {
            echo '
                <div id="yan-main">
                    <div id="yan-question">
                        <div class="qa-container">
                            <center>
                                <form name="Answer" id="Answer" method="POST" action="' . $_SERVER['PHP_SELF'] . '?' . $_SERVER["QUERY_STRING"] . '">
                                    <table>
            ';
            if ($categoryid == "" || $questionid == "") {
                if ($categoryid == "") {
                    echo '
                        <tr>
                            <td>
                                Category :
                                <select name="categoryid">
                                    ' . $this->getCategories() . '
                                </select>
                            </td>
                        </tr>
                    ';
                }
                echo '
                    <tr>
                        <td>
                            Title :
                            <input type="text" name="title" size="50" value="' . $title . '" />
                        </td>
                    </tr>
                ';
            }
            echo '
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
                echo '
                    <input type="hidden" name="categoryid" value="' . $categoryid . '">
                ';
            }
            if ($questionid != "") {
                echo '
                    <input type="hidden" name="questionid" value="' . $questionid . '" />
                ';
            }
            echo '
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
        }
    }

    function submitPost($_POST) {
        if (!function_exists("bb2html")) {
            require "class.bbparser.php";
        }
        $_POST['text'] = bb2html($_POST['text']);

        if (isset($_POST['categoryid'])) {
            if (isset($_POST['questionid']) && $_POST['questionid'] != "") {
                $this->submitAnswer($_POST);
            } else {
                $this->submitQuestion($_POST);
            }
        }
    }

    function submitQuestion($_POST) {
        $query = "INSERT INTO `vragen` (`taalid`, `gebruikerid`, `vraag`, `aanvulling`, `beantwoord`, `posttijd`)
            VALUES ('" . $_POST['categoryid'] . "', '" . $this->getCurrentUser()->id . "', '" . $_POST['title'] . "', '" . $_POST['text'] . "', '0', now());
        ";
        mysql_query($query);
    }

    function submitAnswer($_POST) {
        $query = "INSERT INTO `antwoorden` (`vraagid`, `taalid`, `gebruikersid`, `antwoord`, `votes`, `posttijd`)
            VALUES ('" . $_POST['questionid'] . "', '" . $_POST['categoryid'] . "', '" . $this->getCurrentUser()->id . "', '" . $_POST['text'] . "', 0, now());
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
            if ($_COOKIE && $_COOKIE[$cookiename] != "") {
                $parts = explode(",", $_COOKIE[$cookiename]);
                if (!class_exists('user')) {
                    require "class.user.php";
                }
                $this->User = new user("", $parts[0], $parts[1]);
            } else {
                return false;
            }
        }
    }

    function showUserInfo($id) {
        $user = $this->getUser($id);
        $owned = false;
        if ($this->getCurrentUser() && $this->getCurrentUser()->id == $id) {
            $owned = true;
        }
        echo '
            Profile info:
            <center>
                <table>
        ';
        if ($owned) {
            echo '
                <form name="ProfileEdit" action="index.php" method="POST">
                <tr>
                    <td>Password:</td> <td><input type="password" name="password" /><font color="RED">*</font></td>
                </tr>
                <tr>
                    <td>Confirm password:</td> <td><input type="password" name="confirmpassword" /><font color="RED">*</font></td>
                </tr>
                <tr>
                    <td>Email:</td> <td><input type="text" name="email" value="' . $user->email . '" /></td>
                </tr>
                <tr>
                    <td>Country:</td>
                    <td>
                        <select id="country" name="country" onChange="document.getElementById(\'RegistrationForm\').submit();">
                            ' . $this->getCountries($user->country) . '
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>State/Province:</td>
                    <td>
                        <select id="state" name="state" onChange="document.getElementById(\'RegistrationForm\').submit();">
                            ' . $this->getStates($user->country, $user->state) . '
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>City:</td>
                    <td>
                        <select id="city" name="city">
                            ' . $this->getCities($user->state, $user->city) . '
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>MSN:</td> <td><input type="text" name="msn" value="' . $user->msn . '" /></td>
                </tr>
                <tr>
                    <td>Skype:</td> <td><input type="text" name="skype" value="' . $user->skype . '" /></td>
                </tr>
                <tr>
                    <td>Job:</td> <td><input type="checkbox" name="job" value="' . $user->job . '" /> Yes, i have a job</td>
                </tr>
                <tr>
                    <td><input type="submit" name="submit" value="Save" /></td>
                </tr>
                </form>
            ';
        } else {
            echo '
                <tr>
                    <td>Username:</td> <td>' . ucfirst($user->username) . '</td>
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
        echo '
                </table>
            </center>
        ';
    }

}

?>