var succes = "";

function AjaxRequest(field) {
    var url = "checker.php?" + field.id + "=" + field.value;
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
                alert("Your browser doesn't support AJAX!");
                return false;
            }
        }
    }
    xmlHttp.open("GET", url, false);
    xmlHttp.send(null);
    return (xmlHttp.responseText == "true");
}

function CheckUsername(username, submit) {
    if (username.value == "") {
        if (submit) {
            alert("Username field cannot be empty!");
        }
        document.getElementById('usernameImage').src = "images/incorrect.gif";
        username.focus();
        return false;
    } else if (AjaxRequest(username)) {
        if (submit) {
            alert("Username is already in use.");
        }
        document.getElementById('usernameImage').src = "images/incorrect.gif";
        username.focus();
        return false;
    }
    document.getElementById('usernameImage').src = "images/correct.gif";
    return true;
}

function CheckEmail(email, submit) {
    if (email.value == "") {
        if (submit) {
            alert("Emailaddress field cannot be empty!");
        }
        document.getElementById('emailImage').src = "images/incorrect.gif";
        email.focus();
        return false;
    } else if (email.value != "") {
        var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
        if (!filter.test(email.value)) {
            if (submit) {
                alert("Please provide a valid email address");
            }
            document.getElementById('emailImage').src = "images/incorrect.gif";
            email.focus();
            return false;
        } else if (AjaxRequest(email)) {
            if (submit) {
                alert("There\'s already an account registered with that emailaddress.");
            }
            document.getElementById('emailImage').src = "images/incorrect.gif";
            email.focus();
            return false;
        }
    }
    document.getElementById('emailImage').src = "images/correct.gif";
    return true;
}

function CheckFirstname(firstname, submit) {
    if (firstname.value == "") {
        if (submit) {
            alert("Firstname field cannot be empty!");
        }
        document.getElementById('firstnameImage').src = "images/incorrect.gif";
        firstname.focus();
        return false;
    }
    document.getElementById('firstnameImage').src = "images/correct.gif";
    return true;
}

function CheckLastname(lastname, submit) {
    if (lastname.value == "") {
        if (submit) {
            alert("Lastname field cannot be empty!");
        }
        document.getElementById('lastnameImage').src = "images/incorrect.gif";
        lastname.focus();
        return false;
    }
    document.getElementById('lastnameImage').src = "images/correct.gif";
    return true;
}

function CheckFields(form, submit) {
    var correct = true;
    if (CheckPass(form, submit) == false) {
        correct = false;
    }
    if (CheckUsername(form.username, submit) == false) {
        correct = false;
    }
    if (CheckEmail(form.email, submit) == false) {
        correct = false;
    }
    if (CheckFirstname(form.firstname, submit) == false) {
        correct = false;
    }
    if (CheckLastname(form.lastname, submit) == false) {
        correct = false;
    }
    alert(correct);
    return correct;
}

function CheckPass(form, submit) {
    if (form.password.value == "") {
        if (submit) {
            alert("Password field cannot be empty!");
        }
        form.password.focus();
        return false;
    } else {
        var rules = /^(?=.*\d)(?=.*[A-Z]*[a-z])\w{6,}$/;
        if (form.password.value != form.confirmpassword.value) {
            if (submit) {
                alert("Confirm password field doesn't match the password field.");
            }
            form.confirmpassword.focus();
            return false;
        } else if (!rules.test(form.password.value)) {
            if (submit) {
                alert("Password doesn't match the rules.");
            }
            form.password.focus();
            return false;
        }
    }
    return true;
}