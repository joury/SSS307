<?php 
$banner='
<div id="hd">
            <link type="text/css" rel="stylesheet" href="./images/uh_rsa-1.css">
            <style type="text/css">
                #ygma{
                    position:relative;
                    z-index:1000;
                    zoom:1;
                }
                #ygma #ygma-search{
                    width:480px;
                }
                #ygma #ygma-search input{
                    width:250px;
                }
                #ygma li{
                    font-size:100%;
                }
                #ygma em{
                    font-size:1em;
                    font-weight:bold !important;
                }
                #ygma p{
                    font-size:100%;
                }
                #ygma span{
                    overflow:visible !important;
                }
                #pa2-nav li a{
                    word-wrap:normal;
                }
                #y-ks-banner, #y-ks-mini-banner{
                    z-index:139 !important;
                }
                #ygma .sp{
                    background-image:url(http://l.yimg.com/a/lib/uh/15/sprites/answers-1.0.4.png);
                }
            </style>
            <div id="ygma">
                <div id="ygmaheader">
                    <div class="bd sp">
                        <div id="ymenu" class="ygmaclr">
                            <div id="mepanel">
                                <ul id="mepanel-nav">
                                    <form action="" name="login" method="POST">
                                        <li class="me1">
                                            <input type="text" name="username">
                                            <input type="password" name="password">
                                            <input type="submit" value="Register">
                                            <input type="submit" value="Log in">
                                        </li>
                                    </form>
                                </ul>
                            </div>
                            <div id="pa">
                                <div id="pa-wrapper" style="width: 140px;">
                                    <ul id="pa2-nav" class="sp" style="width: 140px;">
                                        <li class="pa1 sp">
                                            <a class="sp" href="" target="_top">Logout</a> <!-- ToDo : Link invoegen -->
                                        </li>
                                        <li class="pa2 sp">
                                            <a class="sp" href="" target="_top">Help</a> <!-- ToDo : Link invoegen -->
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div id="yahoo" class="ygmaclr">
                            <div id="ygmabot">
                                <a href="./images/index.htm" id="ygmalogo" target="_top"><img id="ygmalogoimg" src="./images/ans.gif" alt="Yahoo! Answers!!" height="26" width="257"></a> <!-- ToDo: Logo invoegen -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tabbed-content">
                <ul class="tabs" id="yan-nav">
                    <li class="menu" id="yan-nav-home">
                        <a href="">Home</a> <!-- ToDo : Link invoegen -->
                    </li>
                    <li id="yan-nav-browse" class="current menu">
                        <a href="">Categories</a> <!-- ToDo : Link invoegen -->
                    </li>
                    <li class="menu" id="yan-nav-about">
                        <a href="">Profile</a> <!-- ToDo : Link invoegen -->
                    </li>
                </ul>
            </div>
            <div id="yan-banner">
                <ul class="short">
                    <li id="yan-banner-ask">
                        <form action="file:///C:/question/ask;_ylt=AgU9SwXIffzRvq4Ur8omKrSzxQt.;_ylv=3" method="get">
                            <div>
                                
                                <div>
                                    <label class="offscreen" for="banner-ask">What would you like to ask?</label>
                                    <input class="default" value="" maxlength="110" id="banner-ask" name="title" type="text">
                                    <span class="cta">
                                        <button id="" value="Continue" name="submit-go" class="cta-button">
                                            <span>
                                                <span>
                                                    <span>
                                                        <span>Ask!</span>
                                                    </span>
                                                </span>
                                            </span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </li>
                    <li id="yan-banner-answer"> <!-- ToDo : Verwijderen ? -->
                        <form action="file:///C:/question/ask;_ylt=AgU9SwXIffzRvq4Ur8omKrSzxQt.;_ylv=3" method="get">
                            <div>
                                <div>
                                    <label class="offscreen" for="banner-answer">What would you like to search?</label>
                                    <input class="default" value="" maxlength="110" id="banner-answer" name="title" type="text">
                                    <span class="cta">
                                        <button id="" value="Continue" name="submit-go" class="cta-button">
                                            <span>
                                                <span>
                                                    <span>
                                                        <span>Search!</span>
                                                    </span>
                                                </span>
                                            </span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </li>
                </ul>
            </div>
            <div id="yan-header">
            </div>
        </div>
';
?>