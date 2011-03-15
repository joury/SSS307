<?php
require "classes/class.database.php";
require "classes/class.user.php";
$Database = new DB();
$User = new User($Database);
if ($_POST['LogIn'])
{
    $username = $_POST['username'];
    $password = $_POST['password'];
    $User->LogIn($username, $password);
}
if ($User->IsLoggedIn() == false)
{
    $ThisPage = $_SERVER['PHP_SELF'];
    $FileLink = explode("/", $ThisPage);
    $Filename = $FileLink[2];
    $User->ShowLogin($Filename);
}
else
{
    $User->RefreshCookie();
    if ($User->IsAdmin())
    {
        if ($_POST['language'])
        {
            if (is_file("Languages/".$_POST['filename']))
            {
                require "Languages/".$_POST['filename'];
                $ChangedFile = fopen("Languages/".$_POST['filename'], 'w');
                $changes = '<?php';
                $changes .= "\n";
                $changes .= '$Language'." = array(";
                $changes .= "\n";
                foreach ($Language as $key => $val)
                {
                    if ($val != "" && $key != "")
                    {
                        $changes .= "    ";
                        $changes .= '"'.$key.'" => "'.$_POST[$key].'",';
                        $changes .= "\n";
                    }
                }
                $changes .= ");";
                $changes .= "\n";
                $changes .= "?>";
                echo $changes;
                fwrite($ChangedFile, $changes);
            }
        }
        else if ($_GET['file'])
        {
            $FileName = $_GET['file'];
            if (is_file("Languages/".$FileName))
            {
                require "Languages/".$FileName;
                echo "Editing: <b>".$FileName."</b><br><hr>";
                echo "<table>";
                echo "<form id='Language' name='Language' action='admin.php' method='POST'>";
                echo "<input type='hidden' name='filename' value='".$FileName."'>";
                foreach ($Language as $key => $val)
                {
                    if ($key != "" && $val != "")
                        echo "<tr><td>$key</td><td><input type='text' name='$key' value='$val' size='50'></td></tr>";
                }
                echo "<div id='LanguageDiv'>";
                echo "</div>";
                echo "<tr><td><input type='submit' name='language' value='Store'></td></tr></form></table>";
                
            }
        }
        else if ($_GET['FileName'])
        {
            $NewFileName = ucfirst($_GET['FileName']);
            if (!preg_match("/.php/i", $NewFileName))
                $NewFileName .= ".php";
            copy("Languages/English.php", "Languages/".$NewFileName);
            echo '<meta http-equiv="refresh" content="0;url=Admin.php">';
        }
        else
        {
            foreach ($User->GetLanguageFiles() as $file)
            {
                echo '<a href="?file='.$file.'.php">'.$file.'.php</a><br>';
            }
            echo "<br><hr><br>";
            echo '<form name="NewFile" method="GET" action="'.$_SERVER['PHP_SELF'].'">';
            echo 'Enter the filename of the new language file: <input type="text" name="FileName"><br>';
            echo '<input type="submit" value="Create Now">';
            echo '</form>';
        }
    }
    else
        echo $User->Translate("NotAdmin")."<a href='index.php'>".$User->Translate("GoBack")."</a>";
}
?>