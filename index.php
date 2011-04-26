<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <?php
    require "classes/class.website.php";
    require "classes/class.database.php";
    $database = new database();
    $website = new website($database);
    if (isset($_POST['btnLogin'])) {
        $website->login($_POST['username'], $_POST['password']);
    } else if (isset($_POST['LogOut'])) {
        $website->logout();
    } else if (isset($_POST['RegistrationForm'])) {
        $website->doRegister($_POST);
    } else if ($website->getCurrentUser()) {
        $website->refreshCookie();
    }
    
    ?>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="Imagetoolbar" content="">
        <?php
        $website->showHeader($_GET);
        ?>
        <link rel="shortcut icon" href="./images/answers_favicon.ico">
        <script type="text/javascript">
            if (screen.width < 600 || screen.height < 600) {
                document.write('<link rel="stylesheet" type="text/css" media="screen" href="./css/mobiel.answers.css">');
                document.write('<meta name="viewport" content="width=680, initial-scale=0.7, user-scalable=yes">');
            } else {
                document.write('<link rel="stylesheet" type="text/css" media="screen" href="./css/answers.css">');
            }
            
            var supportsOrientationChange = "onorientationchange" in window,
                orientationEvent = supportsOrientationChange ? "orientationchange" : "resize";

            window.addEventListener(orientationEvent, function() {
                var newContent = "";
                var xScale = screen.width / 680;
                var yScale = screen.height / 680;
                switch(window.orientation) {
                    case 0: {   // Landscape
                        newContent = "width=680, initial-scale="+xScale+", user-scalable=yes";
                        break;
                    }
                    case -90: { // Portrait
                        newContent = "width=680, initial-scale="+yScale+", user-scalable=yes";
                        break;
                    }
                    case 90: {  // Portrait
                        newContent = "width=680, initial-scale="+yScale+", user-scalable=yes";
                        break;
                    }
                }
                var metatags = document.getElementsByTagName("meta");
                for (var i = 0; i < metatags.length; i++) {
                    if (metatags[i].getAttribute("name") == "viewport") {
                        metatags[i].setAttribute("content", newContent);
                    }
                }
            }, false);

        </script>
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
                        echo $website->getCurrentCategory($_GET['categoryid']);
                        if (isset($_GET['questionid'])) {
                            echo $website->getCurrentQuestion($_GET['categoryid'], $_GET['questionid']);
                        }
                    } else if (isset($_GET['userid'])) {
                        echo '<a href="?userid=' . $_GET['userid'] . '">' . $website->getUser($_GET['userid'])->username . '</a> > ';
                        if (isset($_GET['edit'])) {
                            echo '<a href="?userid=' . $_GET['userid'] . '&edit=1">Edit</a> >';
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
                                        echo $website->getQuestion($_GET['categoryid'], $_GET['questionid']);
                                    } else {
                                        echo $website->getQuestionsMenu($_GET['categoryid']);
                                    }
                                } else if (isset($_GET['userid']) && $_GET['userid'] != "") {
                                    echo $website->getUserInfo($_GET['userid']);
                                } else if (isset($_GET['categories'])) {
                                    echo $website->getCategoryLinks();
                                    echo $website->getNewQuestionButton();
                                } else {
                                    echo $website->getQuestionsMenu();
                                }
                            } else {
                                if ($_POST) {
                                    if (isset($_POST['btnProfileEdit'])) {
                                        $website->submitProfileEdit($_POST);
                                    } else if (isset($_POST['btnAdditionalInfo'])) {
                                        $website->submitAdditional($_POST);
                                    } else if (isset($_POST['btnRegister'])) {
                                        $website->showRegister($_POST);
                                    } else if (isset($_POST['Answer'])) {
                                        $website->SubmitPost($_POST);
                                        if (isset($_POST['questionid'])) {
                                            echo $website->getQuestion($_POST['categoryid'], $_POST['questionid']);
                                        } else {
                                            echo $website->getQuestionsMenu();
                                        }
                                    } else if (isset($_POST['answerid']) && isset($_POST['userid'])) {
                                        $website->submitVote($_POST['answerid'], $_POST['userid'], $_POST['submit']);
                                    }
                                } else {
                                    echo $website->getQuestionsMenu();
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div id="bottom_content">
                        <div id="toolbar">
                            <?php
                            if (isset($_GET['categoryid']) && isset($_GET['questionid'])) {
                                echo $website->getTools();
                            }
                            ?>
                        </div>
                        <div id="answerposter">
                            <?php
                            if (isset($_GET['answer'])) {
                                if (isset($_GET['categoryid'])) {
                                    if (isset($_GET['questionid'])) {
                                        echo $website->getAnswerPoster("", $_GET['categoryid'], $_GET['questionid']);
                                    } else {
                                        echo $website->getAnswerPoster("", $_GET['categoryid'], "");
                                    }
                                } else {
                                    if (!isset($_GET['question'])) {
                                        $_GET['question'] = "";
                                    }
                                    echo $website->getAnswerPoster($_GET['question']);
                                }
                            }
                            ?>
                        </div>
                        <div id="answerdiv">
                            <?php
                            if (isset($_GET['categoryid']) && isset($_GET['questionid'])) {
                                if (isset($_GET['remove'])) {
                                    $website->removeAnswer($_GET['remove']);
                                }
                                echo $website->getAnswerDiv($_GET['categoryid'], $_GET['questionid']);
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div id="yan-related">
                    <div id="yan-categories" class="mod">
                        <h2 class="hd">Categories</h2>
                        <ul class="bd">
                            <li class="expanded">
                                <ul>
                                    <?php
                                    echo $website->getCategoryLinks($_GET);
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