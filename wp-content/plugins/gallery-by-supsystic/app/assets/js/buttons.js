(function($) {

    var dialogClass = '.gallery-select-dialog',
        initDialog = function() {
            var $container = $('body');

            $container.append(
                '<div class="gallery-select-dialog" hidden>' +
                    '<h3>Select gallery to insert</h3>' +
                    '<select></select>' +
                    '<button class="button button-primary">Select</button>' +
                '</div>');

            $.post(wp.ajax.settings.url, {
                    action: 'grid-gallery',
                    _wpnonce: SupsysticGallery.nonce,
                    route:  {
                        module: 'galleries',
                        action: 'list'
                    }
                }, function(response) {
                    $.each(response.galleries, function(index, value) {
                        $container.find(dialogClass + ' select').append(
                            '<option value="' + value.id + '">' + value.title + '</option>'
                        );
                    });
                }
            );
        },
        dialogIsInit = false;

    tinymce.create('tinymce.plugins.addShortcode', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            ed.addButton('addShortcode', {
                title : 'Add Gallery',
                cmd : 'addShortcode',
                image : url + '/img/logo_gallery.png'
            });

            ed.addCommand('addShortcode', function() {

                if (!dialogIsInit) {
                    initDialog();
                    dialogIsInit = true;
                }

                var $dialog = $(dialogClass).bPopup({
                    onClose: function() {
                        $(dialogClass + ' button').off('click');
                    }
                }, function() {
                    $(dialogClass + ' button').on('click', function() {
                        var selected = $(dialogClass).find('select').val(),
                            text = '[supsystic-gallery id=' + selected + ' position=center]';
                        ed.execCommand('mceInsertContent', 0, text);
                        $dialog.close();
                    });
                });
            });
        },

        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'Gallery by Supsystic buttons',
                author : 'Dmitriy Smus',
                infourl : 'http://supsystic.com',
                version : "0.1"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add( 'addShortcode', tinymce.plugins.addShortcode );

})(jQuery);