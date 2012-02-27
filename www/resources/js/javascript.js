
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
        if (main_feed != undefined) {
            loadFeed(main_feed, '#search_result .items', 'full', 6);
        }
        loadFeed('https://gdata.youtube.com/feeds/api/users/'+channel+'/uploads?alt=jsonc&v=2&orderby=viewCount&max-results=6', '#most_viewed ul', 'list_views');
        loadFeed('https://gdata.youtube.com/feeds/api/users/'+channel+'/uploads?alt=jsonc&v=2&orderby=rating&max-results=6', '#top ul', 'list');

        $('#search_form').submit(function() {
            var query = $('#search_text').val();
            document.location.href = '?q='+encodeURIComponent(query);
            return false;
        });

        if (video_data != undefined) {
            $.ajax({
                url: video_data,
                dataType: 'json',
                success: function(data) {
                    $('#descrizione').html(data.data.description.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2')).show();
                }
            });
        }
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
                data = data.data;
                if (data.totalItems == 0) {
                    container.html('').append('No results');
                } else {
                    if (videoPerPage > 0) {
                        var pages = $('.items .page');
                        if (pages.length == 0) {
                            for (i = 0, l = Math.ceil(data.totalItems / videoPerPage); i < l; i++) {
                                var start = i * videoPerPage + 1;
                                if (start != 1)
                                    container.append('<div class="page" rel="' + start + '"></div>');
                                else
                                    container.append('<div class="page"></div>');
                            }
                        }
                        pages = $('.items .page');
                        var offset = data.startIndex;
                        for(i = 0, l = data.items.length; i < l; i++) {
                            var page = Math.floor((i + offset - 1) / videoPerPage);
                            $(pages[page]).append(_formatVideo(data.items[i], style));
                        }
                    } else {
                        for(i = 1, l = data.items.length; i < l + 1; i++) {
                            container.append(_formatVideo(data.items[i - 1], style));
                        }
                    }
                    container.find('.loading').remove();
                    if (container.selector == '#search_result .items' && container.find('.page').length > 1) {
                        $("#search_result").navigator();
                    }
                    if ($('#titolo h2').html() == '') {
                        // $('#titolo h2').html(data.feed.title.$t);
                    }
                }
            },
            error: function() {
                container.html('').append('Error');
            }
        });
    }

    function _formatVideo(video, style) {
        if (video.video)
            video = video.video;
        
        id = video.id;
        thumb = video.thumbnail.sqDefault;
        views = (video.viewCount) ? video.viewCount : 0;
        title = video.title;

        if (style == 'full')
            return '<a href="/?v=' + id + '" class="video"><div class="thumb"><img src="' + thumb + '" /></div><p>' + title + '</p></a>';
        if (style == 'list')
            return '<li><a href="/?v=' + id + '">' + title + '</a></li>';
        if (style == 'list_views')
            return '<li><a href="/?v=' + id + '">' + title + '<span>'+views+'</span></a></li>';
    }





