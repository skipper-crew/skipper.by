                                                                
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Flash HTTP Player - Video On Demand Streaming</title>

	<!-- Framework CSS -->
	<link rel="stylesheet" href="css/screen.css" type="text/css" media="screen, projection">
	<link rel="stylesheet" href="css/wowza.css" type="text/css" />
        
        <!-- Include CSS to eliminate any default margins/padding and set the height of the html element and 
         the body element to 100%, because Firefox, or any Gecko based browser, interprets percentage as 
         the percentage of the height of its parent container, which has to be set explicitly.  Fix for
         Firefox 3.6 focus border issues.  Initially, don't display flashContent div so it won't show 
         if JavaScript disabled.
         -->
        <style type="text/css" media="screen"> 
  
            object:focus { outline:none; }
            #flashContent { display:none; }
            </style>
        
        <!-- Enable Browser History by replacing useBrowserHistory tokens with two hyphens -->
        <!-- BEGIN Browser History required section -->
        <link rel="stylesheet" type="text/css" href="history/history.css" />
        <script type="text/javascript" src="history/history.js"></script>
        <!-- END Browser History required section -->  
        
        <script type="text/javascript" src="swfobject.js"></script>
        <script type="text/javascript">
            // For version detection, set to min. required Flash Player version, or 0 (or 0.0.0), for no version detection.
            var swfVersionStr = "10.2.0";
            // To use express install, set to playerProductInstall.swf, otherwise the empty string.
            var xiSwfUrlStr = "playerProductInstall.swf";
            var flashvars = {};
            var params = {};
            params.quality = "high";
            params.bgcolor = "#000000";
            params.allowscriptaccess = "sameDomain";
            params.allowfullscreen = "true";
            var attributes = {};
            attributes.id = "VOD";
            attributes.name = "VOD";
            attributes.align = "middle";
            swfobject.embedSWF(
                               "VOD.swf", "flashContent",
                               "655", "530",
                               swfVersionStr, xiSwfUrlStr,
                               flashvars, params, attributes);
            // JavaScript enabled so display the flashContent div in case it is not replaced with a swf object.
            swfobject.createCSS("#flashContent", "display:block;text-align:left;");
            
            </script>
</head>
<body>

    <div class="container">
		<!-- HEADER -->
		<!-- END HEADER -->
		<!-- EXAMPLE PLAYER: WIDTH of this player should be 630px, height will vary depending on the example-->
        <div class="span-16">
			<!-- SWFObject's dynamic embed method replaces this alternative HTML content with Flash content when enough
             JavaScript and Flash plug-in support is available. The div is initially hidden so that it doesn't show
             when JavaScript is disabled.
             -->
            <div id="flashContent">
                <p>
                To view this page ensure that Adobe Flash Player version
                10.2.0 or greater is installed.
                </p>
                <script type="text/javascript">
                    var pageHost = ((document.location.protocol == "https:") ? "https://" : "http://");
                    document.write("<a href='http://www.adobe.com/go/getflashplayer'><img src='"
                                   + pageHost + "www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a>" );
                    </script>
            </div>
            
            <noscript>
                <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="655" height="530" id="VOD">
                    <param name="movie" value="VOD.swf" />
                    <param name="quality" value="high" />
                    <param name="bgcolor" value="#000000" />
                    <param name="allowScriptAccess" value="sameDomain" />
                    <param name="allowFullScreen" value="true" />
                    <!--[if !IE]>-->
                    <object type="application/x-shockwave-flash" data="VOD.swf" width="655" height="530">
                        <param name="quality" value="high" />
                        <param name="bgcolor" value="#000000" />
                        <param name="allowScriptAccess" value="sameDomain" />
                        <param name="allowFullScreen" value="true" />
                        <!--<![endif]-->
                        <!--[if gte IE 6]>-->
                        <p>
                        Either scripts and active content are not permitted to run or Adobe Flash Player version
                        10.2.0 or greater is not installed.
                        </p>
                        <!--<![endif]-->
                        <a href="http://www.adobe.com/go/getflashplayer">
                            <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash Player" />
                        </a>
                        <!--[if !IE]>-->
                    </object>
                    <!--<![endif]-->
                </object>
            </noscript>
                            </li>
                        </ul>
                    </li>
                </ol>		
</div>
		<!-- SIDEBAR -->
        <div class="span-7 prepend-1 last">
        </div>
		<!-- FOOTER -->
        <div class="span-24">
<ul>
	<h3>Aviable Streams</h3>
                <li>rtmp://194.84.36.34/vod/mp4:sample.mp4<br>
                <li>rtmp://194.84.36.34/vod/mp4:sample2.mp4<br>
                <li>http://194.84.36.34:1935/vod/mp4:sample.mp4/manifest.f4m<br>
                <li>http://194.84.36.34:1935/vod/mp4:sample2.mp4/manifest.f4m<br>
</body>
</html>
