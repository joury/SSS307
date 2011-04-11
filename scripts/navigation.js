function clear() {
    if (document.getElementById("yan-answers")) {
        document.getElementById("yan-answers").innerHTML = "";
    }
    if (document.getElementById("answerposter")) {
        document.getElementById("yan-content").removeChild(document.getElementById("answerposter"));
    }
    if (document.getElementById("yan-answers")) {
        document.getElementById("yan-content").removeChild(document.getElementById("yan-answers"));
    }
    document.getElementById("yan-breadcrumbs").innerHTML = '<li><a href="index.php" onclick="return loadHome();">Home</a> &gt;</li>';
    var nodes = document.getElementsByTagName("a");
    for (var i = 0; i < nodes.length; i++) {
        if (nodes[i].className == "current") {
            nodes[i].className = "";
        }
    }
}

function loadHome() {
    document.getElementById("yan-nav-home").className="current menu";
    document.getElementById("yan-nav-browse").className="menu";
    if (document.getElementById("yan-nav-about")) {
        document.getElementById("yan-nav-about").className="menu";
    }
    clear();
    var content = ContentRequest("?homepage=1");
    if (content) {
        document.getElementById("yan-question").innerHTML = content;
        return false;
    } else {
        return true;
    }
}

function loadProfile(id) {
    document.getElementById("yan-nav-home").className="menu";
    document.getElementById("yan-nav-browse").className="menu";
    if (document.getElementById("yan-nav-about")) {
        document.getElementById("yan-nav-about").className="current menu";
    }
    clear();
    var content = ContentRequest("?profile="+id);
    if (content) {
        document.getElementById("yan-question").innerHTML = content;
        return false;
    } else {
        return true;
    }
}

function loadCategories() {
    document.getElementById("yan-nav-home").className="menu";
    document.getElementById("yan-nav-browse").className="current menu";
    if (document.getElementById("yan-nav-about")) {
        document.getElementById("yan-nav-about").className="menu";
    }
    clear();
    var content = ContentRequest("?categories=1");
    if (content) {
        document.getElementById("yan-question").innerHTML = content;
        return false;
    } else {
        return true;
    }
}

function loadAnswerPoster(categoryid, questionid) {
    var content = "";
    if (!document.getElementById("answerposter")) {
        if (categoryid == null || questionid == null) {
            if (categoryid == null && questionid == null) {
                content = ContentRequest("?answer=1");
                if (content) {
                    document.getElementById("yan-content").innerHTML += content;
                    return false;
                }
            } else if (categoryid != null) {
                content = ContentRequest("?categoryid="+categoryid+"&answer=1");
                if (content) {
                    document.getElementById("yan-content").innerHTML += content;
                    return false;
                }
            }
        } else {
            content = ContentRequest("?categoryid="+categoryid+"&questionid="+questionid+"&answer=1");
            if (content) {
                document.getElementById("yan-content").insertbefore(content, document.getElementById("yan-question-tools"));
                return false;
            }
        }
    } else {
        return false;
    }
    return true;
}

function loadQuestions(categoryid) {
    var content = "";
    content = ContentRequest("?categoryid="+categoryid);
    if (content) {
        clear();
        document.getElementById("category_"+categoryid).className="current";
        document.getElementById("yan-question").innerHTML = content;
        content = ContentRequest("?categoryid="+categoryid+"&categoryname=1");
        if (content) {
            if (document.getElementById("categoryindex")) {
                document.getElementById("categoryindex").innerHTML = content;
            } else {
                document.getElementById("yan-breadcrumbs").innerHTML += content;
            }
            return false;
        }
    }
    return true;
}

function loadQuestion(categoryid, questionid) {
    var content = "";
    if (!document.getElementById("answerdiv")) {
        content = ContentRequest("?categoryid="+categoryid+"&questionid="+questionid);
        if (content) {
            clear();
            document.getElementById("category_"+categoryid).className="current";
            document.getElementById("yan-question").innerHTML = content;
            content = ContentRequest("?categoryid="+categoryid+"&categoryname=1");
            if (content) {
                if (document.getElementById("categoryindex")) {
                    document.getElementById("categoryindex").innerHTML = content;
                } else {
                    document.getElementById("yan-breadcrumbs").innerHTML += content;
                }
                document.getElementById("yan-breadcrumbs").innerHTML += document.getElementById("subject").innerHTML;
                return loadAnswers(categoryid, questionid);
            }
        }
    } else {
        return false;
    }
    return true;
}

function loadAnswers(categoryid, questionid) {
    var content = "";
    if (!document.getElementById("yan-anwers")) {
        content = ContentRequest("?categoryid="+categoryid+"&questionid="+questionid+"&answers=1");
        if (content) {
            document.getElementById("yan-content").innerHTML += content;
            return false;
        }
    } else {
        return false;
    }
    return true;
}

function getXMLHttp() {
    var xmlHttp;
    try {
        xmlHttp = new XMLHttpRequest();
    } catch (e) {
        try {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                return false;
            }
        }
    }
    return xmlHttp;
}

function ContentRequest(GET) {
    var url = "checker.php" + GET;
    var xmlHttp = getXMLHttp();
    if (xmlHttp) {
        xmlHttp.open("GET", url, false);
        xmlHttp.send(null);
        return xmlHttp.responseText;
    } else {
        return false;
    }
}