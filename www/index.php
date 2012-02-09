<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>YouTchouk.com ^_^</title>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
    
    <link rel="stylesheet" type="text/css" href="resources/css/reset-min.css" />
    <link href="http://fonts.googleapis.com/css?family=Asap:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="resources/css/style.css" />
    
    <script type="text/javascript" src="resources/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="resources/js/jquery.tools.min.js"></script>
    <script type="text/javascript" src="resources/js/javascript.js"></script>
    <script type="text/javascript">
        var channel = 'youtchouk';
        var maxResultsHome = 30;
        <?php if (isset($_GET['q'])) { ?>var main_feed = 'https://gdata.youtube.com/feeds/api/users/'+channel+'/uploads?alt=json&orderby=updated&q=<?php echo $_GET['q']; ?>';
        <?php } elseif (isset($_GET['t'])) { ?>var main_feed = 'https://gdata.youtube.com/feeds/api/users/'+channel+'/uploads/-/<?php echo $_GET['t']; ?>?alt=json&orderby=updated';
        <?php } else { ?>var main_feed = 'https://gdata.youtube.com/feeds/api/users/'+channel+'/uploads?alt=json&orderby=updated&max-results='+maxResultsHome;
        <?php } ?>
    </script>

    <script type="text/javascript">
        // var _gaq = _gaq || [];
        // _gaq.push(['_setAccount', 'UA-6536789-1']);
        // _gaq.push(['_trackPageview']);

        // (function() {
        //     var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        //     ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        //     var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        // })();
    </script>
</head>
<body>
    <div id="wrapper">
        <h1><a href="./">YouTchouk</a></h1>
        <div id="menu" class="box">
            <ul>
                <li><a href="?t=2011">2011</a></li>
                <li><a href="#">Categoria 2</a></li>
                <li><a href="#">Categoria 3</a></li>
                <li><a href="#">Categoria 4</a></li>
                <li><a href="#">Categoria 5</a></li>
                <li><a href="#">Categoria 6</a></li>
                <li><a href="#">Categoria 7</a></li>
                <li><a href="#">Categoria 8</a></li>
            </ul>
        </div>
        <?php if (isset($_GET['v'])) { ?>
        <div id="video" class="box">
            <iframe width="640" height="480" src="http://www.youtube.com/embed/<?php echo $_GET['v']; ?>?autoplay=1" frameborder="0" allowfullscreen></iframe>
        </div>
        <?php } else { ?>
        <div id="titolo" class="box">
            <h2><?php
                if (isset($_GET['q']))
                    echo 'Search results for <em>'.$_GET['q'].'</em>';
                elseif (isset($_GET['t']))
                    echo $_GET['t'];
                else
                    echo 'Most recent';
            ?></h2>
        </div>
        <div id="results" class="box">
            <div id="search_result">
                <div class="items">
                    <span class="loading">Loading...</span>
                </div>
            </div>
        </div>
        <?php } ?>
        <div id="search" class="box">
            <form id="search_form">
                <input id="search_text" <?php if (isset($_GET['q'])) echo ' value="'.$_GET['q'].'"'; ?>/>
            </form>
        </div>
        <div id="most_viewed" class="box">
            <h3>Most viewed</h3>
            <ul><span class="loading">Loading...</span></ul>
        </div>
        <div id="top" class="box">
            <h3>Best</h3>
            <ul><span class="loading">Loading...</span></ul>
        </div>
        <div id="adsense" class="box">
            <div class="adsense_wrapper">
                <script type="text/javascript">
                <!--
                    // google_ad_client = "pub-9138021364556759";
                    // /* 468x60, creato 18/10/08 */
                    // google_ad_slot = "4149347112";
                    // google_ad_width = 468;
                    // google_ad_height = 60;
                -->
                </script>
                <!-- <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script> -->
            </div>
        </div>
        <div id="social" class="box">
            <a id="fb" href="http://www.facebook.com/pages/YouTchouk/140981555717"></a>
            <a id="yt" href="http://www.youtube.com/youtchouk"></a>
        </div>
    </div>
</body>
</html>
    