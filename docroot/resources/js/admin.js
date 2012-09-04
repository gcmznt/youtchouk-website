$(function(){

    var Video = Backbone.Model.extend({
        defaults: function() {
            return {
                visible: true
            };
        }
    });

    var ListaVideo = Backbone.Collection.extend({
        model: Video,
        url: 'video.json',
        initialize: function() {
            new VideoListView;
            this.bind('reset', this.addAll, this);
            this.fetch();
        },
        addOne: function(Video) {
            var view = new VideoView({model: Video});
            this.$("#videoList").append(view.render().el);
        },
        addAll: function() {
            this.each(this.addOne);
        },
        search: function(what) {
            return this.each(function(video){
                if (video.get('name') == what) {
                    video.set({'visible': true});
                } else {
                    video.set({'visible': false});
                }
            });
        }
    });

    var VideoView = Backbone.View.extend({
        tagName: "li",
        template: _.template($('#video-template').html()),
        initialize: function() {
            this.model.bind('change', this.render, this);
        },
        render: function() {
            this.$el.html(this.template(this.model.toJSON()));
            if (this.model.get('visible')) {
                this.$el.show();
            } else {
                this.$el.hide();
            }
            return this;
        },
        events: {
            "click": "show"
        },
        show: function(video){
            var details = new VideoDetailsView({model: this.model});
            $('#videoList .active').removeClass('active');
            this.$el.addClass('active');
            details.render();
        }

    });

    var VideoListView = Backbone.View.extend({
        el: $("#videoCol"),
        events: {
            "keyup .search-query": "search"
        },
        search: function(e) {
            var text = $(e.target).val();
            // if (e.keyCode != 13) return;
            // if (!text) return;

            listaVideo.search(text);
        }
    });
    
    var VideoDetailsView = Backbone.View.extend({
        el: $("#videoDetails"),
        template: _.template($('#video-details-template').html()),
        render: function() {
            this.$el.html(this.template(this.model.toJSON()));
            return this;
        }
    });
    
    var listaVideo = new ListaVideo;




    // var AppView = Backbone.View.extend({
    //     el: $("#mainContainer"),

    //     initialize: function() {
    //         // listaVideo.bind('add', this.addOne, this);
    //         // listaVideo.bind('all', this.render, this);
    //     },
    // });

    // filtro = listaVideo.filter(function(video){ return console.log(video.get('nome')); video.get('nome') == 'Video 1'; });
    // filtro = listaVideo.filter({
    //     'nome': 'Video 1'
    // });
    // console.log(filtro);

    // var App = new AppView;

});
