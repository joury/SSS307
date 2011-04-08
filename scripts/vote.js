function Vote(form, vote) {
    var negativeField = document.getElementById('negative_' + form.answerid.value);
    var positiveField = document.getElementById('positive_' + form.answerid.value);
    var oldNegative = parseInt(negativeField.innerHTML);
    var oldPositive = parseInt(positiveField.innerHTML);
    if (vote < 0) {
        negativeField.innerHTML = oldNegative + 1;
    } else {
        positiveField.innerHTML = oldPositive + 1;
    }
    var url = "checker.php?answerid=" + form.answerid.value + "&userid=" + form.userid.value + "&vote=" + vote;
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
    document.getElementById('votebuttons_' + form.answerid.value).style.visibility = 'hidden';
    return false;
}