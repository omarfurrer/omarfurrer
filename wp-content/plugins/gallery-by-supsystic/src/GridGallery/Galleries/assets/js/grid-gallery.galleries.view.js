/*global jQuery*/

(function (app, $) {

    function Controller() {
        this.isChecked = false;
        this.checked = [];
        this.galleryId = this.getParameterByName('gallery_id');
        this.$checkboxes = $('[data-observable]');

        this.init();
        this.allowRemove(false);
    }

    Controller.prototype.init = function () {
        this.$checkboxes.on('click', $.proxy(function (event) {

            var checked = false, checkboxes = [];

            $.each(this.$checkboxes, function (index, checkbox) {
                if (checkbox.checked) {
                    checked = true;
                    checkboxes.push(
                        app.Common.getParentEntity($(checkbox))
                    );
                }
            });

            this.isChecked = checked;
            this.checked = checkboxes;
            this.allowRemove(checked);

        }, this));
    };

    Controller.prototype.getParameterByName = function (name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");

        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    };

    Controller.prototype.allowRemove = function (state) {
        var $btn = $('[data-button="remove"]');

        $btn.attr('disabled', 'disabled');

        if (state) {
            $btn.removeAttr('disabled');
        }
    };

    Controller.prototype.filterImages = function() {
        var $imagesTr = $('#the-list').children('tr'),
            filterValue = $('#find-by-caption').val();

        $imagesTr.each(function(){
            var $captionField = $(this).find('input[name=caption]')
            if($captionField.val().indexOf(filterValue)!=-1
                || $(this).data('entity-info').attachment.title.indexOf(filterValue)!=-1
            ){
                $(this).show();
            }else{
                $(this).hide();
            }
        });
    };

    Controller.prototype.allImageTags = function () {
        var identifiers = [],
            tag = $("[name=catactions] option:selected"),
            type = $("[name=bulkactions] option:selected").val(),
            newcat = $("[name=newTag]").val(),
            post = app.Ajax.Post({
                module: 'galleries',
                action: 'allImageTags'
            }, { gallery_id: this.galleryId });

        $.each(this.checked, function (index, $entity) {
            identifiers.push($entity.data('entity-id'));
        });

        if(identifiers.length > 0){
            post.add('type', type);
            post.add('ids', identifiers);
            if(newcat.length > 0){
                post.add('tag', newcat);
            } else {
                if(tag.val() == 'allcat'){
                    post.add('tag', 'allcat');
                } else {
                    post.add('tag', tag.text());
                } 
            }

            app.Loader.show('Update image category...');
            post.send(function (response) {
                $.jGrowl('Category update.');
                app.Loader.hide();
                location.reload(true);
            });
        } else {
            $.jGrowl('Select images.');
        }
    }

    Controller.prototype.bulkActions = function () {
        var imageTags = $("[name=catactions]");
        var newTagsInput = $("[name=newTag]");
        var bulkactions = $("[name=bulkactions]");
        if(bulkactions.val() == 'newcat'){
            imageTags.hide();
            newTagsInput.show();
        } else {
            newTagsInput.val('');
            newTagsInput.hide();
            imageTags.show();
            imageTags.find("option:last-child").hide();
        }
        if(bulkactions.val() != "add"){
            imageTags.find("option:last-child").show();
        }
    }

    Controller.prototype.removePhoto = function () {
        var identifiers = [],
            entities = [],
            post = app.Ajax.Post({
                module: 'galleries',
                action: 'deleteResource'
            }, { gallery_id: this.galleryId });

        app.Loader.show('Deleting...');

        $.each(this.checked, function (index, $entity) {
            identifiers.push($entity.data('entity-id'));
            entities.push($entity);
        });

        post.add('ids', identifiers);
        post.send(function (response) {
            if (!response.error) {
                $.each(entities, function (index, $entity) {
                    $entity.remove();
                });
            }

            $.jGrowl(response.message);
            app.Loader.hide();
        });
    };

    Controller.prototype.toggleCheckbox = (function (e) {
        //e.preventDefault();

        //var $button = $(e.currentTarget);

        if (this.checked.length >= 0 && this.checked.length != this.$checkboxes.length) {
            this.$checkboxes.each($.proxy(function (index, element) {
                var $element = $(element);

                if (!$element.is(':checked')) {
                    $element
                        .trigger('click')
                        .attr('checked', 'checked')
                        .iCheck('update');
                }
            }, this));

            //$button.html('<i class="fa fa-fw fa-times"></i> Uncheck all');
        } else if (this.checked.length == this.$checkboxes.length) {
            this.$checkboxes.each($.proxy(function (index, element) {
                var $element = $(element);

                if ($element.is(':checked')) {
                    $element
                        .trigger('click')
                        .removeAttr('checked')
                        .iCheck('update');
                }
            }, this));

            //$button.html('<i class="fa fa-fw fa-check"></i> Check all');
        }
    });

    Controller.prototype.handleEmptyImages = function () {
        var request = app.Ajax.Post({
            module: 'photos',
            action: 'isEmpty'
        }),
            controller = this,
            uploader;

        request.send(function (response) {
            if (!response.isEmpty) {
                return;
            }

            uploader = window.wp.media.frames.file_frame = window.wp.media({
                title:    'Choose images',
                button:   {
                    text: 'Choose images'
                },
                multiple: true
            });

            uploader.on('select', function () {
                var attachments = uploader.state().get('selection').toJSON(),
                    $container  = $('[data-container]'),
                    folderName  = $('#gg-breadcrumbs').find('a').last().text();

                this.request = app.Ajax.Post({
                    module: 'photos',
                    action: 'addFolder'
                });

                this.request.add('folder_name', folderName);
                this.request.add('view_type', $container.data('container'));
                this.request.send(function (response) {
                    var folderId = response.id;

                    if (response.error) {
                        $.jGrowl('Failed to create new folder ' + folderName);
                        return;
                    }

                    $.each(attachments, function (index, attachment) {
                        this.request = app.Ajax.Post({
                            module: 'photos',
                            action: 'add'
                        });

                        this.request.add('attachment_id', attachment.id);
                        this.request.add('folder_id', folderId);
                        this.request.add('view_type', $container.data('container'));

                        this.request.send(function (response) {
                            if (response.error) {
                                $.jGrowl('Failed to import images.');
                                return;
                            }

                            $.jGrowl(response.message);
                        });
                    });

                    this.request = app.Ajax.Post({
                        module: 'galleries',
                        action: 'attach'
                    });
                    this.request.add('gallery_id', controller.getParameterByName('gallery_id'));
                    this.request.add('resources', [{ type: 'folder', id: folderId }]);

                    this.request.send(function (response) {
                        if (!response.error) {
                            window.location.reload(true);
                        }

                        $.jGrowl(response.message);
                    });

                    return;
                });
            });

            $(document).ready(function () {
                $('#addImg').on('click', function (e) {
                    e.preventDefault();
                    uploader.open();
                });
            });
        });
    };

    Controller.prototype.sortBy = function() {
        var identifiers = [],
            sortby = $("[name=sortby]").find("option:selected"),
            sortTo = $("[name=sortto]").find("option:selected");
       
        post = app.Ajax.Post({
            module: 'galleries',
            action: 'saveSortBy'
        }, { gallery_id: this.galleryId, sortby: sortby.val(), sortto: sortTo.val() });

        app.Loader.show('Loading...');
        post.send(function (response) {
            app.Loader.hide();
            $.jGrowl(response.message);
            location.reload(true);
        });
    }

    $(document).ready(function () {
        var queryString = new URI().query(true), controller;

        if (queryString.module === 'galleries'
            && (queryString.action === 'view' || queryString.action === 'addImages')
        ) {
            controller = new Controller();

            $('[data-button="remove"]')
                .on('click', $.proxy(controller.removePhoto, controller));

            $('[data-button="allimagetags"]')
                .on('click', $.proxy(controller.allImageTags, controller));

            $('[data-button="filterimages"]')
                .on('click', $.proxy(controller.filterImages, controller));
            $('#find-by-caption').keyup(function(event){
                controller.filterImages();
            });

            $('[name=bulkactions]')
                .on('change', $.proxy(controller.bulkActions, controller));

            /*$('[data-button="checkAll"]')
                .on('click', $.proxy(controller.toggleCheckbox, controller));*/

            $('input#checkAll')
                .on('click', $.proxy(controller.toggleCheckbox, controller));

            $('[data-button="sortbtn"]')
                .on('click', $.proxy(controller.sortBy, controller));

            $('select[name="sortby"]').on('change', function() {
                var $sortTo = $("#sortToLi");
                if($('select[name="sortby"] option:selected').text() === 'Randomly') {
                    $sortTo.hide();
                } else {
                    $sortTo.show();
                }
            });

            controller.handleEmptyImages();
        }
    });

}(window.SupsysticGallery = window.SupsysticGallery || {}, jQuery));
