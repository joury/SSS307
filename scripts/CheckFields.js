function CheckFields()
{
    if (document.Register.username.value == "") {
        alert("How about entering a username?");
        document.Register.username.focus();
        return false;
    } else if (document.Register.password.value == "") {
        alert("How about entering a password?");
        document.Register.password.focus();
        return false;
    } else if (document.Register.password.value != "") {
        var rules = /^(?=.*\d)(?=.*[A-Z]*[a-z])\w{6,}$/;
        if (!rules.test(document.Register.password.value)) {
            alert("Password doesn't match the rules.");
            document.Register.password.focus();
            return false;
        } else {
            return true;
        }
    } else if (document.Register.emailaddress.value == "") {
        alert("How about entering an emailaddress?");
        document.Register.emailaddress.focus();
        return false;
    } else if (document.Register.emailaddress.value != "") {
        var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
        if (!filter.test(document.Register.emailaddress.value)) {
            alert("Please provide a valid email address");
            document.Register.emailaddress.focus();
            return false;
        } else {
            return true;
        }
    } else{
        return true;
    }
}