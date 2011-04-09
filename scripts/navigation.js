function loadHome() {
    document.getElementById("yan-nav-home").className="current menu";
    document.getElementById("yan-nav-browse").className="menu";
    if (document.getElementById("yan-nav-about")) {
        document.getElementById("yan-nav-about").className="menu";
    }
    document.getElementById("yan-question").innerHTML = ContentRequest("?homepage=1");
    return false;
}

function loadProfile(id) {
    document.getElementById("yan-nav-home").className="menu";
    document.getElementById("yan-nav-browse").className="menu";
    if (document.getElementById("yan-nav-about")) {
        document.getElementById("yan-nav-about").className="current menu";
    }
    document.getElementById("yan-question").innerHTML = ContentRequest("?profile="+id);
    return false;
}

function loadCategories() {
    document.getElementById("yan-nav-home").className="menu";
    document.getElementById("yan-nav-browse").className="current menu";
    if (document.getElementById("yan-nav-about")) {
        document.getElementById("yan-nav-about").className="menu";
    }
    document.getElementById("yan-question").innerHTML = ContentRequest("?categories=1");
    return false;
}

function ContentRequest(GET) {
    var url = "checker.php" + GET;
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
                return true;
            }
        }
    }
    xmlHttp.open("GET", url, false);
    xmlHttp.send(null);
    return xmlHttp.responseText;
}