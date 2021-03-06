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
    return CheckBirthdate(form, currentyear, true);
}

function CheckUsername(username, submit) {
    if (username.value == "") {
        if (submit) {
            alert("Username field cannot be empty!");
        }
        document.getElementById('usernameImage').src = "images/incorrect.gif";
        return false;
    } else {
        $.get("checker.php?" + username.id + "=" + username.value, function(data) {
            if (data == "true") {
                if (submit) {
                    alert("Username is already in use!");
                }
                document.getElementById('usernameImage').src = "images/incorrect.gif";
                return false;
            }
        });
    }
    document.getElementById('usernameImage').src = "images/correct.gif";
    return true;
}

function CheckEmail(email, submit, newone) {
    var filter = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    if (email.value == "" || !filter.test(email.value)) {
        if (submit) {
            if (email.value == "") {
                alert("Emailaddress field cannot be empty!");
            } else {
                alert("Please provide a valid email address");                
            }
        }
        document.getElementById('emailImage').src = "images/incorrect.gif";
        return false;
    } else if (newone) {
        $.get("checker.php?" + email.id + "=" + email.value, function(data) {
            if (data == "true") {
                if (submit) {
                    alert("There\'s already an account registered with that emailaddress.");
                }
                document.getElementById('emailImage').src = "images/incorrect.gif";
                return false;
            }
        });
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

function CheckBirthdate(form, currentyear, submit) {
    var correct = true;
    if (form.day.value == "" || form.day.value < 1 || form.day.value > 31) {
        if (form.day.value != "") {
            correct = false;
            if (submit) {
                alert('You can\'t be born on the ' + form.day.value + 'th day.');
            }
        }
    }
    if (form.month.value == "" || form.month.value < 1 || form.month.value > 12) {
        if (form.month.value != "") {
            correct = false;
            if (submit) {
                alert('You can\'t be born on the ' + form.month.value + 'th month.');
            }
        }
    }
    if (form.year.value == "" || form.year.value < currentyear-100 || form.year.value > currentyear-8) {
        if (submit && form.year.value != "") {
            correct = false;
            if (submit) {
                alert('You can\'t be born in ' + form.year.value + '.');
            }
        }
    }
    if (correct) {
        document.getElementById('birthdateImage').src = "images/correct.gif";
    } else {
        document.getElementById('birthdateImage').src = "images/incorrect.gif";
    }
    return correct;
}

function handleCountryChange(form) {
    for (var j = 0; j < form.state.length; j++) {
        form.state.remove(0);
    }
    for (var k = 0; k < form.city.length; j++) {
        form.city.remove(0);
    }
    var content = ContentRequest("?country=" + form.country.options[form.country.selectedIndex].value);
    if (content) {
        var options = content.split("<br>");
        for (var i = 0; i < options.length; i++) {
            if (options[i] == "") {
                continue;
            }
            var option = document.createElement("option");
            option.text = options[i];
            form.state.options.add(option);
        }
    }
}

function handleStateChange(form) {
    for (var j = 0; j < form.city.length; j++) {
        form.city.remove(0);
    }
    var content = ContentRequest("?country=" + form.country.options[form.country.selectedIndex].value + "&state=" + form.state.options[form.state.selectedIndex].value);
    if (content) {
        var options = content.split("<br>");
        for (var i = 0; i < options.length; i++) {
            if (options[i] == "") {
                continue;
            }
            var option = document.createElement("option");
            option.text = options[i];
            form.city.options.add(option);
        }
    }
}