function CheckRegister(form) {
    var correct = CheckPass(form, true) && CheckUsername(form.username, true) && CheckEmail(form.email, true, true);
    return correct;
}

function CheckProfileEdit(form) {
    var correct = "";
    if (form.password.value == "" && form.confirmpassword.value == "") {
        if (IsEmpty(form.oldpassword)) {
            alert("You should enter your old password.");
        }
        correct = CheckEmail(form.email, true, false) && !IsEmpty(form.oldpassword);
    } else {
        if (IsEmpty(form.oldpassword)) {
            alert("You should enter your old password.");
        }
        correct = CheckPass(form, true) && CheckEmail(form.email, true, false) && !IsEmpty(form.oldpassword);
    }
    return correct;
}

function CheckAdditional(form, currentyear) {
    var correct = CheckFirstname(form.firstname, true) && CheckLastname(form.lastname, true) && CheckBirthdate(form, currentyear);
    return correct;
}

function AjaxRequest(GET) {
    var url = "checker.php" + GET;
    var xmlHttp = getXMLHttp();
    if (xmlHttp) {
        xmlHttp.open("GET", url, false);
        xmlHttp.send(null);
        return (xmlHttp.responseText == "true");
    } else {
        return false;
    }
}

function CheckUsername(username, submit) {
    if (username.value == "" || AjaxRequest("?" + username.id + "=" + username.value)) {
        if (submit) {
            if (username.value == "") {
                alert("Username field cannot be empty!");
            } else {
                alert("Username is already in use.");
            }
        }
        document.getElementById('usernameImage').src = "images/incorrect.gif";
        return false;
    }
    document.getElementById('usernameImage').src = "images/correct.gif";
    return true;
}

function CheckEmail(email, submit, newone) {
    var filter = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    if (email.value == "" || !filter.test(email.value) || (newone && AjaxRequest("?" + email.id + "=" + email.value))) {
        if (submit) {
            if (email.value == "") {
                alert("Emailaddress field cannot be empty!");
            } else if (!filter.test(email.value)) {
                alert("Please provide a valid email address");                
            } else {
                alert("There\'s already an account registered with that emailaddress.");
            }
        }
        document.getElementById('emailImage').src = "images/incorrect.gif";
        return false;
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
        password = false;
        confirm = false;
    } else {
        var rules = /^(?=.*\d)(?=.*[A-Z]*[a-z]).{6,}$/;
        if (!rules.test(form.password.value) || form.password.value.length > 15) {
            if (submit) {
                alert("Password doesn't match the rules.");
            }
            password = false;
            confirm = false;
        } else if (form.confirmpassword.value == "") {
            if (submit) {
                alert("Confirm password field can't be empty.");
            }
            confirm = false;
        } else if (form.password.value != form.confirmpassword.value) {
            if (submit) {
                alert("Confirm password field doesn't match the password field.");
            }
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