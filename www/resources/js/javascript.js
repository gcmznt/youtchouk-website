
    $(document).ready(function() {

        $("#search_result").append('<div class="navi"></div>');
        $("#search_result").scrollable({
            onBeforeSeek: function(e, p) {
                var pages = $('.items .page');
                for (i = p, f = p + 1; i <= f; i++) {
                    var page = $(pages[i]);
                    var rel = page.attr('rel');
                    if (typeof rel !== 'undefined' && rel !== false) {
                        page.removeAttr('rel');
                        loadFeed(main_feed+'&start-index='+rel, '#search_result .items', 'full', 6);
                    }
                }
            }
        });
        loadFeed(main_feed, '#search_result .items', 'full', 6);
        loadFeed('https://gdata.youtube.com/feeds/api/users/'+channel+'/uploads?alt=json&orderby=viewCount&max-results=6', '#most_viewed ul', 'list_views');
        loadFeed('https://gdata.youtube.com/feeds/api/users/'+channel+'/uploads?alt=json&orderby=rating&max-results=6', '#top ul', 'list');

        $('#search_form').submit(function() {
            var query = $('#search_text').val();
            document.location.href = '?q='+encodeURIComponent(query);
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
            dataType: 'json',
            success: function(data) {
                if (data.feed.openSearch$totalResults.$t == 0) {
                    container.html('').append('No results');
                } else {
                    if (videoPerPage > 0) {
                        var pages = $('.items .page');
                        if (pages.length == 0) {
                            for (i = 0, l = Math.ceil(data.feed.openSearch$totalResults.$t / videoPerPage); i < l; i++) {
                                var start = i * videoPerPage + 1;
                                if (start != 1)
                                    container.append('<div class="page" rel="' + start + '"></div>');
                                else
                                    container.append('<div class="page"></div>');
                            }
                        }
                        pages = $('.items .page');
                        var offset = data.feed.openSearch$startIndex.$t;
                        for(i = 0, l = data.feed.entry.length; i < l; i++) {
                            var page = Math.floor((i + offset - 1) / videoPerPage);
                            $(pages[page]).append(_formatVideo(data.feed.entry[i], style));
                        }
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
            },
            error: function() {
                container.html('').append('Error');
            }
        });
    }

    function _formatVideo(video, style) {
        id = video.id.$t.substr(video.id.$t.lastIndexOf('/')+1);
        thumb = video.media$group.media$thumbnail[0].url;
        views = (video.yt$statistics) ? video.yt$statistics.viewCount : 0;
        title = video.title.$t;
        rate = (video.gd$rating) ? video.gd$rating.average : 0;

        if (style == 'full')
            return '<a href="/?v=' + id + '" class="video"><img src="' + thumb + '" /><p>' + title + '</p></a>';
        if (style == 'list')
            return '<li><a href="/?v=' + id + '">' + title + '</a></li>';
        if (style == 'list_views')
            return '<li><a href="/?v=' + id + '">' + title + '<span>'+views+'</span></a></li>';
        if (style == 'list_rate')
            return '<li><a href="/?v=' + id + '">'+rate+' &bull; ' + title + '</a></li>';
    }





