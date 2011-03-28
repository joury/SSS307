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
    } else if ($website->getCurrentUser()) {
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
        if ($_POST || (isset($_GET['userid']) && $website->getCurrentUser() && $website->getCurrentUser()->id == $_GET['userid'])) {
            echo '<script type="text/javascript" src="./scripts/checkfields.js"></script>';
        }
        if ($_GET) {
            if (isset($_GET['answer'])) {
                echo '<script type="text/javascript" src="./scripts/bbcode.js"></script>';
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
                                if (isset($_POST['ProfileEdit'])) {
                                    $website->submitEdit($_POST);
                                }
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
                                    $website->SubmitPost($_POST);
                                    if (isset($_POST['questionid'])) {
                                        $website->showCurrentQuestion($_POST['categoryid'], $_POST['questionid']);
                                    } else {
                                        $website->showHomePage();
                                    }
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
                        if (isset($_GET['categoryid']) && isset($_GET['questionid'])) {
                            if (isset($_GET['answer'])) {
                                $website->showAnswerPoster("", $_GET['categoryid'], $_GET['questionid']);
                            }
                            $website->showAnswers($_REQUEST['categoryid'], $_REQUEST['questionid']);
                        } else if (isset($_GET['answer'])) {
                            if (isset($_GET['question'])) {
                                $website->showAnswerPoster($_GET['question']);
                            } else {
                                $website->showAnswerPoster();
                            }
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