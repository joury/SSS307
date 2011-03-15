function bbcode_ins(fieldId, tag)
{
    field=document.getElementById(fieldId);
    if(tag=='b' || tag=='i' || tag=='u' || tag == 'php' || tag == 'code')
    {
        if (document.selection)
        {
            field.focus();
            var selected = document.selection.createRange().text;
            var ins = '[' + tag + ']' + selected + '[/' + tag +']';
            var selected2 = document.selection.createRange();
            var sel = document.selection.createRange();
            selected2.moveStart ('character', -field.value.length);
            sel.text = '[' + tag + ']' + selected + '[/' + tag+']';
            sel.moveStart('character', selected2.text.length + ins.length - selected.length);
        }
        //MOZILLA/NETSCAPE/SAFARI support
        else if (field.selectionStart || field.selectionStart == 0)
        {
            var startPos = field.selectionStart;
            var endPos = field.selectionEnd;
            var selected = field.value.substring(startPos, endPos);
            var ins = '[' + tag + ']' + selected + '[/' + tag +']';
            field.focus();
            field.value = field.value.substring(0, startPos) + ins + field.value.substring(endPos, field.value.length);
            field.setSelectionRange(endPos+ins.length, endPos+ins.length-selected.length);
        }
    }
    else if(tag == 'img')
    {
        var path = prompt('Enter image path', 'http://');
        if(!path)
        {
            return;
        }
        if (document.selection)
        {
            field.focus();
            var selected = document.selection.createRange().text;
            var ins = '[' + tag + ']' + path + '[/' + tag+']';
            var selected2 = document.selection.createRange();
            var sel = document.selection.createRange();
            sel.text = '[' + tag + ']' + path + '[/' + tag+']';
            selected2.moveStart ('character', -field.value.length);
            sel.moveStart('character', selected2.text.length + ins.length - selected.length);
        }
        //MOZILLA/NETSCAPE/SAFARI support
        else if (field.selectionStart || field.selectionStart == 0)
        {
            var startPos = field.selectionStart;
            var endPos = field.selectionEnd;
            var ins = '[' + tag + ']' + path + '[/' + tag+']';
            field.focus();
            field.value = field.value.substring(0, startPos)
            + ins
            + field.value.substring(endPos, field.value.length);
            field.setSelectionRange(endPos+ins.length, endPos+ins.length-selected.length);
        }
    }
    else if(tag == 'url')
    {
        var url = prompt('Enter link URL', 'http://');
        var linkText = prompt('Enter link text', '');
        if(!url || !linkText)
        {
            return;
        }
        if (document.selection)
        {
            field.focus();
            var selected = document.selection.createRange().text;
            var ins = '[' + tag + '='+url+']' + linkText + '[/' + tag+']';
            var selected2 = document.selection.createRange();
            var sel = document.selection.createRange();
            sel.text = '[' + tag + '='+url+']' + linkText + '[/' + tag+']';
            selected2.moveStart ('character', -field.value.length);
            sel.moveStart('character', selected2.text.length + ins.length - selected.length);
        }
        //MOZILLA/NETSCAPE/SAFARI support
        else if (field.selectionStart || field.selectionStart == 0)
        {
            var startPos = field.selectionStart;
            var endPos = field.selectionEnd;
            var ins = '[' + tag + '='+url+']' + linkText + '[/' + tag+']';
            field.focus();
            field.value = field.value.substring(0, startPos)
            + ins
            + field.value.substring(endPos, field.value.length);
            field.setSelectionRange(endPos+ins.length, endPos+ins.length-selected.length);
        }
    }
    else //For smilies
    {
        if (document.selection)
        {
            field.focus();
            var selected = document.selection.createRange().text;
            var ins = tag;
            var selected2 = document.selection.createRange();
            var sel = document.selection.createRange();
            sel.text = tag;
            selected2.moveStart ('character', -field.value.length);
            sel.moveStart('character', selected2.text.length + ins.length - selected.length);
        }
        //MOZILLA/NETSCAPE/SAFARI support
        else if (field.selectionStart || field.selectionStart == 0)
        {
            var startPos = field.selectionStart;
            var endPos = field.selectionEnd;
            field.focus();
            field.value = field.value.substring(0, startPos) + tag + field.value.substring(endPos, field.value.length);
            field.setSelectionRange(endPos+tag.length, endPos+tag.length);
        }
    }
}