<?php

require "classes/class.website.php";
require "classes/class.database.php";
$database = new database();
$website = new website($database);
if (isset($_GET['username']) || isset($_GET['email'])) {
    $exists = "false";
    if (isset($_GET['username'])) {
        $username = stripslashes(mysql_real_escape_string($_GET['username']));
        if ($website->accountExists($username, "")) {
            $exists = "true";
        }
    }
    if (isset($_GET['email'])) {
        $email = stripslashes(mysql_real_escape_string($_GET['email']));
        if ($website->accountExists("", $email)) {
            $exists = "true";
        }
    }
    echo $exists;
} else if (isset($_GET['answerid']) && isset($_GET['userid']) && isset($_GET['vote'])) {
    $website->submitVote($_GET['answerid'], $_GET['userid'], $_GET['vote']);
} else if (isset($_GET['profile'])) {
    echo $website->getUserInfo($_GET['profile']);
} else if (isset($_GET['homepage'])) {
    echo $website->getQuestionsMenu();
} else if (isset($_GET['categories'])) {
    echo $website->getCategoryLinks();
    echo $website->getNewQuestionButton();
} else if (isset($_GET['answer'])) {
    if (isset($_GET['categoryid']) && isset($_GET['questionid'])) {
        echo $website->getAnswerPoster("", $_GET['categoryid'], $_GET['questionid']);
    } else if (isset($_GET['categoryid'])) {
        echo $website->getAnswerPoster("", $_GET['categoryid']);
    } else {
        echo $website->getAnswerPoster();
    }
} else if (isset($_GET['categoryid'])) {
    if (isset($_GET['questionid'])) {
        if (isset($_GET['answers'])) {
            echo $website->getAnswerDiv($_GET['categoryid'], $_GET['questionid']);
        } else {
            echo $website->getQuestion($_GET['categoryid'], $_GET['questionid']);
        }
    } else {
        if (isset($_GET['categoryname'])) {
            echo $website->getCurrentCategory($_GET['categoryid']);
        } else {
            echo $website->getQuestionsMenu($_GET['categoryid']);
        }
    }
} else if (isset($_GET['search'])) {
    $regexp = $_GET['search'];
    echo "<b>Categories that match your query:</b><br><br>";
    echo $website->getCategoryLinks("", $regexp);
    echo "<br><hr><br>";
    echo "<b>Questions that match your query:</b><br><br>";
    echo $website->getQuestionsMenu("", $regexp);
    echo "<br><hr><br>";
    echo "<b>Users that match your query:</b><br><br>";
    echo $website->getUsers($regexp);
} else if (isset($_GET['country'])) {
    if (!isset($_GET['state'])) {
        $countries = "";
        $pieces = explode("</option>", $website->getStates($_GET['country']));
        foreach ($pieces as $piece) {
            $piece = htmlentities(preg_replace('/<option.*.>/', "", $piece));
            $piece .= "<br>";
            if ($piece != "") {
                $countries .= $piece;
            }
        }
        echo $countries;
    } else {
        $countries = "";
        $pieces = explode("</option>", $website->getCities($_GET['country'], $_GET['state']));
        foreach ($pieces as $piece) {
            $piece = htmlentities(preg_replace('/<option.*.>/', "", $piece));
            $piece .= "<br>";
            if ($piece != "") {
                $countries .= $piece;
            }
        }
        echo $countries;
    }
}
?>
