<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html dir="ltr" lang="en-us">
    <?php
    require "classes/class.website.php";
    require "classes/class.database.php";
    $database = new database();
    $website = new website($database);
    if (isset($_POST['btnLogin'])) {
        $website->LogIn($_POST['username'], $_POST['password']);
    } else if (isset($_POST['LogOut'])) {
        $website->Logout();
    } else if ($website->IsLoggedIn()) {
        $website->RefreshCookie();
    }
    ?>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="Imagetoolbar" content="">
        <?php
        $website->showHeader($_GET);
        ?>
        <link rel="shortcut icon" href="./images/answers_favicon.ico">
        <link rel="stylesheet" type="text/css" media="screen" href="./css/answers.css">
        <?php
        if ($_POST) {
            echo '<script type="text/javascript" src="./scripts/checkfields.js"></script>';
        }
        if ($_GET) {
            if (isset($_GET['answer'])) {
                echo '<script type="text/javascript" src="./scripts/bbcode.js"></script>';
            } else if (isset($_GET['userid'])) {
                if ($website->getCurrentUser() && $website->getCurrentUser()->id == $website->getUser($_GET['userid'])) {
                    echo '
                        <script type="text/javascript">
                            function CheckPass() {
                                var form = document.ProfileEdit;
                                if (form.password.value != "") {
                                    var rules = /^(?=.*\d)(?=.*[A-Z]*[a-z])\w{6,}$/;
                                    if (form.password.value != form.confirmpassword.value) {
                                        alert("Confirm password field doesn\'t match the password field.");
                                        form.confirmpassword.focus();
                                        return false;
                                    } else if (!rules.test(form.password.value)) {
                                        alert("Password doesn\'t match the rules.");
                                        form.password.focus();
                                        return false;
                                    }
                                } else {
                                    alert("Can\'t save without filling in the password fields");
                                    form.password.focus();
                                    return false;
                                }
                            }
                        </script>
                    ';
                }
            }
        }
        ?>
    </head>
    <body class="c-std wide question-index new-header js">
    <iframe style="position: absolute; visibility: visible; width: 2em; height: 2em; top: -31px; left: 0pt; border-width: 0pt;" title="Text Resize Monitor" id="_yuiResizeMonitor"></iframe>
    <div id="yan">
        <?php
        $website->showBanner($_GET);
        ?>
        <div id="yan-wrap">
            <ol id="yan-breadcrumbs">
                <li>
                    <a href="index.php">Home</a> &gt;
                </li>
                <?php
                if ($_GET && !$_POST) {
                    if (isset($_GET['categoryid'])) {
                        $website->showCurrentCategory($_GET['categoryid']);

                        if (isset($_GET['questionid'])) {
                            echo $website->getCurrentQuestion($_GET['categoryid'], $_GET['questionid']);
                        }
                    }
                }
                ?>
            </ol>
            <div id="yan-content">
                <div id="yan-main">
                    <div id="yan-question">
                        <?php
                        if ($_GET && !$_POST) {
                            if (isset($_GET['categoryid'])) {
                                if (isset($_GET['questionid'])) {
                                    $website->showCurrentQuestion($_GET['categoryid'], $_GET['questionid']);
                                } else {
                                    $website->showQuestions($_GET['categoryid']);
                                }
                            } else if (isset($_GET['userid'])) {
                                $website->showUserInfo($_GET['userid']);
                            } else {
                                $website->showHomePage();
                            }
                        } else {
                            if ($_POST) {
                                if (isset($_POST['btnRegister'])) {
                                    $website->showRegister($_POST);
                                } else if (isset($_POST['RegistrationForm'])) {
                                    $website->DoRegister($_POST);
                                } else if (isset($_POST['Answer'])) {
                                    $website->SubmitAnswer($_POST);
                                    $website->showCurrentQuestion($_POST['categoryid'], $_POST['questionid']);
                                }
                            } else {
                                $website->showHomePage();
                            }
                        }
                        ?>
                    </div>
                </div>
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
                <?php
                        if ($_REQUEST && isset($_REQUEST['categoryid']) && isset($_REQUEST['questionid'])) {
                            if (isset($_GET['answer'])) {
                                $website->showAnswerWriter($_GET['categoryid'], $_GET['questionid']);
                            }
                            $website->showAnswers($_REQUEST['categoryid'], $_REQUEST['questionid']);
                        }
                ?>
                    </div>
                    <div id="yan-related">
                        <div id="yan-categories" class="mod">
                            <h2 class="hd">Categories</h2>
                            <ul class="bd">
                                <li class="expanded">
                                    <ul>
                                <?php
                                $website->showCategories($_GET);
                                ?>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>