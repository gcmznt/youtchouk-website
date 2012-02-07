
    $(document).ready(function() {

        $("#search_result").append('<div class="navi"></div>');
        $("#search_result").scrollable();
        loadFeed(main_feed, '#search_result .items', 'full', 6);
        loadFeed('https://gdata.youtube.com/feeds/api/users/tchoukballpromotion/uploads?alt=json&orderby=viewCount&max-results=6', '#most_viewed ul', 'list_views');
        loadFeed('https://gdata.youtube.com/feeds/api/users/tchoukballpromotion/uploads?alt=json&orderby=rating&max-results=6', '#top ul', 'list');

        $('#search_form').submit(function() {
            var query = $('#search_text').val();
            document.location.href = '?q='+query;
            return false;
        });
    });

    function loadFeed(feed, domId, style, videoPerPage) {
        videoPerPage = videoPerPage || 0;

        if($(domId).length === 0)
            return;

        var container = $(domId);

        $.ajax({
            url: feed,
            dataType: 'jsonp',
            success: function(data) {
                if (data.feed.openSearch$totalResults.$t == 0) {
                    container.html('').append('No results');
                } else {
                    if (videoPerPage > 0) {
                        var result = $('');
                        var pageTpl = $('<div class="page"></div>');
                        var page = pageTpl.clone();
                        for(i = 1, l = data.feed.entry.length; i < l + 1; i++) {
                            page.append(_formatVideo(data.feed.entry[i - 1], style));
                            if((i % videoPerPage) === 0) {
                                container.append(page);
                                page = pageTpl.clone();
                            }
                        }
                        if (page.html() != '')
                            container.append(page);
                    } else {
                        for(i = 1, l = data.feed.entry.length; i < l + 1; i++) {
                            container.append(_formatVideo(data.feed.entry[i - 1], style));
                        }
                    }
                    container.find('.loading').remove();
                    if (container.selector == '#search_result .items' && container.find('.page').length > 1) {
                        $("#search_result").navigator();
                    }
                }
            }
        });
    }

    function _formatVideo(video, style) {
        id = video.id.$t.substr(video.id.$t.lastIndexOf('/')+1);
        thumb = video.media$group.media$thumbnail[0].url;
        views = video.yt$statistics.viewCount;
        title = video.title.$t;
        rate = (video.gd$rating) ? video.gd$rating.average : 0;

        if (style == 'full')
            return '<div class="video"><a href="/?v=' + id + '"><img src="' + thumb + '" /></a><p>' + title + '</p></div>';
        if (style == 'list')
            return '<li><a href="/?v=' + id + '">' + title + '</a></li>';
        if (style == 'list_views')
            return '<li><a href="/?v=' + id + '">'+views+' &bull; ' + title + '</a></li>';
        if (style == 'list_rate')
            return '<li><a href="/?v=' + id + '">'+rate+' &bull; ' + title + '</a></li>';
    }





