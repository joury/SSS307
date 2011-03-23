function CheckFields()
{
    var form = document.Register;
    if (form.username.value == "") {
        alert("Username field cannot be empty!");
        form.username.focus();
        return false;
    }
    if (form.password.value == "") {
        alert("Password field cannot be empty!");
        form.password.focus();
        return false;
    } else {
        var rules = /^(?=.*\d)(?=.*[A-Z]*[a-z])\w{6,}$/;
        if (!rules.test(form.password.value)) {
            alert("Password doesn't match the rules.");
            form.password.focus();
            return false;
        } else if (form.password.value != form.confirmpassword.value) {
            alert("Confirm password field doesn't match the password field.");
            form.confirmpassword.focus();
            return false;
        }
    }
    if (form.email.value == "") {
        alert("Emailaddress field cannot be empty!");
        form.email.focus();
        return false;
    } else if (form.email.value != "") {
        var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
        if (!filter.test(form.email.value)) {
            alert("Please provide a valid email address");
            form.email.focus();
            return false;
        }
    }
    if (form.firstname.value == "") {
        alert("Firstname field cannot be empty!");
        form.firstname.focus();
        return false;
    }
    if (form.lastname.value == "") {
        alert("Lastname field cannot be empty!");
        form.lastname.focus();
        return false;
    }
    return true;
}