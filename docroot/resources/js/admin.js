$(function(){

    var Video = Backbone.Model.extend({
        defaults: function() {
            return {
                id: null,
                name: "New video",
                publish: 0,
                code: '',
                description: '',
                type: '',
                category: '',
                mage: '',
                round: '',
                terrain: '',
                country: '',
                city: '',
                date: ''
            };
        },
        check: function() {
            var res = '';
            if (this.get('code') == '') res += 'Insert YouTube code';
            if (listaVideo.where({code:this.get('code')}).length > 1) res += 'YouTube code duplicated';
            if (this.get('name') == '') res += 'Insert video title';
            return (res == '') ? true : res;
        },
        publish: function() {
            var check = this.check();
            if (check == true) {
                this.set('publish', 1);
                this.save();
                return true;
            } else {
                return false;
            }
        },
        unpublish: function() {
            this.set('publish', 0);
            this.save();
            return this;
        }
    });


    var Log = Backbone.Model.extend({
        defaults: function() {
            return {
                id: null,
                entry: ''
            };
        },
        url: '/api/log'
    });

    var ListaVideo = Backbone.Collection.extend({
        model: Video,
        url: '/api/video',
        initialize: function() {
            new VideoListView;
            this.bind('add', this.addOne, this);
            this.bind('reset', this.addAll, this);
            this.fetch();
        },
        addOne: function(Video) {
            var view = new VideoView({model: Video});
            $("#videoList").append(view.render().el);
            return view;
        },
        comparator: function(video) {
          return video.get("name");
        },
        addAll: function() {
            this.each(this.addOne);
        },
        search: function(what) {
            return this.each(function(video){
                var found = 0;
                needles = what.split(' ');
                for (var i = needles.length - 1; i >= 0; i--) {
                    re = new RegExp(needles[i], 'i');
                    if (video.get('name').search(re) >= 0) {
                        found++;
                    }
                };
                if (found == needles.length) {
                    video.trigger('show');
                } else {
                    video.trigger('hide');
                }
            });
        }
    });

    var VideoView = Backbone.View.extend({
        tagName: "tr",
        template: _.template($('#video-template').html()),
        initialize: function() {
            this.model.bind('detail', this.detail, this);
            this.model.bind('change', this.render, this);
            this.model.bind('destroy', this.delete, this);
            this.model.bind('hide', this.hide, this);
            this.model.bind('show', this.show, this);
        },
        render: function() {
            this.$el.html(this.template(this.model.toJSON()));
            return this;
        },
        events: {
            "click td": "detail"
        },
        detail: function(){
            if (!detailsView.modifing) {
                $('#videoList .info').removeClass('info');
                this.$el.addClass('info');
                detailsView.model = this.model;
                detailsView.render();
            }
        },
        show: function() {
            this.$el.show();
        },
        delete: function() {
            this.$el.remove();
        },
        hide: function() {
            this.$el.hide();
            this.$el.find('input[type=checkbox]').removeAttr('checked');
        }

    });

    var VideoListView = Backbone.View.extend({
        el: $("#videoCol"),
        events: {
            "keyup .search-query": "search",
            "click #nuovoVideo": "newVideo",
            "click #publishSelected": "publish",
            "click #unpublishSelected": "unpublish",
            "click #deleteSelected": "delete"
        },
        initialize: function() {
            detailsView = new VideoDetailsView();
        },
        search: function(e) {
            var text = $(e.target).val();
            // if (e.keyCode != 13) return;
            // if (!text) return;

            listaVideo.search(text);
        },
        newVideo: function() {
            var nuovo = new Video;
            var v = listaVideo.create(nuovo.toJSON());
            v.trigger('detail');
            v.trigger('hide');
            detailsView.mod();
        },
        publish: function(e) {
            e.preventDefault();
            $(this.el).find('#videoList input[type=checkbox]:checked').each(function() {
                listaVideo.where({code: $(this).val()})[0].publish();
            });
            $('#checkAll').removeAttr('checked');
        },
        unpublish: function(e) {
            e.preventDefault();
            $(this.el).find('#videoList input[type=checkbox]:checked').each(function() {
                listaVideo.where({code: $(this).val()})[0].unpublish();
            });
            $('#checkAll').removeAttr('checked');
        },
        delete: function(e) {
            e.preventDefault();
            $(this.el).find('#videoList input[type=checkbox]:checked').each(function() {
                var video = listaVideo.where({code: $(this).val()})[0];
                if (video.get('publish') == 0) {
                    video.destroy();
                }
            });
            $('#checkAll').removeAttr('checked');
        }
    });
    
    var VideoDetailsView = Backbone.View.extend({
        el: $("#videoDetails"),
        template: _.template($('#video-details-template').html()),
        modifing: false,
        events: {
            "click .modifyVideo": "mod",
            "click .saveVideo": "save",
            "click .undoVideo": "undo",
            "click .publishVideo": "publish",
            "click .unpublishVideo": "unpublish",
            "click .deleteVideo": "delete"
        },
        initialize: function() {
            $(this.el).empty();
        },
        render: function() {
            this.$el.html(this.template(this.model.toJSON()));
            this.$el.find('input, textarea, select').attr('disabled', 'disabled');
            this.$el.find('.form-actions.read').show();
            this.$el.find('.form-actions.modify').hide();
            this.$el.find('.datepicker').datepicker({format: 'yyyy-mm-dd', weekStart: 1});
            return this;
        },
        undo: function() {
            var check = this.model.check();
            if (check != true) {
                this.$el.empty();
                this.model.destroy();
            } else {
                this.render();
            }
            this.modifing = false;
            return this;
        },
        mod: function() {
            this.$el.find('input, textarea, select').removeAttr('disabled');
            this.$el.find('.form-actions.read').hide();
            this.$el.find('.form-actions.modify').show();
            this.modifing = true;
            return this;
        },
        delete: function() {
            if (confirm("Sei sicuro di voler eliminare il video " + this.model.get('name') + "?")) {
                new Log({entry: 'eliminato video ' + this.model.id}).save();
                this.$el.empty();
                this.model.destroy();
            }
            this.modifing = false;
            return this;
        },
        publish: function() {
            if (this.model.publish() == true) {
                this.render();
            } else {
                this.$el.find('.alert').remove();
                this.$el.find('.form-horizontal').after('<div class="alert alert-error">' + check + '</div>');
            }
            return this;
        },
        unpublish: function() {
            this.model.unpublish();
            this.render();
            return this;
        },
        save: function() {
            var formData = this.$el.find('form').serializeArray();
            for (var i = formData.length - 1; i >= 0; i--) {
                this.model.set(formData[i].name, formData[i].value);
            };
            var check = this.model.check();
            if (check == true) {
                new Log({entry: 'modificato video ' + this.model.id}).save();
                this.model.save();
                this.model.trigger('show');
                this.modifing = false;
                this.render();
            } else {
                this.$el.find('.alert').remove();
                this.$el.find('.form-horizontal').after('<div class="alert alert-error">' + check + '</div>');
            }
            return this;
        }
    });
    
    var listaVideo = new ListaVideo;


    $(document).ready(function(){

        $('#checkAll').click(function(){
            if ($(this).is(':checked')) {
                $('#videoList input[type=checkbox]').attr('checked', 'checked');
            } else {
                $('#videoList input[type=checkbox]').removeAttr('checked');
            }
        });

    });

});
