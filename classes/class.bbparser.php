<?php

$prevent_xss = true;

$cb_ref_title = 'go to the reference';

if (!isset($corzblog['mail_addy']))
    $corzblog['mail_addy'] = 'me@myaddress.com';

// php syntax highlighting
// for the cool colored code tags [ccc][/ccc]..
ini_set('highlight.string', '#E53600');
ini_set('highlight.comment', '#FFAD1D');
ini_set('highlight.keyword', '#47A35E');
ini_set('highlight.bg', '#FFFFFF');
ini_set('highlight.default', '#3F6DAE');
ini_set('highlight.html', '#0D3D0D');

$GLOBALS['cbparser']['warning_message'] = '';

$GLOBALS['cbparser']['warnings']['balance_fixed'] = '
		<div class="centered" id="message">
			<span class="red">note</span>: some tags were automatically closed for you.<br />
			(check your bbcode)
		</div>';

$GLOBALS['cbparser']['warnings']['imbalanced'] = '
		<div class="centered" id="message">
			<span class="red">note</span>: your tags are not <a 
			title="in other words; you have opened a tag, but not closed it.">balanced!</a><br />
			(check your bbcode)<br />
			<br />
		</div>
		<div id="bbinfo">
			<strong>notes..</strong><br />
			to produce a square bracket, double it! -&gt; <code><strong>[[ ! ]]</strong></code><br />
			to insert shell code or ascii art, use <code><strong>[pre][/pre]</strong></code> or <code><strong>[tt][/tt]</strong></code> tags.<br />
			for php web code, use [ccc] tags..<pre>
[ccc]&lt;?php
echo "foo!";
?&gt;[/ccc]</pre>
			for more information, check out <a href="http://corz.org/bbtags" 
			onclick="window.open(this.href); return false;" title="ALL the tags!">the <big>instructions</big></a>!
		</div>';

$GLOBALS['cbparser']['warnings']['empty'] = '
		<div class="centered" id="message">
			there was no text!
		</div>';

$check_tag_balance = true;

/*
  bbcocode to xhtml

  converts bbcode to xhtml 1.0 strict.

  usage:

  string ( string to transform [, string title])

 */

function bb2html() {
    global $cb_ref_title, $check_tag_balance, $insert_link, $prevent_spam, $prevent_xss;

    $bb2html = func_get_arg(0);
    if (func_num_args() == 2) {
        $title = func_get_arg(1);
        $id_title = make_valid_id($title); // fix up bad id's
    } else {
        $id_title = $title = '';
    }

    // init.. [useful global array]
    $GLOBALS['cbparser']['state'] = 0;
    $GLOBALS['cbparser']['close_tags'] = '';
    $GLOBALS['cbparser']['text'] = slash_it($bb2html);
    if (!empty($GLOBALS['do_debug'])) {
        debug("\n\n" . 'cbparser incoming [$bb2html]: ' . $bb2html . "\n\n");
    }// :debug:
    // oops!
    if ($bb2html == '') {
        $GLOBALS['cbparser']['state'] = 1;
        $GLOBALS['cbparser']['warning_message'] .= $GLOBALS['cbparser']['warnings']['empty'];
        return false;
    }

    // grab any *real* square brackets first, store 'em..
    $bb2html = str_replace('[[[[', '**$@$**[[', $bb2html); // catch demo tags next to demo tags
    $bb2html = str_replace(']]]]', ']]**@^@**', $bb2html); // ditto
    $bb2html = str_replace('[[[', '**$@$**[', $bb2html); // catch tags next to demo tags
    $bb2html = str_replace(']]]', ']**@^@**', $bb2html); // ditto
    $bb2html = str_replace('[[', '**$@$**', $bb2html); // finally!
    $bb2html = str_replace(']]', '**@^@**', $bb2html);


    // ensure bbcode is lowercase..
    $bb2html = bbcode_to_lower($bb2html);

    /*
      pre-formatted text

      even bbcode inside [pre] text will remain untouched, as it should be.
      there may be multiple [pre] or [ccc] blocks, so we grab them all and create arrays..
     */

    $pre = array();
    $i = 9999;
    while ($pre_str = stristr($bb2html, '[pre]')) {
        if (!empty($GLOBALS['do_debug']))
            debug("\n" . '$pre_str: ' . "$pre_str\n\n"); // :debug:
 $pre_str = substr($pre_str, 0, strpos($pre_str, '[/pre]') + 6);
        $bb2html = str_replace($pre_str, "***pre_string***$i", $bb2html);
        $pre[$i] = encode(str_replace(array('**$@$**', '**@^@**'), array('[[', ']]'), $pre_str));
        if (!empty($GLOBALS['do_debug']))
            debug("\n" . '$pre[$i]: ' . "$pre[$i]\n\n"); // :debug:
 $i++; //	^^	we encode this, for html tags, etc.
    }

    /*
      syntax highlighting (Cool Colored Code™)
      och, why not!
     */
    $ccc = array();
    $i = 0;
    while ($ccc_str = stristr($bb2html, '[ccc]')) {
        $ccc_str = substr($ccc_str, 0, strpos($ccc_str, '[/ccc]') + 6);
        $bb2html = str_replace($ccc_str, "***ccc_string***$i", $bb2html);
        $ccc[$i] = str_replace(array('**$@$**', '**@^@**', "\r\n"), array('[[', ']]', "\n"), $ccc_str);
        $i++;
    }

    // rudimentary tag balance checking..
    if ($check_tag_balance) {
        $bb2html = check_balance($bb2html);
    }
    if ($GLOBALS['cbparser']['state'] == 1) {
        return false;
    } // imbalanced tags
    // xss attack prevention [99.9% safe!]..
    if ($prevent_xss) {
        $bb2html = xssclean($bb2html);
    }

    // generic entity encode
    $bb2html = htmlentities($bb2html, ENT_NOQUOTES, 'utf-8');
    $bb2html = str_replace('[sp]', '&nbsp;', $bb2html);


    // process links?
    $GLOBALS['is_spammer'] = false;
    $bb2html = process_links($bb2html);

    //	no tinned pidgeon!! (you probably have to be Scottish to understand this joke)
    if ($prevent_spam and $GLOBALS['is_spammer']) {
        $GLOBALS['cbparser']['state'] = 3;
        $GLOBALS['cbparser']['warning_message'] .= $GLOBALS['cbparser']['warnings']['spammer'];
        $GLOBALS['cbparser']['text'] = '';
        //return false; // zero-tolerance!
        return $GLOBALS['spammer_return_string']; // zero-tolerance!
    }

    // the bbcode proper..
    // news headline block
    $bb2html = str_replace('[news]', '<div class="cb-news">', $bb2html);
    $bb2html = str_replace('[/news]', '<!--news--></div>', $bb2html);

    // references - we need to create the whole string first, for the str_replace
    $r1 = '<a class="cb-refs-title" href="#refs-' . $id_title . '" title="' . $cb_ref_title . '">';
    $bb2html = str_replace('[ref]', $r1, $bb2html);
    $bb2html = str_replace('[/ref]', '<!--ref--></a>', $bb2html);
    $ref_start = '<div class="cb-ref" id="refs-' . $id_title . '">
<a class="ref-title" title="back to the text" href="javascript:history.go(-1)">references:</a>
<div class="reftext">';
    $bb2html = str_replace('[reftxt]', $ref_start, $bb2html);
    $bb2html = str_replace('[/reftxt]', '<!--reftxt-->
</div>
</div>', $bb2html);

    // ordinary transformations..
    // we rely on the browser producing \r\n (DOS) carriage returns, as per spec.
    $bb2html = str_replace("\r", '<br />', $bb2html);  // the \n remains, and makes the raw html readable
    $bb2html = str_replace('[b]', '<strong>', $bb2html); // ie. "\r\n" becomes "<br />\n"
    $bb2html = str_replace('[/b]', '</strong>', $bb2html);
    $bb2html = str_replace('[i]', '<em>', $bb2html);
    $bb2html = str_replace('[/i]', '</em>', $bb2html);
    $bb2html = str_replace('[u]', '<span class="underline">', $bb2html);
    $bb2html = str_replace('[/u]', '<!--u--></span>', $bb2html);
    $bb2html = str_replace('[big]', '<big>', $bb2html);
    $bb2html = str_replace('[/big]', '</big>', $bb2html);
    $bb2html = str_replace('[sm]', '<small>', $bb2html);
    $bb2html = str_replace('[/sm]', '</small>', $bb2html);

    // tables (couldn't resist this, too handy)
    $bb2html = str_replace('[t]', '<div class="cb-table">', $bb2html);
    $bb2html = str_replace('[bt]', '<div class="cb-table-b">', $bb2html);
    $bb2html = str_replace('[st]', '<div class="cb-table-s">', $bb2html);
    $bb2html = str_replace('[/t]', '<!--table--></div><div class="clear"></div>', $bb2html);
    $bb2html = str_replace('[c]', '<div class="cell">', $bb2html); // regular 50% width
    $bb2html = str_replace('[c2]', '<div class="cell">', $bb2html); // in-case they do an intuitive thang
    $bb2html = str_replace('[c1]', '<div class="cell1">', $bb2html); // cell data 100% width
    $bb2html = str_replace('[c3]', '<div class="cell3">', $bb2html);
    $bb2html = str_replace('[c4]', '<div class="cell4">', $bb2html);
    $bb2html = str_replace('[c5]', '<div class="cell5">', $bb2html);
    $bb2html = str_replace('[/c]', '<!--end-cell--></div>', $bb2html);
    $bb2html = str_replace('[r]', '<div class="cb-tablerow">', $bb2html); // a row
    $bb2html = str_replace('[/r]', '<!--row--></div>', $bb2html);

    $bb2html = str_replace('[box]', '<span class="box">', $bb2html);
    $bb2html = str_replace('[/box]', '<!--box--></span>', $bb2html);
    $bb2html = str_replace('[bbox]', '<div class="box">', $bb2html);
    $bb2html = str_replace('[/bbox]', '<!--box--></div>', $bb2html);

    // simple lists..
    $bb2html = str_replace('[*]', '<li>', $bb2html);
    $bb2html = str_replace('[/*]', '</li>', $bb2html);
    $bb2html = str_replace('[ul]', '<ul>', $bb2html);
    $bb2html = str_replace('[/ul]', '</ul>', $bb2html);
    $bb2html = str_replace('[list]', '<ul>', $bb2html);
    $bb2html = str_replace('[/list]', '</ul>', $bb2html);
    $bb2html = str_replace('[ol]', '<ol>', $bb2html);
    $bb2html = str_replace('[/ol]', '</ol>', $bb2html);

    // fix up gaps..
    $bb2html = str_replace('</li><br />', '</li>', $bb2html);
    $bb2html = str_replace('<ul><br />', '<ul>', $bb2html);
    $bb2html = str_replace('</ul><br />', '</ul>', $bb2html);
    $bb2html = str_replace('<ol><br />', '<ol>', $bb2html);
    $bb2html = str_replace('</ol><br />', '</ol>', $bb2html);

    // anchors and stuff..
    $bb2html = str_replace('[img]', '<img class="cb-img" src="', $bb2html);
    $bb2html = str_replace('[/img]', '" alt="an image" style="max-width:100%" />', $bb2html);   // Dark ToDo
    // encode the URI part? //:2do.
    // what about custom alt tags. hmm. //:2do.
    // clickable mail URL ..
    $bb2html = preg_replace_callback("/\[mmail\=(.+?)\](.+?)\[\/mmail\]/i", "create_mmail", $bb2html);
    $bb2html = preg_replace_callback("/\[email\=(.+?)\](.+?)\[\/email\]/i", "create_mail", $bb2html);

    // other URLs..
    $bb2html = str_replace('[url]', '<br /><br /><div class="warning">please check your URL bbcode syntax!!!</div><br /><br />', $bb2html);
    $bb2html = str_replace('[eurl=', '<a class="eurl" onclick="window.open(this.href); return false;" href=', $bb2html);
    $bb2html = str_replace('[turl=', '<a class="turl" title=', $bb2html); /* title-only url */
    $bb2html = str_replace('[purl=', '[url=', $bb2html); /* title-only url */
    $bb2html = str_replace('[url=', '<a class="url" href=', $bb2html); /* on-page url */
    $bb2html = str_replace('[/url]', '<!--url--></a>', $bb2html);
    // encode the URI part? //:2do.
    // check for spammer strings in URL right here //:2do.
    // floaters..
    $bb2html = str_replace('[right]', '<div class="right">', $bb2html);
    $bb2html = str_replace('[/right]', '<!--right--></div>', $bb2html);
    $bb2html = str_replace('[left]', '<div class="left">', $bb2html);
    $bb2html = str_replace('[/left]', '<!--left--></div>', $bb2html);

    // code
    $bb2html = str_replace('[tt]', '<tt>', $bb2html);
    $bb2html = str_replace('[/tt]', '</tt>', $bb2html);
    $bb2html = str_replace('[code]', '<span class="code">', $bb2html);
    $bb2html = str_replace('[/code]', '<!--code--></span>', $bb2html);
    $bb2html = str_replace('[coderz]', '<div class="coderz">', $bb2html);
    $bb2html = str_replace('[/coderz]', '<!--coderz--></div>', $bb2html);

    // simple quotes..
    $bb2html = str_replace('[quote]', '<cite>', $bb2html);
    $bb2html = str_replace('[/quote]', '</cite>', $bb2html);

    // divisions..
    $bb2html = str_replace('[hr]', '<hr class="cb-hr" />', $bb2html);
    $bb2html = str_replace('[hr2]', '<hr class="cb-hr2" />', $bb2html);
    $bb2html = str_replace('[hr3]', '<hr class="cb-hr3" />', $bb2html);
    $bb2html = str_replace('[hr4]', '<hr class="cb-hr4" />', $bb2html);
    $bb2html = str_replace('[hrr]', '<hr class="cb-hr-regular" />', $bb2html);
    $bb2html = str_replace('[block]', '<blockquote><div class="blockquote">', $bb2html);
    $bb2html = str_replace('[/block]', '</div></blockquote>', $bb2html);
    $bb2html = str_replace('</div></blockquote><br />', '</div></blockquote>', $bb2html);

    $bb2html = str_replace('[mid]', '<div class="cb-center">', $bb2html);
    $bb2html = str_replace('[/mid]', '<!--mid--></div>', $bb2html);

    // dropcaps. five flavours, small up to large.. [dc1]I[/dc] -> [dc5]W[/dc]
    $bb2html = str_replace('[dc1]', '<span class="dropcap1">', $bb2html);
    $bb2html = str_replace('[dc2]', '<span class="dropcap2">', $bb2html);
    $bb2html = str_replace('[dc3]', '<span class="dropcap3">', $bb2html);
    $bb2html = str_replace('[dc4]', '<span class="dropcap4">', $bb2html);
    $bb2html = str_replace('[dc5]', '<span class="dropcap5">', $bb2html);
    $bb2html = str_replace('[/dc]', '<!--dc--></span>', $bb2html);

    $bb2html = str_replace('[h2]', '<h2>', $bb2html);
    $bb2html = str_replace('[/h2]', '</h2>', $bb2html);
    $bb2html = str_replace('[h3]', '<h3>', $bb2html);
    $bb2html = str_replace('[/h3]', '</h3>', $bb2html);
    $bb2html = str_replace('[h4]', '<h4>', $bb2html);
    $bb2html = str_replace('[/h4]', '</h4>', $bb2html);
    $bb2html = str_replace('[h5]', '<h5>', $bb2html);
    $bb2html = str_replace('[/h5]', '</h5>', $bb2html);
    $bb2html = str_replace('[h6]', '<h6>', $bb2html);
    $bb2html = str_replace('[/h6]', '</h6>', $bb2html);

    // fix up input spacings..
    $bb2html = str_replace('</h2><br />', '</h2>', $bb2html);
    $bb2html = str_replace('</h3><br />', '</h3>', $bb2html);
    $bb2html = str_replace('</h4><br />', '</h4>', $bb2html);
    $bb2html = str_replace('</h5><br />', '</h5>', $bb2html);
    $bb2html = str_replace('</h6><br />', '</h6>', $bb2html);

    // oh, all right then..
    // my [color=red]colour[/color] [color=blue]test[/color] [color=#C5BB41]test[/color]
    $bb2html = preg_replace('/\[color\=(.+?)\](.+?)\[\/color\]/is', "<span style=\"color:$1\">$2<!--color--></span>", $bb2html);

    // I noticed someone trying to do these at the org. use standard pixel sizes
    $bb2html = preg_replace('/\[size\=(.+?)\](.+?)\[\/size\]/is', "<span style=\"font-size:$1px\">$2<!--size--></span>", $bb2html);

    // for URL's, and InfiniTags™..
    $bb2html = str_replace('[', ' <', $bb2html); // you can just replace < and >  with [ and ] in your bbcode
    $bb2html = str_replace(']', ' >', $bb2html); // for instance, [strike] cool [/strike] would work!
    $bb2html = str_replace('/ >', '/>', $bb2html); // self-closers
    $bb2html = str_replace('-- >', '-->', $bb2html); // close comments
    // get back any real square brackets..
    $bb2html = str_replace('**$@$**', '[', $bb2html);
    $bb2html = str_replace('**@^@**', ']', $bb2html);

    // prevent some twat running arbitary php commands on our web server
    // I may roll this into the xss prevention and just keep it all enabled. hmm.
    $php_str = $bb2html;
    $bb2html = preg_replace("/<\?(.*)\? ?>/is", "<strong>script-kiddie prank: &lt;?\\1 ?&gt;</strong>", $bb2html);
    if ($php_str != $bb2html) {
        $GLOBALS['cbparser']['state'] = 5;
    }

    // re-insert the preformatted text blocks..
    $cp = count($pre) + 9998;
    for ($i = 9999; $i <= $cp; $i++) {
        $bb2html = str_replace("***pre_string***$i", '<pre>' . $pre[$i] . '</pre>', $bb2html);
    }
    if (!empty($GLOBALS['do_debug']))
        debug("\n" . '$bb2html (after pre back in): ' . "$bb2html\n\n"); // :debug:
        // re-insert the cool colored code..
        // we fix-up the output, too, make it xhtml strict.
 $cp = count($ccc) - 1;
    for ($i = 0; $i <= $cp; $i++) {
        $tmp_str = substr($ccc[$i], 5, -6);
        $tmp_str = highlight_string(stripslashes($tmp_str), true);
        $tmp_str = str_replace('font color="', 'span style="color:', $tmp_str);
        $tmp_str = str_replace('font', 'span', $tmp_str); // erm.
        if (get_magic_quotes_gpc ())
            $tmp_str = addslashes($tmp_str);
        $bb2html = str_replace("***ccc_string***$i", '<div class="cb-ccc">' . $tmp_str . '<!--ccccode--></div>', $bb2html);
    }

    $bb2html = slash_it($bb2html);
    if (!empty($GLOBALS['do_debug'])) {
        debug("\n\n" . 'cbparser outgoing [$bb2html]: ' . $bb2html . "\n\n");
    }// :debug:
    if ($GLOBALS['trans_warp_drive']) {
        $bb2html = strrev($bb2html);
    }

    return $bb2html;
}

/*  end function bb2html()  */

/*  function html2bb()  */

function html2bb() {
    global $cb_ref_title;
    if (func_num_args() == 2) {
        $id_title = func_get_arg(1);
    } else {
        $id_title = '';
    }
    $html2bb = func_get_arg(0);

    // legacy bbcode conversion..
    if (stristr($html2bb, '<font') or stristr($html2bb, 'align=') or stristr($html2bb, 'border=') or stristr($html2bb, '<i>') or stristr($html2bb, 'target=') or stristr($html2bb, '<u>') or stristr($html2bb, '<br>')) {
        return oldhtml2bb($html2bb, $id_title);
    }

    // we presume..
    $GLOBALS['cbparser']['state'] = 0;

    // pre-formatted text
    $pre = array();
    $i = 9999;
    while ($pre_str = stristr($html2bb, '<pre>')) {
        $pre_str = substr($pre_str, 0, strpos($pre_str, '</pre>') + 6);
        $html2bb = str_replace($pre_str, "***pre_string***$i", $html2bb);
        $pre[$i] = str_replace("\n", "\r\n", $pre_str);
        $i++;
    }

    // cool colored code
    $ccc = array();
    $i = 0;
    while ($ccc_str = stristr($html2bb, '<div class="cb-ccc">')) {
        $ccc_str = substr($ccc_str, 0, strpos($ccc_str, '<!--ccccode--></div>') + 20);
        $html2bb = str_replace($ccc_str, "***ccc_string***$i", $html2bb);
        $ccc[$i] = str_replace("<br />", "\r\n", $ccc_str);
        $i++;
    }

    $html2bb = str_replace('[', '***^***', $html2bb);
    $html2bb = str_replace(']', '**@^@**', $html2bb);

    // news
    $html2bb = str_replace('<div class="cb-news">', '[news]', $html2bb);
    $html2bb = str_replace('<!--news--></div>', '[/news]', $html2bb);

    // references..
    $r1 = '<a class="cb-refs-title" href="#refs-' . $id_title . '" title="' . $cb_ref_title . '">';
    $html2bb = str_replace($r1, "[ref]", $html2bb);
    $html2bb = str_replace('<!--ref--></a>', '[/ref]', $html2bb);
    $ref_start = '<div class="cb-ref" id="refs-' . $id_title . '">
<a class="ref-title" title="back to the text" href="javascript:history.go(-1)">references:</a>
<div class="reftext">';
    $html2bb = str_replace($ref_start, '[reftxt]', $html2bb);
    $html2bb = str_replace('<!--reftxt-->
</div>
</div>', '[/reftxt]', $html2bb);

    // let's remove all the linefeeds, unix
    $html2bb = str_replace(chr(10), '', $html2bb); //		"\n"
    // and Mac (windoze uses both)
    $html2bb = str_replace(chr(13), '', $html2bb); //		"\r"
    // 'ordinary' transformations..
    $html2bb = str_replace('<strong>', '[b]', $html2bb);
    $html2bb = str_replace('</strong>', '[/b]', $html2bb);
    $html2bb = str_replace('<em>', '[i]', $html2bb);
    $html2bb = str_replace('</em>', '[/i]', $html2bb);
    $html2bb = str_replace('<span class="underline">', '[u]', $html2bb);
    $html2bb = str_replace('<!--u--></span>', '[/u]', $html2bb);
    $html2bb = str_replace('<big>', '[big]', $html2bb);
    $html2bb = str_replace('</big>', '[/big]', $html2bb);
    $html2bb = str_replace('<small>', '[sm]', $html2bb);
    $html2bb = str_replace('</small>', '[/sm]', $html2bb);

    // tables..
    $html2bb = str_replace('<div class="cb-table">', '[t]', $html2bb);
    $html2bb = str_replace('<div class="cb-table-b">', '[bt]', $html2bb);
    $html2bb = str_replace('<div class="cb-table-s">', '[st]', $html2bb);
    $html2bb = str_replace('<!--table--></div><div class="clear"></div>', '[/t]', $html2bb);
    $html2bb = str_replace('<div class="cell">', '[c]', $html2bb);
    $html2bb = str_replace('<div class="cell1">', '[c1]', $html2bb);
    $html2bb = str_replace('<div class="cell3">', '[c3]', $html2bb);
    $html2bb = str_replace('<div class="cell4">', '[c4]', $html2bb);
    $html2bb = str_replace('<div class="cell5">', '[c5]', $html2bb);
    $html2bb = str_replace('<!--end-cell--></div>', '[/c]', $html2bb);
    $html2bb = str_replace('<div class="cb-tablerow">', '[r]', $html2bb);
    $html2bb = str_replace('<!--row--></div>', '[/r]', $html2bb);

    $html2bb = str_replace('<span class="box">', '[box]', $html2bb);
    $html2bb = str_replace('<!--box--></span>', '[/box]', $html2bb);
    $html2bb = str_replace('<div class="box">', '[bbox]', $html2bb);
    $html2bb = str_replace('<!--box--></div>', '[/bbox]', $html2bb);

    // lists. we like these.
    $html2bb = str_replace('<li>', '[*]', $html2bb);
    $html2bb = str_replace('</li>', '[/*]<br />', $html2bb); // we convert <br /> to \r\n later..
    $html2bb = str_replace('<ul>', '[list]<br />', $html2bb);
    $html2bb = str_replace('</ul>', '[/list]<br />', $html2bb);
    $html2bb = str_replace('<ol>', '[ol]<br />', $html2bb);
    $html2bb = str_replace('</ol>', '[/ol]<br />', $html2bb);

    // legacy "smilie" locations..
    if (stristr($html2bb, 'smilie')) {
        $smiley_str = 'smilie';
    } else {
        $smiley_str = 'smiley';
    }

    // images..
    $html2bb = str_replace('<img class="cb-img" src="', '[img]', $html2bb);
    $html2bb = str_replace('<img class="cb-img-right" src="', '[img]', $html2bb); // deprecation in action!
    $html2bb = str_replace('<img src="', '[img]', $html2bb); // catch certain legacy entries
    $html2bb = str_replace('<img class="cb-img-left" src="', '[img]', $html2bb);
    $html2bb = str_replace('" alt="an image" />', '[/img]', $html2bb);


    // anchors, etc..
    // da "email" tags..
    $html2bb = preg_replace_callback("/\<a class=\"cb-mail\" title=\"mail me\!\" href\=(.+?)\>(.+?)\<\!--mail--\><\/a\>/i", "get_mmail", $html2bb);

    $html2bb = preg_replace_callback("/\<a title\=\"mail me\!\" href\=(.+?)\>(.+?)\<\/a\>/i",
                    "get_email", $html2bb);

    $html2bb = str_replace('<a onclick="window.open(this.href); return false;" href=', '[eurl=', $html2bb);
    $html2bb = str_replace('<a class="eurl" onclick="window.open(this.href); return false;" href=', '[eurl=', $html2bb);
    $html2bb = str_replace('<a class="turl" title=', '[turl=', $html2bb);
    $html2bb = str_replace('<a class="purl" href=', '[url=', $html2bb);
    $html2bb = str_replace('<a class="url" href=', '[url=', $html2bb);
    $html2bb = str_replace('<!--url--></a>', '[/url]', $html2bb);
    $html2bb = str_replace('</a>', '[/url]', $html2bb); // catch for early beta html
    // floaters..
    $html2bb = str_replace('<div class="right">', '[right]', $html2bb);
    $html2bb = str_replace('<!--right--></div>', '[/right]', $html2bb);
    $html2bb = str_replace('<div class="left">', '[left]', $html2bb);
    $html2bb = str_replace('<!--left--></div>', '[/left]', $html2bb);

    // code..
    $html2bb = str_replace('<tt>', '[tt]', $html2bb);
    $html2bb = str_replace('</tt>', '[/tt]', $html2bb);
    $html2bb = str_replace('<span class="code">', '[code]', $html2bb);
    $html2bb = str_replace('<!--code--></span>', '[/code]', $html2bb);
    $html2bb = str_replace('<div class="coderz">', '[coderz]', $html2bb);
    $html2bb = str_replace('<!--coderz--></div>', '[/coderz]', $html2bb);


    $html2bb = str_replace('<cite>', '[quote]', $html2bb);
    $html2bb = str_replace('</cite>', '[/quote]', $html2bb);

    // etc..
    $html2bb = str_replace('<hr class="cb-hr" />', '[hr]', $html2bb);
    $html2bb = str_replace('<hr class="cb-hr2" />', '[hr2]', $html2bb);
    $html2bb = str_replace('<hr class="cb-hr3" />', '[hr3]', $html2bb);
    $html2bb = str_replace('<hr class="cb-hr4" />', '[hr4]', $html2bb);
    $html2bb = str_replace('<hr class="cb-hr-regular" />', '[hrr]', $html2bb);
    $html2bb = str_replace('<blockquote><div class="blockquote">', '[block]', $html2bb);
    $html2bb = str_replace('</div></blockquote>', '[/block]<br />', $html2bb);

    $html2bb = str_replace('<div class="cb-center">', '[mid]', $html2bb);
    $html2bb = str_replace('<!--mid--></div>', '[/mid]', $html2bb);

    // the irresistible dropcaps (good name for a band)
    $html2bb = str_replace('<span class="dropcap1">', '[dc1]', $html2bb);
    $html2bb = str_replace('<span class="dropcap2">', '[dc2]', $html2bb);
    $html2bb = str_replace('<span class="dropcap3">', '[dc3]', $html2bb);
    $html2bb = str_replace('<span class="dropcap4">', '[dc4]', $html2bb);
    $html2bb = str_replace('<span class="dropcap5">', '[dc5]', $html2bb);
    $html2bb = str_replace('<!--dc--></span>', '[/dc]', $html2bb);

    $html2bb = str_replace('<h2>', '[h2]', $html2bb);
    $html2bb = str_replace('</h2>', '[/h2]<br />', $html2bb);
    $html2bb = str_replace('<h3>', '[h3]', $html2bb);
    $html2bb = str_replace('</h3>', '[/h3]<br />', $html2bb);
    $html2bb = str_replace('<h4>', '[h4]', $html2bb);
    $html2bb = str_replace('</h4>', '[/h4]<br />', $html2bb);
    $html2bb = str_replace('<h5>', '[h5]', $html2bb);
    $html2bb = str_replace('</h5>', '[/h5]<br />', $html2bb);
    $html2bb = str_replace('<h6>', '[h6]', $html2bb);
    $html2bb = str_replace('</h6>', '[/h6]<br />', $html2bb);

    // pfff..
    $html2bb = preg_replace("/\<span style\=\"color:(.+?)\"\>(.+?)\<\!--color--\>\<\/span\>/is", "[color=$1]$2[/color]", $html2bb);

    // size, in pixels.
    $html2bb = preg_replace("/\<span style\=\"font-size:(.+?)px\"\>(.+?)\<\!--size--\>\<\/span\>/is", "[size=$1]$2[/size]", $html2bb);

    // bring back the brackets
    $html2bb = str_replace('***^***', '[[', $html2bb);
    $html2bb = str_replace('**@^@**', ']]', $html2bb);

    // I just threw this down here for the list fixes.
    $html2bb = str_replace('<br />', "\r\n", $html2bb);
    $html2bb = str_replace('&nbsp;', '[sp]', $html2bb);

    // InfiniTag™ enablers!
    $html2bb = str_replace(' <', '[', $html2bb);
    $html2bb = str_replace(' >', ']', $html2bb);
    $html2bb = str_replace('-->', '--]', $html2bb); // comments within comments!
    $html2bb = str_replace('/>', '/]', $html2bb); // self-closers
    //$html2bb = str_replace('&amp;', '&', $html2bb);

    $cp = count($ccc) - 1;
    for ($i = 0; $i <= $cp; $i++) {
        $html2bb = str_replace("***ccc_string***$i", '[ccc]'
                        . trim(strip_tags($ccc[$i])) . '[/ccc]', $html2bb);
    }

    $cp = count($pre) + 9998; // it all hinges on simple arithmetic
    for ($i = 9999; $i <= $cp; $i++) {
        $html2bb = str_replace("***pre_string***$i", '[pre]' . substr($pre[$i], 5, -6) . '[/pre]', $html2bb);
    }
    if (!empty($GLOBALS['do_debug'])) {
        debug("\n\n" . 'cbparser outgoing [$html2bb]: ' . $html2bb . "\n\n");
    }// :debug:
//if (!empty($GLOBALS['do_debug'])) { debug('$GLOBALS: '."\t".print_r($GLOBALS, true)."\n\n\n"); }// :debug:

    return ($html2bb);
}

/*
  create_mail
  a callback function for the email tag */

function create_mail($matches) {
    $removers = array('"', '\\'); // in case they add quotes
    $mail = str_replace($removers, '', $matches[1]);
    $mail = str_replace(' ', '%20', bbmashed_mail($mail));
    return '<a title="mail me!" href="' . $mail . '">' . $matches[2] . '</a>';
}

/*
  create *my* email
  a callback function for the mmail tag */

function create_mmail($matches) {
    global $corzblog;
    $removers = array('"', '\\'); // in case they add quotes
    $mashed_address = str_replace($removers, '', $matches[1]);
    $mashed_address = bbmashed_mail($corzblog['mail_addy'] . '?subject=' . $mashed_address);
    $mashed_address = str_replace(' ', '%20', $mashed_address); // hmmm
    return '<a class="cb-mail" title="mail me!" href="' . $mashed_address . '\">' . $matches[2] . '<!--mail--></a>';
}

/*
  get email
  a callback function for the html >> bbcode email tag */

function get_email($matches) {
    $removers = array('"', '\\', 'mailto:');
    $href = str_replace($removers, '', un_mash($matches[1]));
    return '[email=' . str_replace('%20', ' ', $href) . ']' . $matches[2] . '[/email]';
}

/*
  get *my* mail
  a callback function for the html >> bbcode mmail tag */

function get_mmail($matches) {
    global $corzblog;
    $removers = array('"', '\\'); // not strictly necessary
    $href = str_replace($removers, '', $matches[1]);
    $href = str_replace('mailto:' . $corzblog['mail_addy'] . '?subject=', '', un_mash($href));
    return '[mmail=' . str_replace('%20', ' ', $href) . ']' . $matches[2] . '[/mmail]';
}

/*
  function bbmashed_mail()

  it's handy to keep this here. used to encode your email addresses
  so the spam-bots don't chew on it.

  see <http://corz.org/engine> for more stuff like this.


 */

function bbmashed_mail($addy) {
    $addy = 'mailto:' . $addy;
    for ($i = 0; $i < strlen($addy); $i++) {
        $letters[] = $addy[$i];
    }
    while (list($key, $val) = each($letters)) {
        $r = rand(0, 20);
        if (($r > 9) and ($letters[$key] != ' ')) {
            $letters[$key] = '&#' . ord($letters[$key]) . ';';
        }
    }
    $addy = implode('', $letters);
    return str_replace(' ', '%20', $addy);
}

/*
  end function mashed_mail() */



/*
  un-mash an email address, a tricky business */

function un_mash($string) {
    $entities = array();
    for ($i = 32; $i < 256; $i++) {
        $entities['orig'][$i] = '&#' . $i . ';';
        $entities['new'][$i] = chr($i);
    } // now we have a translations array..
    return str_replace($entities['orig'], $entities['new'], $string);
}

// add slashes to a string, or don't..
function slash_it($string) {
    if (get_magic_quotes_gpc ()) {
        return stripslashes($string);
    } else {
        return $string;
    }
}

/*
  make a xhtml strict valid id..

  this function exists in the main corzblog functions,
  but cbparser goes out on its own, so...
 */

function make_valid_id($title) {
    $title = str_replace(' ', '-', strip_tags($title));
    $id_title = preg_replace("/[^a-z0-9-]*/i", '', $title);
    while (is_numeric((substr($id_title, 0, 1))) or substr($id_title, 0, 1) == '-') {
        $id_title = substr($id_title, 1);
    }
    return trim(str_replace('--', '-', $id_title));
}

/*
  encode to html entities (for <pre> tags */

function encode($string) {
    //$string = str_replace("\r\n", "\n", slash_it($string));
    $string = str_replace("\r\n", "\n", $string);
    $string = str_replace(array('[pre]', '[/pre]'), '', $string);
    return htmlentities($string, ENT_NOQUOTES, 'utf-8'); // this is plenty
}

/*
  xss clean-up
  clean up against potential xss attacks

  adapted from the bitflux xss prevention techniques..
  http://blog.bitflux.ch/wiki/XSS_Prevention

  any comments or suggestions about this to
  security at corz dot org, ta.
 */

function xssclean($string) {

    // we'll see if it still matches at the end of all this..
    if (get_magic_quotes_gpc ()) {
        $string = stripslashes($string);
    }
    $input = $string;

    // fix &entitiy\n; (except those named above)
    $string = preg_replace('#(&\#*\w+)[\x00-\x20]+;#us', "$1;", $string);
    $string = preg_replace('#(&\#x*)([0-9A-F]+);*#ius', "$1$2;", $string);
    $string = html_entity_decode($string, ENT_COMPAT);
    //$string = html_entity_decode($string, ENT_COMPAT, "utf-8"); // if your php is capable of this :pref:
    // remove "on" and other unnecessary attributes (we specify them all to prevent words like "one" being affected)
    $string = preg_replace('#(\[[^\]]+[\x00-\x20\"\'])(onabort|onactivate|onafterprint|onafterupdate|onbeforeactivate|onbeforecopy|onbeforecut|onbeforedeactivate|onbeforeeditfocus|onbeforepaste|onbeforeprint|onbeforeunload|onbeforeupdate|onblur|onbounce|oncellchange|onchange|onclick|oncontextmenu|oncontrolselect|oncopy|oncut|ondataavailable|ondatasetchanged|ondatasetcomplete|ondblclick|ondeactivate|ondrag|ondragend|ondragenter|ondragleave|ondragover|ondragstart|ondrop|onerror|onerrorupdate|onfilterchange|onfinish|onfocus|onfocusin|onfocusout|onhelp|onkeydown|onkeypress|onkeyup|onlayoutcomplete|onload|onlosecapture|onmousedown|onmouseenter|onmouseleave|onmousemove|onmouseout|onmouseover|onmouseup|onmousewheel|onmove|onmoveend|onmovestart|onpaste|onpropertychange|onreadystatechange|onreset|onresize|onresizeend|onresizestart|onrowenter|onrowexit|onrowsdelete|onrowsinserted|onscroll|onselect|onselectionchange|onselectstart|onstart|onstop|onsubmit|onunload|xmlns|datasrc|src|lowsrc|dynsrc)[^\]]*\]#isUu', "$1]", $string);

    // remove javascript and vbscript..
    $string = preg_replace('#([a-z]*)[\x00-\x20]*=?[\x00-\x20]*([\`\'\"]*)[\\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $string);
    $string = preg_replace('#([a-z]*)[\x00-\x20]*=?([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $string);

    // style expression hacks. only works in buggy ie... (fer fuxake! get a browser!)
    $string = preg_replace('#(\[[^\]]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*expression[\x00-\x20]*\([^\]]*>#iUs', "$1\]", $string);
    $string = preg_replace('#(\[[^\]]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*behaviour[\x00-\x20]*\([^\]]*>#iUs', "$1\]", $string);
    $string = preg_replace('#(\[[^\]]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^\]]*\]>#iUus', "$1\]", $string);

    // remove namespaced elements..
    $string = preg_replace('#\[/*\w+:\w[^\]]*\]#is', "", $string);

    // the really fun <tags>..
    do {
        $oldstring = $string;
        $string = preg_replace('#\[/*(applet|meta|xml|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|base|sourcetext|parsererror)[^[]*\]#is', "", $string);
    } while ($oldstring != $string); // loop through to catch tricky doubles
    //$string = html_entity encode($string, ENT_COMPAT);
    // make a note: someone tried to wonk-up the site
    if ($input !== $string) {
        $GLOBALS['cbparser']['state'] = 5;
    }

    // leave no trace..
    if (get_magic_quotes_gpc ()) {
        $string = addslashes($string);
    }
    return $string;
}

// check balance and attempt to close some tags for final publishing
function check_balance($bb2html) {
    // some tags would be pointless to attempt to close, like image tags
    // and lists, and such. better if they just fix those themselves.
    // could still use a '[img] => [/img]' type array, and include more tags.
    $GLOBALS['cbparser']['close_tags'] = '';
    $tags_to_close = array(
        '[b]',
        '[i]',
        '[u]',
        '[big]',
        '[sm]',
        '[box]',
        '[bbox]',
        '[ul]',
        '[list]',
        '[ol]',
        '[left]',
        '[right]',
        '[tt]',
        '[code]',
        '[coderz]',
        '[block]',
        '[mid]',
        '[h2]',
        '[h3]',
        '[h4]',
        '[h5]',
        '[h6]',
        '[quote]',
        '[color]');

    foreach ($tags_to_close as $key => $value) {

        $open = substr_count($bb2html, $value);
        $close_tag = '[/' . substr($value, 1);

        while (substr_count($bb2html, $close_tag) < $open) {
            $bb2html .= $close_tag;
            $GLOBALS['cbparser']['close_tags'] .= $close_tag;
            $GLOBALS['cbparser']['state'] = 2;
        }
    }

    $GLOBALS['cbparser']['text'] .= $GLOBALS['cbparser']['close_tags'];

    if ($GLOBALS['cbparser']['state'] == 2) {
        $GLOBALS['cbparser']['warning_message'] .= $GLOBALS['cbparser']['warnings']['balance_fixed'];
    }

    // some sums..
    $check_string = preg_replace("/\[(.+)\/\]/Ui", "", $bb2html); // self-closers
    $check_string = preg_replace("/\[\!--(.+)--\]/i", "", $check_string); // we support comments!
    $removers = array('[hr]', '[hr2]', '[hr3]', '[hr4]', '[sp]', '[*]', '[/*]');
    $check_string = str_replace($removers, '', $check_string);

    if (((substr_count($check_string, "[")) != (substr_count($check_string, "]")))
            or ((substr_count($check_string, "[/")) != ((substr_count($check_string, "[")) / 2))
            // a couple of common errors (definitely the main culprits for tag mixing errors)..
            or (substr_count($check_string, "[b]")) != (substr_count($check_string, "[/b]"))
            or (substr_count($check_string, "[i]")) != (substr_count($check_string, "[/i]"))) {
        $GLOBALS['cbparser']['state'] = 1;
        $GLOBALS['cbparser']['warning_message'] .= $GLOBALS['cbparser']['warnings']['imbalanced'];
        return false;
    }

    if (!empty($GLOBALS['do_debug'])) {
        debug("\n" . '$bb2html Final: ' . "$bb2html\n\n");
    }// :debug:

    return $bb2html;
}

// another possibility is to scan the comment and work out which tags are used, close them.
// simply create a no-check list of non-closing tags to check against, and close others.
// the non-symetrical tags can cause problems, though.


/*
  check the URL's
  if the post is from a known spammer, set $GLOBALS['is_spammer'] to true.
 */
function process_links($bb2html) {
    /*
      this is in two parts. first we check against our list of known spammer strings
      (generally domains). In the future, I'd hope to hook this up to some reliable,
      well-kept online database of known spammer domains.
     */

    if (!empty($GLOBALS['spammer_file']) and file_exists($GLOBALS['spammer_file'])) {
        $GLOBALS['spammer_strings'] = get_spammer_strings($GLOBALS['spammer_file']);
    }

    // extract URL's into an array..
    $url_array = explode('url=', $bb2html);

    // spam-bot user-agents..
    $double_agents = explode(',', $GLOBALS['spammer_agents']);
    foreach ($double_agents as $double_agent) {
        $double_agent = trim($double_agent);
        if ($double_agent and stristr(@$_SERVER['HTTP_USER_AGENT'], trim($double_agent))) {
            $GLOBALS['is_spammer'] = true;
            return $GLOBALS['spammer_return_string'];
        }
    }
    // we may do more, later.
    return $bb2html;
}

// read the spammers file into an array of spammer strings..
function get_spammer_strings($spammers_file) {
    if (file_exists($spammers_file)) {
        $fp = fopen($spammers_file, 'rb');
        $list = fread($fp, filesize($spammers_file));
        fclose($fp);
        clearstatcache();
    } else {
        $GLOBALS['cbparser']['warning_message'] .= '<div class="centered" id="message">spammer file is missing!</div>';
        if (!empty($GLOBALS['spammer_strings'])) {
            return $GLOBALS['spammer_strings'];
        } else {
            $GLOBALS['cbparser']['warning_message'] .= '<div class="centered" id="message">spammer file is missing, and spammer_strings have been deleted. sorree!</div>';
            return array(0, '');
        }
    }
    return explode("\n", trim($list));
}

/*
  bbcode to lowercase.

  ensure all bbcode is lower case..
  don't lowercase URIs, though.
 */

function bbcode_to_lower($tring) {
    while ($str = strstr($tring, '[')) {
        if (strpos($str, ']') > (strpos($str, '"'))) {
            $k = '"';
        } else {
            $k = ']';
        }
        $str = substr($str, 1, strpos($str, $k));
        $tring = str_replace('[' . $str, '**%^%**' . strtolower($str), $tring);
    }
    return str_replace('**%^%**', '[', $tring);
}

?>