<?php

    if (isset($_GET['video'])) {
        header('Location: /yt/?video='.$_GET['video']);
        die();
    }

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>YOUtchouk - Let's Play TchoukBall!</title>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
    <link rel="shortcut icon" href="resources/img/favicon.png" type="image/png" />
    <link rel="icon" href="resources/img/favicon.png" type="image/png" />
    
    <link rel="stylesheet" type="text/css" href="resources/css/reset-min.css" />
    <link href="http://fonts.googleapis.com/css?family=Asap:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="resources/css/style.css" />
    
    <script type="text/javascript" src="resources/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="resources/js/jquery.tools.min.js"></script>
    <script type="text/javascript">
        var channel = 'youtchouk';
        var maxResultsHome = 6;
        var main_feed;
        var video_data;
        <?php if (isset($_GET['q'])) { ?>main_feed = 'https://gdata.youtube.com/feeds/api/users/'+channel+'/uploads?alt=jsonc&v=2&max-results='+maxResultsHome+'&q=<?php echo $_GET['q']; ?>';
        <?php } elseif (isset($_GET['t'])) { ?>main_feed = 'https://gdata.youtube.com/feeds/api/users/'+channel+'/uploads/?alt=jsonc&v=2&category=<?php echo $_GET['t']; ?>&max-results='+maxResultsHome;
        <?php } elseif (isset($_GET['p'])) { ?>main_feed = 'https://gdata.youtube.com/feeds/api/playlists/<?php echo $_GET['p']; ?>?alt=jsonc&v=2&max-results='+maxResultsHome;
        <?php } elseif (isset($_GET['s'])) { ?>main_feed = 'https://gdata.youtube.com/feeds/api/users/'+channel+'/uploads?alt=jsonc&v=2&orderby=<?php echo $_GET['s']; ?>&max-results='+maxResultsHome;
        <?php } elseif (isset($_GET['v'])) { ?>video_data = 'https://gdata.youtube.com/feeds/api/videos/<?php echo $_GET['v']; ?>?alt=jsonc&v=2';
        <?php } else { ?>main_feed = 'https://gdata.youtube.com/feeds/api/playlists/A84A9ACFED641E80?alt=jsonc&v=2&max-results='+maxResultsHome;
        <?php } ?>
    </script>
    <script type="text/javascript" src="resources/js/javascript.js"></script>

    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-6536789-1']);
        _gaq.push(['_trackPageview']);

        (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
    </script>
</head>
<body>
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/it_IT/all.js#xfbml=1&appId=242888632473039";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
    <div id="wrapper">
        <h1><a href="./">YouTchouk</a></h1>
        <div style="float:left;width:214px;">
            <div id="menu" class="box">
                <ul>
                    <li><a href="./">Home page</a></li>
                    <li><a href="?s=published">Last videos</a></li>
                    <li><a href="?t=National%2Cchampionship">National Championship</a></li>
                    <li><a href="?t=EWC">EWC</a></li>
                    <li><a href="?t=Beach">Beach</a></li>
                    <li><a href="?t=Videoclip">Videoclip</a></li>
                    <li><a href="?t=Rules">Rules</a></li>
                    <li><a href="/yt">Old YouTchouk</a></li>
                </ul>
            </div>
            <div id="search" class="box">
                <form id="search_form">
                    <input id="search_text" <?php if (isset($_GET['q'])) echo ' value="'.$_GET['q'].'"'; ?>/>
                </form>
            </div>
        </div>
        <?php if (isset($_GET['v'])) { ?>
        <div id="video" class="box">
            <iframe width="642" height="480" src="http://www.youtube.com/embed/<?php echo $_GET['v']; ?>?autoplay=1" frameborder="0" allowfullscreen></iframe>
        </div>
        <div id="descrizione" class="box">
            <div class="fb-like-container">
                <div class="fb-like" data-href="http://www.youtchouk.com/?v=<?php echo $_GET['v']; ?>" data-send="false" data-layout="box_count" data-width="64" data-show-faces="true" data-colorscheme="dark"></div>
            </div>
        </div>
        <?php } else { ?>
        <div id="titolo" class="box">
            <h2><?php
                if (isset($_GET['q']))
                    echo 'Search results for <em>'.$_GET['q'].'</em>';
                elseif (isset($_GET['t']))
                    echo str_replace(',', ' ', $_GET['t']);
                elseif (isset($_GET['p']))
                    echo '';
                elseif (isset($_GET['s']) && $_GET['s'] == 'published')
                    echo 'Last videos';
                elseif (isset($_GET['s']) && $_GET['s'] == 'viewCount')
                    echo 'Most viewed';
                elseif (isset($_GET['s']) && $_GET['s'] == 'rating')
                    echo 'Best videos';
                else
                    echo 'Home page';
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
        <div id="most_viewed" class="box">
            <h3><a href="?s=viewCount">Most viewed</a></h3>
            <ul><span class="loading">Loading...</span></ul>
        </div>
        <div id="top" class="box">
            <h3><a href="?s=rating">Best</a></h3>
            <ul><span class="loading">Loading...</span></ul>
        </div>
        <div id="banner" class="box">
            <div id="join">
                Have you some tchoukball videos?<br />
                Do you want to share them on YOUtchouk?<br />
                <big><span>Join</span> us!</big><br />
                Write an email to join@youtchouk.com
            </div>
        </div>
        <div id="social" class="box">
            <a id="fb" href="http://www.facebook.com/youtchouk">Facebook Page</a>
            <a id="yt" href="http://www.youtube.com/youtchouk">Youtube Page</a>
        </div>
        <div id="partner" class="box">
            <a id="ftbi" href="http://www.tchoukball.it">FTBI - Federazione TchoukBall Italia</a>
        </div>
        <div id="adsense" class="box">
            <div class="adsense_wrapper">
                <script type="text/javascript">
                <!--
                    google_ad_client = "pub-9138021364556759";
                    /* 468x60, creato 18/10/08 */
                    google_ad_slot = "4149347112";
                    google_ad_width = 468;
                    google_ad_height = 60;
                -->
                </script>
                <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
            </div>
        </div>
    </div>
    <a href="http://www.giko.it" id="giko">Sito realizzato da Giko</a>
</body>
</html>
    