<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html dir="ltr" lang="en-us">
    <?php
    require "classes/class.website.php";
    require "classes/class.database.php";
    $database = new database();
    $website = new website($database);
    ?>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="Imagetoolbar" content="">
        <meta name="description" content="If you leave for example: laptop chargers, PSP cha�"> <!-- ToDo : Vraag -->
        <meta name="keywords" content="answers,  questions, Programming"> <!-- ToDo : Tags -> sitenaam, answers, questions, code, categorienaam -->
        <meta name="title" content=""> <!-- ToDo : Vraag -->
        <title>Does leaving a charger cable in a plug outlet waste electricity? - Yahoo! Answers</title> <!-- ToDo : Zelfde tekst als hierboven-->
        <link rel="shortcut icon" href="http://l.yimg.com/a/i/us/sch/gr/answers_favicon.ico">
        <link rel="stylesheet" type="text/css" media="screen" href="./css/answers-fe-us.css">
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
                if ($_GET) {
                    if (isset($_GET['categoryid'])) {
                        $website->showCurrentCategory($_GET['categoryid']);
                    }

                    if (isset($_GET['questionid'])) {
                        $website->showCurrentQuestion($_GET['questionid']);
                    }
                }
                ?>
            </ol>
            <div id="yan-content">
                <div id="yan-main">
                    <div id="yan-question">
                        <?php
                        if ($_GET) {
                            if (isset($_GET['categoryid']) && !isset($_GET['questionid'])) {
                                $website->showQuestions($_GET['categoryid']);
                            } else if (isset($_GET['questionid'])) {
                                $website->showCurrentQuestion($_GET['questionid']);
                            } else {
                                $website->showHomePage();
                            }
                        } else {
                            if ($_POST) {
                                if (isset($_POST['Login'])) {

                                } else if (isset($_POST['Register'])) {
                                    $website->showRegister($_POST);
                                }
                            } else {
                                $website->showHomePage();
                            }
                        }
                        /*
                          <div id="profile" class="profile vcard">  <!-- ToDo : Dynamische profiel informatie vanuit DB -->
                          <a href="" class="avatar">  <!-- ToDO : Link invoegen naar profiel -->
                          <img class="photo" alt="" src="" width="48">    <!-- ToDo : Link invoegen naar user plaatje -->
                          </a>
                          <span class="user">
                          <a class="url" href="">  <!-- ToDO : Link invoegen naar profiel -->
                          <span class="fn" title=""></span> <!-- ToDo : Username hier -->
                          </a>
                          </span>
                          </div>
                          <div class="qa-container"> <!-- ToDo : Dynamische vraag via database -->
                          <div class="hd">
                          <h2>Open Question</h2>
                          </div>
                          <h1 class="subject"></h1>   <!-- ToDo : Invullen -->
                          <div class="content">   <!-- ToDo : Invullen -->
                          </div>
                          <ul class="meta">
                          <li>
                          <abbr title=""></abbr> <!-- ToDo : Invullen -->
                          </li>
                          </ul>
                          <p class="cta">
                          <a href=""> <!-- ToDo : Link invullen -->
                          <span><span><span><span>Answer Question</span></span></span></span>
                          </a>
                          </p>
                          </div> */
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
                <div id="yan-answers" class="mod">
                    <div class="hd">
                        <h3>
                            <strong>Answers</strong> (4) <!-- Nummertje moet dynamisch -->
                        </h3>
                        <form action="file:///C:/question/index" method="get" id="yan-answer-sort">
                            <div>
                                <input name="qid" value="20110301153400AA8GaqD" type="hidden">
                                <label for="yan-answer-sort-box">Show:</label>
                                <select name="show" id="yan-answer-sort-box">
                                    <option selected="selected" value="7">All Answers</option>
                                    <option value="1">Oldest to Newest</option>
                                    <option value="2">Newest to Oldest</option>
                                    <option value="3">Rated Highest to Lowest</option>
                                </select>
                                <input name="go" value="Go" class="button" type="submit">
                            </div>
                        </form>
                    </div>
                    <div class="bd">
                        <ul class="shown"> <!-- Alles hieronder moet dynamisch... de mogelijke antwoorden -->
                            <li>  <div id="GqUpLTPiI1ZxF8Y21lau" class="answer">
                                    <div id="profile-nQ7G1nBVaa" class="profile vcard">
                                        <a href="" class="avatar">
                                            <img class="photo" alt="classicsat" src="" width="48">
                                        </a>
                                        <span class="user">
                                            <span class="by">by </span>
                                            <a class="url" href=""><span class="fn" title="classicsat">classics...</span></a>
                                        </span>
                                        <div class="user-badge top-contrib">
                                            <img src="./images/topcontrib.gif" alt="A Top Contributor is someone who is knowledgeable in a particular category.">
                                        </div>
                                    </div>
                                    <div class="qa-container">
                                        <div class="content">Yes they do, but individually not much. Gobally, it adds up significantly.</div>
                                        <ul class="meta">
                                            <li>
                                                <abbr title="2011-03-01 23:54:01 +0000">12 hours ago</abbr>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="yan-related">
                <div id="yan-categories" class="mod">
                    <h2 class="hd">Categories</h2>
                    <ul class="bd">
                        <li>
                            <a id="view-all-cats" href="">All Programming Languages</a>
                        <li class="expanded">
                            <ul>
                                <?php
                                $website->showCategories($_GET);
                                ?>
                            </ul>
                        </li>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>