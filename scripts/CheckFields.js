function CheckRegister(form) {
    var correct = CheckPass(form, true) && CheckUsername(form.username, true) && CheckEmail(form.email, true, true);
    return correct;
}

function CheckProfileEdit(form) {
    var correct = CheckPass(form, true) && CheckEmail(form.email, true, false) && !IsEmpty(form.oldpassword);
    return correct;
}

function CheckAdditional(form, currentyear) {
    var correct = CheckFirstname(form.firstname, true) && CheckLastname(form.lastname, true) && CheckBirthdate(form, currentyear);
    return correct;
}

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
                alert("Your browser doesn't support AJAX, this means the image next to email and username might not be correct.");
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

function CheckEmail(email, submit, newone) {
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
        } else if (newone && AjaxRequest(email)) {
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

function CheckPass(form, submit) {
    var password = true;
    var confirm = true;
    if (form.password.value == "") {
        if (submit) {
            alert("Password field cannot be empty!");
        }
        form.password.focus();
        password = false;
        confirm = false;
    } else {
        var rules = /^(?=.*\d)(?=.*[A-Z]*[a-z]).{6,}$/;
        if (!rules.test(form.password.value) || form.password.value.length > 15) {
            if (submit) {
                alert("Password doesn't match the rules.");
            }
            form.password.focus();
            password = false;
            confirm = false;
        } else if (form.confirmpassword.value == "") {
            if (submit) {
                alert("Confirm password field can't be empty.");
            }
            form.confirmpassword.focus();
            confirm = false;
        } else if (form.password.value != form.confirmpassword.value) {
            if (submit) {
                alert("Confirm password field doesn't match the password field.");
            }
            form.confirmpassword.focus();
            confirm = false;
        }
        
    }
    if (password) {
        document.getElementById('passwordImage').src = "images/correct.gif";
    } else {
        document.getElementById('passwordImage').src = "images/incorrect.gif";
    }
    if (confirm) {
        document.getElementById('confirmpasswordImage').src = "images/correct.gif";
    } else {
        document.getElementById('confirmpasswordImage').src = "images/incorrect.gif";
    }
    var ok = password && confirm;
    return ok;
}

function CheckBirthdate(form, currentyear) {
    var correct = true;
    if (form.day.value == "" || form.day.value < 1 || form.day.value > 31) {
        if (form.day.value != "") {
            alert('You can\'t be born on the ' + form.day.value + 'th day.');
        }
        correct = false;
    }
    if (form.month.value == "" || form.month.value < 1 || form.month.value > 12) {
        if (form.month.value != "") {
            alert('You can\'t be born on the ' + form.month.value + 'th month.');
        }
        correct = false;
    }
    if (form.year.value == "" || form.year.value < currentyear-100 || form.year.value > currentyear-8) {
        if (form.year.value != "") {
            alert('You can\'t be born in ' + form.year.value + '.');
        }
        correct = false;
    }
    if (correct) {
        document.getElementById('birthdateImage').src = "images/correct.gif";
    } else {
        document.getElementById('birthdateImage').src = "images/incorrect.gif";
    }
    return correct;
}

function IsEmpty(field) {
    if (field.value == "") {
        alert(field.id + " is empty!");
        field.focus();
        return true;
    }
    return false;
}