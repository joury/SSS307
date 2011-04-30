function clear() {
    if (document.getElementById("yan-answers")) {
        document.getElementById("yan-answers").innerHTML = "";
    }
    if (document.getElementById("answerposter")) {
        document.getElementById("answerposter").innerHTML = "";
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
    $("#yan-question").load("checker.php?homepage=1");
    return false;
}

function loadProfile(id) {
    document.getElementById("yan-nav-home").className="menu";
    document.getElementById("yan-nav-browse").className="menu";
    if (document.getElementById("yan-nav-about")) {
        document.getElementById("yan-nav-about").className="current menu";
    }
    clear();
    $("#yan-question").load("checker.php?profile=" + id);
    return false;
}

function loadCategories() {
    document.getElementById("yan-nav-home").className="menu";
    document.getElementById("yan-nav-browse").className="current menu";
    if (document.getElementById("yan-nav-about")) {
        document.getElementById("yan-nav-about").className="menu";
    }
    clear();
    $("#yan-question").load("checker.php?categories=1");
    return false;
}

function loadAnswerPoster(categoryid, questionid) {
    var answerposter = document.getElementById("answerposter");
    var empty = answerposter.innerHTML.replace(/^\s*|\s*$/g, '') == "";
    if (empty) {
        if (categoryid == null || questionid == null) {
            if (categoryid == null && questionid == null) {
                $.get("checker.php?answer=1", function(data) {
                    $('#yan-content').append(data);
                });
            } else if (categoryid != null) {
                $.get("checker.php?categoryid="+categoryid+"&answer=1", function(data) {
                    $('#yan-content').append(data);
                });
            }
        } else {
            $("#answerposter").load("checker.php?categoryid="+categoryid+"&questionid="+questionid+"&answer=1");
        }
    }
    return false;
}

function loadQuestions(categoryid) {
    clear();
    document.getElementById("category_"+categoryid).className="current";
    $("#yan-question").load("checker.php?categoryid="+categoryid);
    if (document.getElementById("categoryindex")) {
        $("#categoryindex").load("checker.php?categoryid="+categoryid+"&categoryname=1");
    } else {
        $.get("checker.php?categoryid="+categoryid+"&categoryname=1", function(data) {
            $('#categoryindex').append(data);
        });
    }
    return false;
}

function loadQuestion(categoryid, questionid) {
    var answerdiv = document.getElementById("answerdiv");
    var empty = answerdiv.innerHTML.replace(/^\s*|\s*$/g, '') == "";
    if (empty) {
        clear();
        document.getElementById("category_"+categoryid).className="current";
        $("#yan-question").load("checker.php?categoryid="+categoryid+"&questionid="+questionid);
        if (document.getElementById("categoryindex")) {
            $("categoryindex").load("checker.php?categoryid="+categoryid+"&categoryname=1");
            $("#yan-breadcrumbs").append($("#subject"));
        } else {
            $.get("checker.php?categoryid="+categoryid+"&categoryname=1", function(data) {
                $('#yan-breadcrumbs').append(data);
                $("#yan-breadcrumbs").append(document.getElementById("subject").innerHTML);
            });
        }
        return loadAnswers(categoryid, questionid);
    }
    return false;
}

function loadAnswers(categoryid, questionid) {
    if (document.getElementById("yan-anwers")) {
        document.getElementById("yan-content").removeChild(document.getElementById("yan-answers"));
    }
    $.get("checker.php?categoryid="+categoryid+"&questionid="+questionid+"&answers=1", function(data) {
        $('#yan-content').append(data);
    });
    return false;
}

function handleSearch(form) {
    if (form.query.value.length >= 3) {
        clear();
        $("yan-question").load("checker.php?search=" + form.query.value);
    } else {
        clear();
        $("yan-question").load("checker.php?homepage=1");
    }
    return false;
}

function vote(form, vote) {
    var negativeField = document.getElementById('negative_' + form.answerid.value);
    var positiveField = document.getElementById('positive_' + form.answerid.value);
    var oldNegative = parseInt(negativeField.innerHTML);
    var oldPositive = parseInt(positiveField.innerHTML);
    if (vote < 0) {
        negativeField.innerHTML = oldNegative + 1;
    } else {
        positiveField.innerHTML = oldPositive + 1;
    }
    /*
    var url = "checker.php?answerid=" + form.answerid.value + "&userid=" + form.userid.value + "&vote=" + vote;
    var xmlHttp = getXMLHttp();
    xmlHttp.open("GET", url, false);
    xmlHttp.send(null);*/
    $.get("checker.php?answerid=" + form.answerid.value + "&userid=" + form.userid.value + "&vote=" + vote);
    document.getElementById('votebuttons_' + form.answerid.value).style.visibility = 'hidden';
    loadAnswers(form.categoryid.value, form.questionid.value);
    return false;
}