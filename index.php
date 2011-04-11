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
    } else if (isset($_POST['RegistrationForm'])) {
        $website->DoRegister($_POST);
    } else if ($website->getCurrentUser()) {
        $website->RefreshCookie();
    }
    
    ?>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="Imagetoolbar" content="">
        <?php
        $website->showHeader($_GET);
        ?>
        <link rel="shortcut icon" href="./images/answers_favicon.ico">
        <link rel="stylesheet" type="text/css" media="screen" href="./css/answers.css">
        <script type="text/javascript" src="./scripts/navigation.js"></script>
        <script type="text/javascript" src="./scripts/checkfields.js"></script>
        <script type="text/javascript" src="./scripts/vote.js"></script>
        <script type="text/javascript" src="./scripts/bbcode.js"></script>
    </head>
    <body class="c-std wide question-index new-header js">
        <div id="yan">
            <?php
            $website->showBanner($_GET);
            ?>
            <div id="yan-wrap">
                <ol id="yan-breadcrumbs">
                    <li>
                        <a href="index.php" onclick="return loadHome();">Home</a> &gt;
                    </li>
                    <?php
                        if (isset($_GET['categoryid'])) {
                            $website->showCurrentCategory($_GET['categoryid']);

                            if (isset($_GET['questionid'])) {
                                echo $website->getCurrentQuestion($_GET['categoryid'], $_GET['questionid']);
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
                                        $website->showQuestion($_GET['categoryid'], $_GET['questionid']);
                                    } else {
                                        $website->showQuestions($_GET['categoryid']);
                                    }
                                } else if (isset($_GET['userid']) && $_GET['userid'] != "") {
                                    $website->showUserInfo($_GET['userid']);
                                } else if (isset($_GET['categories'])) {
                                    $website->showCategories();
                                    $website->showNewQuestionButton();
                                } else {
                                    $website->showQuestions();
                                }
                            } else {
                                if ($_POST) {
                                    if (isset($_POST['btnProfileEdit'])) {
                                        $website->submitEdit($_POST);
                                    } else if (isset($_POST['btnAdditionalInfo'])) {
                                        $website->submitAdditional($_POST);
                                    } else if (isset($_POST['btnRegister'])) {
                                        $website->showRegister($_POST);
                                    } else if (isset($_POST['Answer'])) {
                                        $website->SubmitPost($_POST);
                                        if (isset($_POST['questionid'])) {
                                            $website->showQuestion($_POST['categoryid'], $_POST['questionid']);
                                        } else {
                                            $website->showQuestions();
                                        }
                                    } else if (isset($_POST['answerid']) && isset($_POST['userid'])) {
                                        $website->submitVote($_POST['answerid'], $_POST['userid'], $_POST['submit']);
                                    }
                                } else {
                                    $website->showQuestions();
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                            if (isset($_GET['categoryid']) && isset($_GET['questionid'])) {
                                $website->showTools();
                                if (isset($_GET['answer'])) {
                                    $website->showAnswerPoster("", $_GET['categoryid'], $_GET['questionid']);
                                }
                                $website->showAnswers($_GET['categoryid'], $_GET['questionid']);
                            } else if (isset($_GET['categoryid']) && isset($_GET['answer'])) {
                                $website->showAnswerPoster("", $_GET['categoryid'], "");
                            } else if (isset($_GET['answer'])) {
                                if (!isset($_GET['question'])) {
                                    $_GET['question'] = "";
                                }
                                $website->showAnswerPoster($_GET['question']);
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