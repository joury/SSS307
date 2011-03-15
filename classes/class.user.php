<?php
Class User
{
    var $Username;
    var $Password;
    var $DB;
    var $EmailAddress;
    var $Country;
    var $State;
    var $City;
    var $Gender;
    var $Birthdate;
    var $ID;
    var $MainConfigFile = "configs/config.php";
    var $LanguageDir = "languages/";
    function __construct($db)
    {
        $this->DB = $db;
    }
    function IsLoggedIn()
    {
        $username = $this->GetName();
        $password = $this->GetPass();
        $id = $this->GetID();
        if ($username != "" && $password != "" && $id != "")
            return true;
        else
            return false;
    }
    function ShowRegister()
    {
        for ($i = 1; $i <= 31; $i++)
        {
            if ($i < 10)
            $i = "0".$i;
            $days .= "<option value='$i'>$i</option>";
        }
        for ($i = 1; $i <= 12; $i++)
        {
            if ($i < 10)
                $i = "0".$i;
            $months .= "<option value='$i'>$i</option>";
        }
        for ($i = (date(Y)-150); $i <= date(Y)-4; $i++)
            $years .= "<option value='$i'>$i</option>";
        echo '
            <table>
                <form name="Register" onSubmit="return CheckFields();" action="index.php" method="POST">
                <tr>
                    <td>Username:</td> <td><input type="text" name="username" id="username"></td>
                </tr>
                <tr>
                    <td>Password:</td> <td><input type="password" name="password" id="password"></td> <td>Rules are: Atleast 6 chars, atleast 1 digit, only numbers characters and underscore are allowed.</td>
                </tr>
                <tr>
                    <td>Email:</td> <td><input type="text" name="email" id="emailaddress"></td>
                </tr>
                <tr>
                    <td>Country:</td> <td><select id="countrySelect" name="country" onchange="populateState()"></select><script type="text/javascript">populateCountry();</script></td>
                </tr>
                <tr>
                    <td>State/Province:</td> <td><select id="stateSelect" name="selectedstate" onchange="populateCity()"></select></td>
                </tr>
                <tr>
                    <td>City:</td> <td><select id="citySelect" name="city"></select></td>
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
                        <select name="day">
                            '.$days.'
                        </select>
                        <select name="month">
                            '.$months.'
                        </select>
                        <select name="year">
                            '.$years.'
                        </select>
                    </td> <td>dd-mm-yyyy</td>
                </tr>
                <tr>
                    <td><input type="submit" name="Register" value="Register!"></td>
                </tr>
                </form>
            </table>
        ';
    }
    function Translate($string)
    {
        $language = $this->GetLanguage();
        $languagefile = $LanguageDir.$language.".php";
        if (is_file($languagefile))
            require $languagefile;
        else
            require $LanguageDir."English.php";
        if ($Language[$string] == "")
            die("Error: ".$string."\ncan't be translated...");
        return $Language[$string];
    }
    function GetLanguageFiles()
    {
        if ($handle = opendir($LanguageDir))
        {
            $LanguageFiles = array();
            while (false !== ($file = readdir($handle)))
            {
                if ($file != "." && $file != "..")
                {
                    $file = explode(".", $file);
                    $LanguageFiles[] = $file[0];
                }
            }
        }
        closedir($handle);
        return $LanguageFiles;
    }
    function RefreshCookie()
    {
        require $this->MainConfigFile;
        if ($_COOKIE[$cookiename] != "")
            setcookie($cookiename, $_COOKIE[$cookiename], time()+$cookietime);      // Make a new cookie with the same name and same info
    }
    function DoRegister($Info)			// Begin the register function, get the $username and $password from the function call in index.php
    {
        $username = $Info['username'];
        $password = $Info['password'];
        $email = $Info['email'];
        $country = $Info['country'];
        $state = $Info['selectedstate'];
        $city = $Info['city'];
        $gender = $Info['gender'];
        $birthdate = $Info['day']."-".$Info['month']."-".$Info['year'];
        require $this->MainConfigFile;								// Get the connection variables for mysql from the config file
        if ($this->DB->MakeConnection() == true)
        {
            $username = mysql_real_escape_string($username);		// Make sure there are no weird tokens in the variables
            $password = mysql_real_escape_string($password);
            $sha_pass = sha1($password);							// Encrypt the password with "Sha1"
            $check = mysql_query("select `id` FROM `accounts` WHERE `username` = '".$username."' OR `email` = '".$email."';");	// Query to check if the username isn't already in use
            if (mysql_num_rows($check) == 0)						// If 0 results came back from the above query... (if the account name is free for usage)
            {
                $raw_account_query = "INSERT INTO `accounts` values ('','".$username."','".$sha_pass."','".$email."','".$country."','".$state."','".$city."','".$gender."','".$birthdate."','0');";
                $account_query = mysql_query($raw_account_query);	// Insert the account info
                $checking = mysql_query("select `id` FROM `accounts` WHERE `username` = '".$username."';");		// Query to check if the query succeeded
                if (mysql_num_rows($checking) != 0)					// If we got a hit (account exists)...
                {
                    $this->LogIn($username, $password);					// Log in to the account
                    return true;									// Tell the function call in index.php that it succeeded
                }
                else
                    return false;									// Tell the function call in index.php that it failed
            }
            else
                echo $this->Translate('AccountwName')." ".$username." ".$this->Translate('AlreadyExist');	    // if the account already existed, show the part below
        }
        else
            die($this->Translate('NoDB'));		// If we had no connection, stop the script with the message "No DB connection"
    }
    function GetName($id = "")
    {
        require $this->MainConfigFile;
        if ($this->Username != "" && $id == "")
            return $this->Username;
        else if ($_COOKIE[$cookiename] != "" && $id == "")
        {
            $parts = explode(",", $_COOKIE[$cookiename]);
            if ($parts[0] != "")
                $username = $parts[0];
            if ($username != "")
            {
                $this->Username = $username;
                return $username;
            }
        }
        else if ($id != "")
        {
            $this->DB->MakeConnection(); 
            $query = mysql_query("select `name` FROM `accounts` WHERE `id` = '".$id."';");
            if (mysql_num_rows($query) != 0)
            {
                $result = mysql_result($query, 0);
                if ($result != "")
                    return $result;
            }
        }
    }
    function GetPass()
    {
        require $this->MainConfigFile;
        if ($this->Password != "")
            return $this->Password;
        else if ($_COOKIE[$cookiename] != "")
        {
            require $this->MainConfigFile;
            $parts = explode(",", $_COOKIE[$cookiename]);
            if ($parts[1] != "")
                $password = $parts[1];
            if ($password != "")
            {
                $this->Password = $password;
                return $password;
            }
        }
    }
    function GetID($name = "")
    {
        if ($this->ID != "" && $name == "")
            return $this->ID;
        else
        {
            require $this->MainConfigFile;
            $this->DB->MakeConnection(); 
            if ($name != "")
                $query = mysql_query("SELECT `id` FROM `accounts` WHERE `username` = '".$name."';");
            else
                $query = mysql_query("SELECT `id` FROM `accounts` WHERE `username` = '".$this->GetName()."';");
            if (mysql_num_rows($query) != 0)
            {
                $result = mysql_result($query, 0);
                if ($result != "")
                {
                    $this->ID = $result;
                    return $result;
                }
                else
                    die("Unknown error");
            }
        }
    }
    function GetLanguage()
    {
        $raw_language_query = "SELECT `primary_language` FROM `additional` WHERE `id` = ".$this->GetID().";";
        $language_query = mysql_query($raw_language_query);
        if (@mysql_num_rows($language_query) != 0)
        {
            $language = mysql_result($language_query, 0);
        }
        if ($language == "")
            $language = "English";
        return $language;
    }
    function ShowLogin($destination = "")		// Show the login part (left top of index.php when not logged in)
    {
        if ($destination == "")
            $destination = "index.php";
        echo '
            <form name="LogIn" action="'.$destination.'" method="POST">
            Username: <input type="text" name="username">
            Password: <input type="password" name="password">
            <input type="submit" name="LogIn" value="Log in">
            </form>
        ';
    }
    function LogIn($username, $password)		// Check if the variables sent are correct and set the cookie
    {
        require $this->MainConfigFile;
        $this->DB->MakeConnection();
        $username = mysql_real_escape_string($username);
        $password = mysql_real_escape_string($password);
        $sha_pass = sha1($password);
        $query = mysql_query("select * FROM `accounts` WHERE `username` = '".$username."';");		// Get the password from the DB that's associated with this account name
        if (mysql_num_rows($query) != 0)			// If the account exists
        {
            $fields = mysql_fetch_assoc($query);
            $password = $fields['password'];		// Get the password of the user with $username
        }
        else
            die("Account does not exist in the DB.");		// If the password query returned nothing, the account doesn't exist

        if ($sha_pass == $password)			// If the Sha1 encrypted version of the posted password equals the entry in the database...
        {
            $cookie = setcookie($cookiename, "$username,$sha_pass", time()+$cookietime);		// Set a cookie with "name,password" that is legit for the following 5 minutes
            $this->ID = $fields['id'];
            $this->Username = $username;
            $this->Password = $sha_pass;
            $this->EmailAddress = $fields['email'];
            $this->Country = $fields['country'];
            $this->State = $fields['state'];
            $this->City = $fields['city'];
            $this->Gender = $fields['gender'];
            $this->Birthdate = $fields['birthdate'];
        }
        else
            die("Invalid password entered.");		    // If they don't match, the entered pass wasn't correct
    }
    function Logout()
    {
        require $this->MainConfigFile;
        setcookie($cookiename, "1", time()-3600);		// To delete a cookie, overwrite the cookie with an expiration time of "one hour ago"
        foreach(get_defined_vars() as $key)		// Reset all variables (clear the session)
            unset($GLOBALS[$key]);
        echo '<meta http-equiv="refresh" content="0">';
    }
    function ShowLogout()				// Show the logout button
    {
        echo '
            <form name="LogOut" action="index.php" method="POST">
            <input type="submit" name="LogOut" value="Log out">
            </form>
        ';
    }
    function ShowChangePass()
    {
        echo '
            <form name="ChangePass" action="index.php" method="POST">
            Current password: <input type="password" name="old_password">
            New password: <input type="password" name="new_password">
            Confirm password: <input type="password" name="new_password2">
            <input type="submit" name="ChangePass" value="Change password">
            </form>
        ';
    }
    function ChangePass($_POST)
    {
        $new_password = $_POST['new_password'];
        $old_password = $_POST['old_password'];
        require $this->MainConfigFile;
        $this->DB->MakeConnection();
        $old_password = sha1($old_password);
        if ($old_password == $this->GetPass())			// If the Sha1 encrypted version of the posted password equals the entry in the database...
        {
            $new_password = sha1($new_password);
            $change_pass = mysql_query("UPDATE `accounts` SET `password` = '".$new_password."' WHERE `username` = '".$this->GetName()."';");
            if ($change_pass)
                echo $this->Translate('PasswordChanged');
            else
                echo mysql_error($connection);
        }
        else
            die("Invalid password entered.");		    // If they don't match, the entered pass wasn't correct
    }
    function ShowAdditionalForm()
    {
        $fields = $this->GetAdditional();
        $sexuality = $fields['sexuality'];
        $relationship = $fields['relationship'];
        $language = $fields['primary_language'];
        echo '
            <table>
                <form name="Additional" action="index.php" method="POST" enctype="multipart/form-data">
                <tr>
                    <td>Display Picture:</td> <td><input type="file" name="file" id="file" accept="image/jpg,image/jpeg" size = "50"></td>
                </tr>
                <tr>
                    <td>:</td>
                    <td>
                        <select name="">
                            <option value="">1</option>
                            <option value="">2</option>
                        </select>
                    </td>
                </tr>
                </form>
            </table>
        ';
    }
    function SaveAdditional($_FILES, $_POST)
    {
        if ($this->GetID() != "")
        {
            if ($_FILES["file"]["name"] != "")
            {
                $FileType = $_FILES["file"]["type"];
                $FileName = $_FILES["file"]["name"];
                $FileSize = round($_FILES["file"]["size"]/1024/1024, 2);
                if (!is_dir($SaveDir))
                    mkdir($SaveDir);
                if ((($FileType == "image/png") || ($FileType == "image/jpg") || ($FileType == "image/jpeg") || ($FileType == "image/bmp")) && ($FileSize < $MaxFileSize))
                {
                    if ($_FILES["file"]["error"] > 0)
                    {
                        echo $this->Translate('ErrorCode').": " . $_FILES["file"]["error"] . "<br />";
                    }
                    else
                    {
                        if (file_exists($SaveDir.$FileName))
                        {
                            $ExistingFile = @fopen($SaveDir.$FileName, 'w');
                            unlink($SaveDir.$FileName);
                        }
                        if ($this->GetID() != "")
                        {
                            $FileNamePieces = explode(".", $FileName);
                            $FileName = $this->GetID().".".$FileNamePieces[1];
                            $Moved = @move_uploaded_file($_FILES["file"]["tmp_name"], $SaveDir.$FileName);
                            $Picture = 1;
                            $ImageHandler = new Image();
                            $ImageHandler->CreateDisplayPicture($User);
                        }
                        else
                            $this->ShowLogin();
                    }
                }
                else
                {
                    if ($FileSize > $MaxFileSize)
                    {
                        echo $this->Translate('FileBig').$FileSize." MB".$this->Translate('FileSize')." MB";
                    }
                    else
                        echo $this->Translate('FileType').$FileType;
                }
            }
            else
                $Picture = 0;
            $this->DB->MakeConnection();
            if ($this->ShowAdditional == false)
            {
                foreach($_POST as $key => $val)
                {
                    if ($val != "" && $key != "key" && $key != "val" && $key != "Additional")
                    {
                        $changes .= strtolower($key)." = '".$val."', ";
                    }
                }
                $changes = substr($changes, 0, -2);
                $raw_change_additional = "UPDATE `additional` SET $changes WHERE `id` = '".$this->GetID()."';";
            }
            else
            {
                foreach($_POST as $key => $val)
                {
                    if ($val != "" && $key != "key" && $key != "val" && $key != "Additional")
                    {
                        $changes .= "'".$val."', ";
                    }
                }
                $changes = substr($changes, 0, -2);
                $raw_change_additional = "INSERT INTO `additional` values (".$this->GetID().", $Picture, $changes);";
            }
            $change_additional = mysql_query($raw_change_additional);
        }
    }
    function GetAdditional($name = "")
    {
        if ($name != "")
            $raw_additional_query = "SELECT * FROM `additional` WHERE `id` = (SELECT `id` FROM `accounts` WHERE `username` = '".$name."');";
        else
            $raw_additional_query = "SELECT * FROM `additional` WHERE `id` = (SELECT `id` FROM `accounts` WHERE `username` = '".$this->GetName()."');";
        $additional_query = mysql_query($raw_additional_query);
        if (mysql_num_rows($additional_query) != 0)
        {
            $fields = mysql_fetch_assoc($additional_query);
        }
        return $fields;
    }
    function ShowAdditional($name = "")
    {
        if ($this->GetAdditional($name) != "")
        {
            $fields = $this->GetAdditional($name);
            if ($this->GetImage() != "")
                $this->GetImage();
            $additional .= "<table border='0.2'>";
            foreach ($fields as $key => $val)
            {
                if ($val != "" && $key != "id" && $key != "picture")
                {
                    $key = preg_replace("/_/", " ", $key);
                    $additional .= "<tr><td>".ucfirst($key)."</td> <td>".$val."</td></tr>";
                }
            }
            $additional .= "</table>";
        }
        else
            $additional = false;
        return $additional;
    }
    function DeleteImage()
    {
        require $this->MainConfigFile;
        $this->DB->MakeConnection();
        $raw_remove_picture = "UPDATE `additional` SET `picture` = '0' WHERE `username` = '$this->GetID()';";
        $remove_picture = mysql_query($raw_change_additional);
    }
    function GetImage($Thumb = "")
    {
        require $this->MainConfigFile;
        if (!is_dir($SaveDir))
            mkdir($SaveDir);
        if ($handle = opendir($SaveDir))
        {
            while (false !== ($file = readdir($handle)))
            {
                if ($file != "." && $file != "..")
                {
                    if (preg_match("/$this->GetID()/", $file))
                    {
                        if ($Thumb != "" && preg_match("/thumb/i", $file))
                            $originalImage = $SaveDir.$file;
                        else if ($Thumb == "" && !preg_match("/thumb/i", $file))
                            $originalImage = $SaveDir.$file;
                    }
                }
            }
        }
        closedir($handle);
        if ($originalImage == "")
        {
            $this->DeleteImage();
            return false;
        }
        return '<img src="'.$originalImage.'"/>';
    }
    function IsAdmin()
    {
        if ($this->GetName() == "")
            return false;
        $raw_is_admin = "SELECT `rank` FROM `accounts` WHERE `username` = '".$this->GetName()."';";
        $is_admin = mysql_query($raw_is_admin);
        $rank = mysql_result($is_admin, 0);
        if ($rank == 0)
            return false;
        else
            return true;
    }
}
?>