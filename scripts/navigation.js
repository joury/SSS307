function loadHome() {
    document.getElementById("yan-nav-home").className="current menu";
    document.getElementById("yan-nav-browse").className="menu";
    if (document.getElementById("yan-nav-about")) {
        document.getElementById("yan-nav-about").className="menu";
    }
    if (document.getElementById("yan-answers")) {
        document.getElementById("yan-answers").innerHTML = "";
    }
    if (document.getElementById("answerposter")) {
        document.getElementById("yan_content").removeChild(document.getElementById("answerposter"));
    }
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
    if (document.getElementById("yan-answers")) {
        document.getElementById("yan-answers").innerHTML = "";
    }
    if (document.getElementById("answerposter")) {
        document.getElementById("yan_content").removeChild(document.getElementById("answerposter"));
    }
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
    if (document.getElementById("yan-answers")) {
        document.getElementById("yan-answers").innerHTML = "";
    }
    if (document.getElementById("answerposter")) {
        document.getElementById("yan_content").removeChild(document.getElementById("answerposter"));
    }
    var content = ContentRequest("?categories=1");
    if (content) {
        document.getElementById("yan-question").innerHTML = content;
        return false;
    } else {
        return true;
    }
}

function loadAnswerPoster(categoryid, questionid) {
    if (!document.getElementById("answerposter")) {
        if (categoryid == null || questionid == null) {
            if (categoryid == null && questionid == null) {
                var content = ContentRequest("?answer=1");
                if (content) {
                    document.getElementById("yan-content").innerHTML += content;
                    return false;
                }
            }
        } else {
            var content = ContentRequest("?categoryid="+categoryid+"&questionid="+questionid);
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