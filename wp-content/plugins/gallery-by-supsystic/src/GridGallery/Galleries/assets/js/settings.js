/*global jQuery*/
(function (app, $) {

    function Controller() {
        this.$container = $('.form-tabs');
        this.tabs = this.getAvailableTabs();
        this.$currentTab = null;
        this.$currentTarget = null;

        this.strToBool = function(value) {
            if(value == 'true') {
                return true;
            } else {
                return false;
            }
        }

        this.init();
    }

    Controller.prototype.init = function () {
        var lastTab = this.getCookie('lastTab');

        if (!lastTab) {
            this.$currentTab = this.tabs[Object.keys(this.tabs)[0]];
            this.$currentTarget = $('.change-tab').first();
        } else {
            this.$currentTarget = $('.change-tab[href="' + lastTab + '"]');
            this.$currentTab = $('[data-tab="' + lastTab + '"]');
        }

        this.hideTabs();
        this.$currentTab.fadeIn();
        this.$currentTarget.addClass('active');
    };

    Controller.prototype.getParameterByName = function (name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");

        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    };

    Controller.prototype.getCookie = function (name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : null;
    };

    Controller.prototype.setCookie = function (name, value) {
        document.cookie = name + '=' + encodeURIComponent(value);
    }

    Controller.prototype.getAvailableTabs = function () {
        var tabs = {};

        $.each($('[data-tab]'), function (index, tab) {
            tabs[$(tab).data('tab')] = $(tab);
        });

        return tabs;
    };

    Controller.prototype.hideTabs = function () {
        $.each(this.tabs, function () { this.hide() });
    };

    Controller.prototype.changeTab = function (event) {
        event.preventDefault();

        this.hideTabs();

        this.$currentTarget.removeClass('active');

        this.$currentTarget = $(event.currentTarget);
        this.$currentTarget.addClass('active');

        this.$currentTab = this.tabs[this.$currentTarget.attr('href')];
        this.$currentTab.show();

        this.setCookie('lastTab', this.$currentTarget.attr('href'));
    };

    Controller.prototype.remove = function (event) {
        if (!confirm('Are you sure?')) {
            event.preventDefault();
        }
    };

    Controller.prototype.saveButton = function() {
        $('#btnSave').on('click', function() {
            document.forms['form-settings'].submit();
        });
    }

    Controller.prototype.setInputColor = (function() {
        $('input[type="color"]').each(function() {
            if(navigator.userAgent.match(/Trident\/7\./)) {
                $(this).css('background-color', $(this).val());
            }
        });
    });

    // ICONS
    Controller.prototype.initIconsDialog = function () {

        var $dialog = $('#iconsPreview').dialog({
            autoOpen: false,
            buttons:  {
                Cancel: function () {
                    $(this).dialog('close');
                }
            },
            modal:    true,
            width:    645
        });

        $('#selectIconsEffect').on('click', function(event) {
            event.preventDefault();
            $dialog.dialog('open');
        });

        $('#iconsPreview .hi-icon').on('click', function(event) {
            event.preventDefault();
            var effectName = $(event.currentTarget).data('effect');
            $dialog.dialog('close');
            $('#iconsEffectNameText').text(effectName);
            $('#iconsEffectName').val(effectName);
        });

        // $('#iconsOverlay').on('change', $.proxy(Ctrl.toggleOverlay, Ctrl)).trigger('change');
    };

    Controller.prototype.toggleSlideShow = function () {
        var $options = $('[name="box[slideshowSpeed]"], [name="box[slideshowAuto]"], [name="box[popupHoverStop]"]'),
            $slideshow = $('[name="box[slideshow]"]');

        if($slideshow.val() == 'true'){
            $options.parents('tr').show();
        } else {
            $options.parents('tr').hide();
        }

        $slideshow.on('change', function(){
            if($(this).val() == 'true') {
                $options.parents('tr').show();
            } else {
                $options.parents('tr').hide();
            }
        })
    };

    Controller.prototype.togglePopupTheme = function (value) {
        var $boxType = $('[name="box[type]"]');

        $boxType.attr('value', '0');

        if(value == 'theme_6') {
            $boxType.attr('value', '1');
        }
        if(value == 'theme_1_pro') {
            $boxType.attr('value', '2');
        }
    };

    Controller.prototype.removePresetRequest = function () {
        var presetId = $('#presetId').val(),
            request = app.Ajax.Post({
                module: 'galleries',
                action: 'removePreset'
        });

        request.add('preset_id', presetId);

        return request;
    };

    Controller.prototype.initSaveDialog = function () {
        $('#saveDialog').dialog({
            width:    350,
            autoOpen: false,
            modal:    true,
            buttons:  {
                Save:   function () {
                    var $settingsId  = $('#settingsId'),
                        $presetTitle = $('#presetTitle'),
                        request = app.Ajax.Post(
                            {
                                module: 'galleries',
                                action: 'savePreset'
                            }
                        );

                    // Close the dialog and show error if the settings isn't yet saved to the database.
                    if ($settingsId.val() === 'undefined') {
                        $.jGrowl('You must to save the settings first.');
                        $(this).dialog('close');
                    }

                    request.add('settings_id', $settingsId.val());
                    request.add('title', $presetTitle.val());

                    request.send($.proxy(function (response) {
                        if (response.message) {
                            $.jGrowl(response.message);
                        }

                        if (!response.error) {
                            window.location.reload(true);
                        }

                        $(this).dialog('close');
                    }, this));
                },
                Cancel: function () {
                    $(this).dialog('close');
                }
            }
        });
    };

    Controller.prototype.openSaveDialog = function () {
        $('#saveDialog').dialog('open');
    };

    Controller.prototype.initDeleteDialog = function () {
        var controller = this;

        $('#deletePreset').dialog({
            width:    350,
            autoOpen: false,
            modal:    true,
            buttons:  {
                Delete: function () {
                    var request = controller.removePresetRequest();

                    request.send($.proxy(function (response) {
                        if (response.message) {
                            $.jGrowl(response.message);
                        }

                        if (!response.error) {
                            window.location.reload(true);
                        }

                        $(this).dialog('close');
                    }, this));
                },
                Cancel: function () {
                    $(this).dialog('close');
                }
            }
        });
    };

    Controller.prototype.openDeleteDialog = function () {
        $('#deletePreset').dialog('open');
    };

    Controller.prototype.initLoadDialog = function () {
        var controller = this;

        $("#loadPreset").dialog({
            width:    350,
            autoOpen: false,
            modal:    true,
            buttons:  {
                Cancel: function () {
                    $(this).dialog('close');
                },
                Load: function () {
                    var galleryId,
                        presetId,
                        $presetsList = $('#presetList'),
                        request = app.Ajax.Post({
                            module: 'galleries',
                            action: 'applyPreset'
                        });

                    // Get gallery Id from the browser's query string.
                    galleryId = parseInt(controller.getParameterByName('gallery_id'), 10);

                    // Get preset id from the preset list.
                    presetId = parseInt($presetsList.val(), 10);

                    request.add('gallery_id', galleryId);
                    request.add('preset_id', presetId);

                    request.send($.proxy(function (response) {
                        if (response.message) {
                            $.jGrowl(response.message);
                        }

                        if (!response.error) {
                            $(this).dialog('close');
                            window.location.reload(true);
                        }
                    }, this));
                }
            },
            open: function () {
                var request = app.Ajax.Post({
                    module: 'galleries',
                    action: 'getPresetList'
                });

                request.send(function (response) {
                    var $presetList = $('#presetList'),
                        $errors = $('#presetListError');

                    if (response.error) {
                        $presetList.attr('disabled', 'disabled');
                        $errors.find('#presetLoadingFailed').show();
                        return;
                    }

                    if (response.presets.length < 0) {
                        $presetList.attr('disabled', 'disabled');
                        $errors.find('#presetEmpty').show();
                        return;
                    }

                    $.each(response.presets, function (index, preset) {
                        $presetList.append('<option value="'+preset.id+'">'+preset.title+'</option>');
                    });
                });
            }
        });
    };

    Controller.prototype.openPresetDialog = function () {
        $('#loadPreset').dialog('open');
    };

    Controller.prototype.removePresetFromList = function () {
        var request = this.removePresetRequest();

        request.send(function (response) {
            if (response.error) {
                return false;
            }

            $('#presetId').find(':selected').remove();
        });
    };

    Controller.prototype.initNoticeDialog = function() {
        $('#reviewNotice').dialog({
            modal:    true,
            width:    600,
            autoOpen: true
        });
    };

    Controller.prototype.showReviewNotice = function() {
        var self = this;

        $.post(window.wp.ajax.settings.url,
            {
                action: 'grid-gallery',
                _wpnonce: SupsysticGallery.nonce,
                route: {
                    module: 'galleries',
                    action: 'checkReviewNotice'
                }
            })
            .success(function (response) {

                if(response.show) {
                    self.initNoticeDialog();

                    $.merge($('#reviewNotice [data-statistic-code]').closest('button'),
                        $('#reviewNotice').prev().find('button')).on('click', function() {

                        $.post(window.wp.ajax.settings.url, {
                                action: 'grid-gallery',
                                _wpnonce: SupsysticGallery.nonce,
                                route: {
                                    module: 'galleries',
                                    action: 'checkNoticeButton'
                                }
                            })
                            .success(function(response) {
                                $('#reviewNotice').dialog('close');
                            });
                    });
                }
            });
    };

    Controller.prototype.initThemeDialog = function () {
        $('#themeDialog').dialog({
            autoOpen: false,
            modal:    true,
            width:    570,
            buttons:  {
                // Select: function () {
                //     var selected = $('#bigImageThemeSelect').val(),
                //         $theme = $('#bigImageTheme');
                //
                //     $theme.val(selected);
                //     $(this).dialog('close');
                // },
                Cancel: function () {
                    $(this).dialog('close');
                }
            }
        });

        Controller.prototype.initThemeSelect = function () {
            var $theme = $('#bigImageTheme'),
                self = this;

            $('.theme').on('click', function () {
                $theme.val($(this).data('val'));
                $('.themeName').text($(this).data('name'));
                self.togglePopupTheme($(this).data('val'));
                $('#themeDialog').dialog('close');
            });
        };

        Controller.prototype.initEffectsDialog = function () {
            $('#effectDialog').dialog({
                autoOpen: false,
                modal:    true,
                width:    740,
                buttons:  {
                    Cancel: function () {
                        $(this).dialog('close');
                    }
                }
            });
        };

        Controller.prototype.openEffectsDialog = function () {
            $('#effectDialog').dialog('open');
        };
    };

    Controller.prototype.setScroll = function() {
        var $settingsWrap = $('.settings-wrap');

        $settingsWrap.slimScroll({
            height: '700px',
            railVisible: true,
            alwaysVisible: true,
            allowPageScroll: true
        });
        var $preview = $('#preview.gallery-preview').css({
            'opacity': 1,
        }).hide();
        $settingsWrap.fadeIn();
        $preview.fadeIn();
    };

    Controller.prototype.initEffectPreview = function () {
        var $effect  = $('#overlayEffect'),
            $preview = $('#effectsPreview'),
            $dialog  = $('#effectDialog');

        $preview.find('figure').on('click', function (event) {
            event.preventDefault();

            $preview.find('figure').removeClass('selected');
            $(this).addClass('selected');

            if ($(this).data('type') == 'icons') {
                if (!confirm($(this).parent().data('confirm'))) {
                    return;
                }
                $('#icons-enable').iCheck('toggle').trigger('change');
            }

            $('.selectedEffectName').text($.proxy(function () {
                return this.find('span').text();
            }, $(this)));

            $('#preview [data-grid-gallery-type]')
                .data('grid-gallery-type', $(this).data('grid-gallery-type'));

            $('#preview.gallery-preview').trigger('preview.refresh');

            $effect.val($(this).data('grid-gallery-type'));

            if ($(this).data('grid-gallery-type') == 'polaroid') {
                $('#polaroid-effect select').val('true').trigger('change');
            }
            $dialog.dialog('close');
        });
        $preview.find('[data-grid-gallery-type="' + $effect.val() + '"]').addClass('selected');
    };

    Controller.prototype.openThemeDialog = function () {
        $('#themeDialog').dialog('open');
    };

    Controller.prototype.toggleArea = function() {
        var $toggle = $('[name="area[grid]"]'),
            $pagesRow = $('#usePages'),
            $optionsHeight = $('[name="area[photo_height]"]'),
            $optionsHeightRow = $optionsHeight.closest('tr'),
            $optionsWidth = $('[name="area[photo_width]"]'),
            $optionsWidthRow = $optionsWidth.closest('tr'),
            $columsRow = $('#generalColumnsRow');
            $responsiveColumnsRow = $('#responsive-columns').closest('tr');

        $toggle.on('change', function() {

            $optionsWidthRow.find('input, select').prop('disabled', false);
            $optionsHeightRow.find('input, select').prop('disabled', false);
            $optionsWidthRow.show();
            $optionsHeightRow.show();
            $columsRow.hide();
            $responsiveColumnsRow.hide();

            if (!$optionsHeight.val().length) {
                $optionsHeight.val($optionsWidth.val());
            }

            if (!$optionsWidth.val().length) {
                $optionsWidth.val($optionsHeight.val());
            };

            switch($(this).find('option:selected').val()) {
                // Fixed
                case '0':
                    $optionsWidthRow.find('option[name="percents"]').hide();
                    $optionsHeightRow.find('option[name="percents"]').hide();
                    break;
                // Vertical
                case '1':
                    if ($pagesRow.find('#showPages').is(':checked')) {
                        $pagesRow.find('#hidePages').attr('checked', 'check').trigger('change');
                        $pagesRow.find('input').attr('disabled', true);
                        $.jGrowl('Pagination disabled now');
                    } else {
                        $pagesRow.find('input').prop('disabled', false);
                    }
                    $optionsHeightRow.hide();
                    $optionsHeightRow.find('input, select').prop('disabled', true);
                    $optionsWidthRow.find('option[name="percents"]').show();
                    break;
                // Horizontal
                case '2':
                    $optionsWidthRow.hide();
                    $optionsHeightRow.find('option[name="percents"]').show();
                    $optionsWidthRow.find('input, select').prop('disabled', true);
                    break;
                // Fixed columns
                case '3':
                    $columsRow.show();
                    $responsiveColumnsRow.show();
                    $optionsWidthRow.find('option[name="percents"]').hide();
                    $optionsHeightRow.find('option[name="percents"]').hide();
                    break;
            }
        }).trigger('change');
    };

    Controller.prototype.toggleShadow = function () {
        var $shadowTable = $('[name="shadow"]'),
            value = 0;

        $('#showShadow').on('click', function () {
            $shadowTable.find('tbody').show();
        });

        $('#hideShadow').on('click', function () {
            $('#useMouseOverShadow').attr('value', 0);
            $('select[name="thumbnail[shadow][overlay]"]').attr('value', 0).trigger('change');
            $shadowTable.find('tbody').hide();
        });

        $shadowTable.find('thead input[type="radio"]:checked')
            .trigger('click')
            .trigger('change');
    };

    Controller.prototype.toggleBorder = function () {
        var $table = $('table[name="border"]'),
            $borderType =$('select[name="thumbnail[border][type]"]'),
            $toggleRow = $borderType.closest('tr'),
            value = 0;

        value = parseInt($toggleRow.val(), 10);

        $borderType.on('change', function () {
            if($(this).find('option:selected').val() != 'none') {
                $table.find('tr').show();
            } else {
                $table.find('tr').hide();
                $toggleRow.show();
            }
        });

        $borderType.on('change', function() {
            $table.find('[name="border-type"]').css('border-style', $(this).find('option:selected').val());
        });
    };

    Controller.prototype.toggleCaptions = function () {
        var $table = $('table[name="captions"] thead'),
            $toggleRow = $('#useCaptions'),
            value = 0;

        value = this.strToBool($('[name="thumbnail[overlay][enabled]"]:checked').val());

        $('#hideCaptions').on('change', function () {
            $table.find('tr').hide();
            $toggleRow.show();
        }).trigger('change');

        $('#showCaptions').on('change', function () {
            $table.find('tr').show();
        }).trigger('change');

        if(value) {
            $table.find('tr').show();
        } else {
            $table.find('tr').hide();
            $toggleRow.show();
        }
    };

    Controller.prototype.areaNotifications = function () {
        var $photoWidth = $('input[name= "area[photo_width]"]'),
            $photoHeight = $('input[name= "area[photo_height]"]'),
            $postFeed = $('select[name="posts[enable]"]'),
            $overlay = $('[name="thumbnail[overlay][enabled]"], [name="icons[enabled]"]'),
            self = this;

        $photoWidth.on('change' , function() {
            if($photoWidth.val() < 240 && parseInt($postFeed.val(), 10)) {
                $.jGrowl('Low image width \n post feed content can be too small');
            }

            if($photoWidth.val() == 'auto') {
                $.jGrowl('Use image original width');
            }
        });

        $photoHeight.on('change', function() {
            if($photoHeight.val() < 240 && parseInt($postFeed.val(), 10)) {
                $.jGrowl('Low image height \n post feed content can be too small');
            }

            if($photoHeight.val() == 'auto') {
                $.jGrowl('Use image original height');
            }
        });

        $overlay.on('change', function(event) {
            var $overlayChecked = $('[name="thumbnail[overlay][enabled]"]:checked, [name="icons[enabled]"]:checked'),
                showNotification = true;

            $.each($overlayChecked, function(index, value) {
                if(!self.strToBool($(value).val()) || !this.length) {
                    showNotification = false;
                }
            });


            if(showNotification) {
                $.jGrowl("Caption animation effect is disabled now, turn off icons to use it");
            }
        });
    }

    Controller.prototype.togglePostsTable = (function() {
        var $navButtons = $('.form-tabs'),
            $table = $('#gbox_ui-jqgrid-htable');

        $navButtons.on('click', function() {
            if($(this).find('a.active').attr('href') == 'post') {
                $table.show();
            } else {
                $table.hide()
            }
        }).trigger('click');

    });

    Controller.prototype.togglePopUp = (function() {
        $popupTable = $('#box').closest('table');

        $('#box-enable').on('change', function () {
            $popupTable.find('tbody').show();
        });

        $('#box-disable').on('change', function () {
            $popupTable.find('tbody').hide();
        });

        $popupTable.find('thead input[type="radio"]:checked')
            .trigger('click')
            .trigger('change');
    });

    Controller.prototype.toggleIcons = (function() {
        var $table = $('#photo-icon').closest('table');

        $('#icons-enable').on('change', function () {
            $table.find('tbody').show();
        });

        $('#icons-disable').on('change', function () {
            $table.find('tbody').hide();
        });

        $table.find('thead input[type="radio"]:checked').trigger('click').trigger('change');
    });

    Controller.prototype.togglePosts = function () {
        var $changedRow = $('select[name="posts[enable]"]'),
            $toggleRow = $('select[name="quicksand[enabled]"]'),
            value = 0;

        $changedRow.on('change', function () {
            value = parseInt($(this).val(), 10);

            if (value) {
                $toggleRow.attr('disabled', 'disabled');
                if ($toggleRow.val() > 0) {
                    $.jGrowl('You cant use image shuffling option \n when post feed is enabled');
                    $toggleRow.val('0');
                };
            } else {
                $toggleRow.removeAttr('disabled');
            }
        }).trigger('change');
    };

    Controller.prototype.toggleAutoPosts = function() {
        var $autoposts = $("#autoposts"),
            $autopostsCategories = $("#autopostsCategories"),
            $autopostsElements = [
                $("#autopostsNumber").closest('tr'),
                $("#autopostsCategories").closest('tr')
            ],
            $postsElements = [
                $("#posts").closest('tr'),
                $("#pages").closest('tr'),
                $("#table-toolbar"),
                $("#gview_ui-jqgrid-htable"),

            ];

        $autopostsCategories.chosen({width: '200px'});
        // $autopostsCategories.next('div').find('li.search-field').height('35px');

        $autoposts.on('change', function () {
            var value = parseInt($(this).val(), 10);

            if (value) {
                for(var i in $autopostsElements){
                    $autopostsElements[i].show();
                }
                for(var i in $postsElements){
                    $postsElements[i].hide();
                }
            } else {
                for(var i in $autopostsElements){
                    $autopostsElements[i].hide();
                }
                for(var i in $postsElements){
                    $postsElements[i].show();
                }
            }
        }).trigger('change');
    }

    Controller.prototype.initSocialSharing = function(){
        var $table = $('#social-sharing').closest('table');

        $('#image-sharing-hidden,#popup-sharing-hidden').closest('tr').hide();

        $('#social-sharing-enable').on('change', function () {
            $table.find('tbody').show();
        });

        $('#social-sharing-disable').on('change', function () {
            $table.find('tbody').hide();
        });

        $table.find('thead input[type="radio"]:checked').trigger('click').trigger('change');

        //init gallery sharing checkbox
        var gallerySharingToggle = function(){
            var $input = $('#gallery-social-sharing').find('input'),
                $buttonsPositionTr = $('#gallery-sharing-buttons-position').closest('tr');
            if($input.is(':checked')){
                $buttonsPositionTr.show();
            }else{
                $buttonsPositionTr.hide();
            }
        };
        $('#gallery-social-sharing').find('input').on('click',function(){
            gallerySharingToggle();
        });
        gallerySharingToggle();

        var $settingSelectors = {
            'imageButtons' : {
                'enableInput' : $('#image-social-sharing').find('input'),
                'buttonsPositionSelect' : $('#image-sharing-buttons-position').find('select'),
                'buttonsAlignHorizontalTr' : $('#image-sharing-buttons-align-horizontal').closest('tr'),
                'buttonsAlignVerticalTr' : $('#image-sharing-buttons-align-vertical').closest('tr'),
            },
            'popupButtons' : {
                'enableInput' : $('#popup-social-sharing').find('input'),
                'buttonsPositionSelect' : $('#popup-sharing-buttons-position').find('select'),
                'buttonsAlignHorizontalTr' : $('#popup-sharing-buttons-align-horizontal').closest('tr'),
                'buttonsAlignVerticalTr' : $('#popup-sharing-buttons-align-vertical').closest('tr'),
            },
        };

        //init image sharing checkbox
        var buttonsPositionToggle = function(index){
            var set = $settingSelectors[index],
                buttonsPosition = set.buttonsPositionSelect.val(),
                $alignHorizontalTr = set.buttonsAlignHorizontalTr,
                $alignVerticalTr = set.buttonsAlignVerticalTr;

            if(buttonsPosition == 'top' || buttonsPosition == 'bottom'){
                $alignHorizontalTr.show();
                $alignVerticalTr.hide();
            }else{
                $alignHorizontalTr.hide();
                $alignVerticalTr.show();
            }
        };
        var socialSharingToggle = function(index){
            var set = $settingSelectors[index],
                $input = set.enableInput,
                $buttonsPositionTr = set.buttonsPositionSelect.closest('tr');

            if($input.is(':checked')){
                $buttonsPositionTr.show();
                buttonsPositionToggle(index);
            }else{
                $buttonsPositionTr.hide();
                set.buttonsAlignHorizontalTr.hide();
                set.buttonsAlignVerticalTr.hide();
            }
        };

        $('#image-social-sharing').find('input').on('click',function(){
            socialSharingToggle('imageButtons');
        });
        $('#image-sharing-buttons-position').find('select').on('change',function(){
            buttonsPositionToggle('imageButtons');
        });
        socialSharingToggle('imageButtons');

        $('#popup-social-sharing').find('input').on('click',function(){
            socialSharingToggle('popupButtons');
        });
        $('#popup-sharing-buttons-position').find('select').on('change',function(){
            buttonsPositionToggle('popupButtons');
        });
        socialSharingToggle('popupButtons');
    };

    Controller.prototype.toggleHorizontallScroll = function () {
        var $table = $('#horizontal-scroll').closest('table');

        $('#horizontal-scroll-enable').on('change', function () {
            $table.find('tbody').show();
        });

        $('#horizontal-scroll-disable').on('change', function () {
            $table.find('tbody').hide();
        });

        $table.find('thead input[type="radio"]:checked').trigger('click').trigger('change');
    };

    Controller.prototype.selectCover = function (e) {
        var target = $(e.currentTarget),
            covers = $('.covers'),
            cover  = $('#coverUrl');

        covers.find('li').removeClass('selected');
        target.parents('li').addClass('selected');

        cover.val(target.parents('li').data('url'));
    };

    Controller.prototype.savePosts = function () {
        jQuery('[name="posts[add]"]').on('click', $.proxy(function() {
            SupsysticGallery.Loader.show('Please, wait until post will be imported.');
            var request = SupsysticGallery.Ajax.Post({
                module: 'galleries',
                action: 'savePosts'
            });

            request.add('galleryId', parseInt(this.getParameterByName('gallery_id'), 10));
            request.add('id', parseInt(jQuery('[name="posts[current]"] option:selected').val()));

            request.send($.proxy(function (response) {
                jQuery("#ui-jqgrid-htable").jqGrid('addRowData', jQuery('[name="posts[current]"] option:selected').val() , {
                    id: jQuery('[name="posts[current]"] option:selected').val(),
                    image: response.photo,
                    title: jQuery('[name="posts[current]"] option:selected').text(),
                    author: response.post_author,
                    comments: response.comment_count,
                    type: response.type,
                    date: response.post_date
                });
                SupsysticGallery.Loader.hide();
                $.jGrowl('Done');
            }, this));
        }, this));

        jQuery('#remove-posts').on('click', $.proxy(function() {
            var request = SupsysticGallery.Ajax.Post({
                module: 'galleries',
                action: 'removePosts'
            });

            var postsId = new Array();
            jQuery('.ui-jqgrid [type="checkbox"]').each(function() {
                if($(this).attr('checked')) {
                    postsId.push($(this).closest('tr').find('[aria-describedby="ui-jqgrid-htable_id"]').text());
                    $(this).closest('tr').remove();
                }
            });

            request.add('galleryId', parseInt(this.getParameterByName('gallery_id'), 10));
            request.add('id', postsId);

            request.send($.proxy(function (response) {
                $.jGrowl('Removed');
            }, this));
        }, this));

        jQuery('#button-select-posts').on('click', function() {
            checkboxes = jQuery('.ui-jqgrid input[type="checkbox"]');
            checkboxes.prop("checked", !checkboxes.first().prop("checked")).iCheck('update');
        });
    }

    Controller.prototype.savePages = function () {
        jQuery('[name="pages[add]"]').on('click', $.proxy(function () {
            SupsysticGallery.Loader.show('Please, wait until page will be imported.');
            var request = SupsysticGallery.Ajax.Post({
                module: 'galleries',
                action: 'savePages'
            });

            request.add('galleryId', parseInt(this.getParameterByName('gallery_id'), 10));
            request.add('id', parseInt(jQuery('[name="pages[current]"] option:selected').val()));

            request.send($.proxy(function (response) {
                jQuery("#ui-jqgrid-htable").jqGrid('addRowData', jQuery('[name="pages[current]"] option:selected').val() , {
                    id: jQuery('[name="pages[current]"] option:selected').val(),
                    image: response.photo,
                    title: jQuery('[name="pages[current]"] option:selected').text(),
                    author: response.page_author,
                    comments: response.comment_count,
                    type: response.type,
                    date: response.page_date
                });
                SupsysticGallery.Loader.hide();
                $.jGrowl('Done');
            }, this));
        }, this));
    };

    Controller.prototype.initShadowDialog = function () {
        var $wrapper = $('#shadowDialog');

        $wrapper.dialog({
            autoOpen: false,
            modal:    true,
            width:    650,
            buttons:  {
                Cancel: function () {
                    $(this).dialog('close');
                }
            }
        });

        Controller.prototype.initShadowSelect = function () {
            var $shadowColor = $('[name="thumbnail[shadow][color]"]'),
                $shadowOffsetX = $('[name="thumbnail[shadow][x]"]'),
                $shadowOffsetY = $('[name="thumbnail[shadow][y]"]'),
                $shadowBlur = $('[name="thumbnail[shadow][blur]"]');

            $wrapper.find('.shadow-preset').on('click', function() {
                var offsetX = parseInt($(this).data('offset-x')),
                    offsetY = parseInt($(this).data('offset-y')),
                    blur = parseInt($(this).data('blur')),
                    color = $(this).data('color');

                $shadowColor.attr('value', color);
                $shadowOffsetX.attr('value', offsetX);
                $shadowOffsetY.attr('value', offsetY);
                $shadowBlur.attr('value', blur);

                $shadowColor.trigger('change');

                $wrapper.dialog('close');
            });
        };

        Controller.prototype.openShadowDialog = function () {
            var $button = $('#chooseShadowPreset');

            $button.on('click', function() {
                $wrapper.dialog('open');
            });
        };
    };

    Controller.prototype.initImportSettingDialog = function () {

        galleryId = parseInt(this.getParameterByName('gallery_id'), 10);

        $.get(window.wp.ajax.settings.url, {
            action: 'grid-gallery',
            _wpnonce: SupsysticGallery.nonce,
            route: {
                module: 'galleries',
                action: 'getGalleriesList'
            }
        }).success(function (response) {
            if (response.list) {
                $.each(response.list, function (i, item) {
                    if (galleryId != item.id) {
                        $('#settingsImportDialog .list').append($('<option>', {
                            value: item.id,
                            text: item.title
                        }));
                    }
                });
                if (response.list.length < 2) {
                    $('#settingsImportDialog .import').hide();
                    $('#settingsImportDialog .import-not-available').show();
                }
            }
        });

        var $wrapper = $('#settingsImportDialog');

        $wrapper.dialog({
            autoOpen: false,
            modal:    true,
            width:    350,
            buttons:  [
                {
                    text: 'Cancel',
                    click: function () {
                        $(this).dialog('close');
                    },
                },
                {
                    id: 'import-confirm-button',
                    text: 'Import',
                    click: function () {
                        $.post(window.wp.ajax.settings.url, {
                            action: 'grid-gallery',
                            _wpnonce: SupsysticGallery.nonce,
                            route: {
                                module: 'galleries',
                                action: 'importSettings'
                            },
                            from: $(this).find('.list').val(),
                            to: galleryId
                        }).success(function(response) {
                            if (response.success) {
                                window.location.reload();
                            }
                        });
                    },
                }
            ]
        });

        $('#openSettingsImportDialog').on('click', function(event) {
            event.preventDefault();
            $wrapper.dialog('open');
            if ($wrapper.find('.import').is(':hidden')) {
                $('#import-confirm-button').hide();
            }
        });
    };

    Controller.prototype.initCustomButtonsPrevieDialog = function () {
        $buttonsEditPresetDialog = $('.buttons-edit-preset-dialog-preview');

        $buttonsEditPresetDialog.dialog({
            autoOpen: false,
            modal:    true,
            width:    880,
            buttons:  {
                Cancel: function () {
                    $(this).dialog('close');
                },
                'Get Pro': function () {
                    url = $(this).find('a').attr('href');
                    window.open(url);
                    $(this).dialog('close');
                }
            }
        });

        $('#custom-buttons-preview input').on('click', function(event) {
            event.preventDefault();
            $buttonsEditPresetDialog.dialog('open');
        });
    };

    Controller.prototype.polaroidEffectSettings = function () {
        $('#polaroid-effect select').on('change', function(event) {
            event.preventDefault();
            $overlayEffect = $('#overlayEffect').val();
            $polaroidSettings = $('#polaroid-animation, #polaroid-scattering').closest('tr')
            if ($(this).val() == 'true') {
                $polaroidSettings.removeClass('hidden');
                if ($overlayEffect !== 'polaroid') {
                    confirm('Caption Effect will change to Polaroid Style') && $('#effectsPreview [data-grid-gallery-type="polaroid"]').trigger('click');
                }
            } else {
                $polaroidSettings.addClass('hidden');
                if ($overlayEffect == 'polaroid') {
                    $('#effectsPreview [data-grid-gallery-type]:first').trigger('click');
                }
            }
        }).trigger('change');

        $('#polaroid-animation select, #polaroid-scattering select, #polaroid-frame-width input').on('change', function(event) {
            event.preventDefault();
            clearTimeout($('#preview.gallery-preview').data('polaroidDebounce'));
            $('#preview.gallery-preview').data('polaroidDebounce', 
                setTimeout(function() {
                    app.ImagePreview.initCaptionEffects();
                }, 300));
        });

        $('#preview.gallery-preview').on('updateCaptionBackground', function(event, background) {
            var $bgWrap = $('#preview .polaroid-bg-wrap');

            if (! $bgWrap.length) {
                var $figcaption = $('#preview .grid-gallery-caption[data-grid-gallery-type="polaroid"]');
                $figcaption.find('img').wrap('<div class="polaroid-bg-wrap">');
                $bgWrap = $figcaption.find('.polaroid-bg-wrap');
                $bgWrap.css({
                    'transition':  $('#preview .grid-gallery-caption').css('transition')
                });
            }

            $(this).find('.grid-gallery-caption').css('background-color', background);
        });
    };

    //Get Pro Loader
    Controller.prototype.initLoaderPrevieDialog = function () {
        $LoaderDialog = $('.loader-dialog-preview');

        $LoaderDialog.dialog({
            autoOpen: false,
            modal:    true,
            width:    450,
            buttons:  {
                Cancel: function () {
                    $(this).dialog('close');
                }
            },
            create: function (event) { 
                $(event.target).parent().css({
                    position: 'fixed'
                });
            }
        });

        $('#choosePreicon-free').on('click', function(event) {
            event.preventDefault();
            $LoaderDialog.dialog('open');
        });
        $('#preloadColor-free').on('change',function(){
            $LoaderDialog.dialog('open');
        });
    };
    Controller.prototype.togglePreload = (function() {
        $preloadTable = $('[name=preload]');

        $('#preload-enable').on('change', function () {
            $preloadTable.find('tbody').show();
        });

        $('#preload-disable').on('change', function () {
            $preloadTable.find('tbody').hide();
        });

        $preloadTable.find('thead input[type="radio"]:checked')
            .trigger('click')
            .trigger('change');
    });

    Controller.prototype.initOpenByLink = function(){
        var openByLink = $('#open-by-link').find('input'),
            galleryLinkTr = $('#gallery-link').closest('tr');

        var checkOpenLink = function(){
                if(openByLink.is(':checked')){
                    galleryLinkTr.show();
                }else{
                    galleryLinkTr.hide();
                }
            };
        openByLink.on('change',function(){
            setTimeout(function(){checkOpenLink()},100)
        });

        checkOpenLink();
    };

    $(document).ready(function () {
        var qs = new URI().query(true), controller;

        if (qs.module === 'galleries' && qs.action === 'settings') {
            controller = new Controller();

            $('a').not('.nav-tab, .wp-color-result').on('click', function(event) {
                if (controller.settingsChanged && !confirm($('.settings-wrap').data('leave-confirm'))) {
                    event.stopPropagation();
                    event.preventDefault();
                    return false;
                }
            });

            controller.setScroll();
            controller.initSaveDialog();
            controller.initDeleteDialog();
            controller.initLoadDialog();
            controller.initThemeDialog();
            controller.initEffectsDialog();
            controller.initIconsDialog();
            controller.initLoaderPrevieDialog();
            controller.togglePreload();

            controller.initEffectPreview();

            controller.initShadowDialog();
            controller.initImportSettingDialog();
            controller.initShadowSelect();
            controller.openShadowDialog();

            controller.toggleArea();
            controller.toggleShadow();
            controller.toggleBorder();
            controller.toggleCaptions();
            controller.togglePopUp();
            controller.toggleIcons();
            controller.toggleHorizontallScroll();

            controller.initSocialSharing();
            controller.initThemeSelect();

            controller.savePosts();
            controller.savePages();

            controller.showReviewNotice();

            controller.saveButton();
            controller.togglePosts();
            controller.togglePostsTable();
            controller.toggleAutoPosts();
            controller.areaNotifications();
            controller.setInputColor();
            controller.toggleSlideShow();

            controller.initOpenByLink();

            controller.polaroidEffectSettings();


            controller.initCustomButtonsPrevieDialog();

            // Save as preset dialog
            $('#btnSaveAsPreset').on('click', controller.openSaveDialog);

            // Delete preset dialog
            $('#btnDeletePreset').on('click', controller.openDeleteDialog);

            // Load from preset dialog
            $('#btnLoadFromPreset').on('click', controller.openPresetDialog);

            // Delete gallery
            $('.delete').on('click', controller.remove);

            // Change the tab
            $('.change-tab')
                .on('click', $.proxy(controller.changeTab, controller));

            // Open theme dialog
            $('#chooseTheme').on('click', controller.openThemeDialog);

            // Open effects dialog
            $('#chooseEffect').on('click', controller.openEffectsDialog);

            // Cover
            $('.covers img').on('click', controller.selectCover);

        $(document).one('input ifClicked', '.supsystic-plugin :input',function(event) {
            controller.settingsChanged = true;
        });

        }
    });

}(window.SupsysticGallery = window.SupsysticGallery || {}, jQuery));

// Preview

(function ($, Gallery) {
    var getSelector = (function (fieldName) {
        return '[name="' + fieldName + '"]';
    });


    function ImagePreview(enabled) {
        this.$window = $('#preview.gallery-preview');

        if (enabled) {
            this.init();
        }

        this.$window.on('preview.refresh', $.proxy(function(event) {
            this.init();
        }, this));
    }

    ImagePreview.prototype.setProp = (function (selector, props) {
        this.$window.find(selector).css(props);
    });

    ImagePreview.prototype.setDataset = (function (selector, name, value) {
        this.$window.find(selector).attr(name, value);
    });

    ImagePreview.prototype.initBorder = (function () {
        var fieldNames = {
            type: 'thumbnail[border][type]',
            color: 'thumbnail[border][color]',
            width: 'thumbnail[border][width]',
            radius: 'thumbnail[border][radius]',
            radiusUnit: 'thumbnail[border][radius_unit]'
        };

        $(getSelector(fieldNames.type)).on('change', $.proxy(function (e) {
            this.setProp('figure', { borderStyle: $(e.currentTarget).val() });
        }, this)).trigger('change');

        $('#border-color a.wp-color-result').attrchange({
            trackValues: true,
            callback: function (e) {
                if(e.attributeName == 'style') {
                    $('#preview .grid-gallery-caption').css('border-color', e.newStyle);
                }
            }
        });

        $(getSelector(fieldNames.color)).on('change', $.proxy(function (e) {
            this.setProp('figure', { borderColor: $(e.currentTarget).val() });
        }, this)).trigger('change');

        $(getSelector(fieldNames.width)).bind('change paste keyup', $.proxy(function (e) {
            this.setProp('figure', { borderWidth: $(e.currentTarget).val() });
        }, this)).trigger('change');

        $(getSelector(fieldNames.radius) + ',' + getSelector(fieldNames.radiusUnit))
            .bind('change paste keyup', $.proxy(function () {
                var $value = $(getSelector(fieldNames.radius)),
                    $unit  = $(getSelector(fieldNames.radiusUnit)),
                    unitValue = 'px';

                if (parseInt($unit.val(), 10) == 1) {
                    unitValue = '%';
                }

                this.setProp('figure', { borderRadius: $value.val() + unitValue });

            }, this))
            .trigger('change');
    });

    ImagePreview.prototype.initIcons = (function () {
        var fields = {
            iconsColor: 'icons[color]',
            hoverIconsColor: 'icons[hover_color]',
            bgColor: 'icons[background]',
            hoverBgColor: 'icons[background_hover]',
            iconsSize : 'icons[size]'
        };

        if($.parseJSON($('#showIcons').val())) {
            $('#preview figure.grid-gallery-caption').attr('data-grid-gallery-type', 'icons');
            $('#preview figcaption').show();
        }
        $('#showIcons').on('change', $.proxy(function() {
            if($.parseJSON($('#showIcons').val())){
                this.setDataset('figure', 'data-grid-gallery-type', 'icons');
                $('#preview figcaption').show();
            } else {
                $('#preview figcaption').hide();
            }
        }, this));

        $(getSelector(fields.iconsColor)).bind('change paste keyup', $.proxy(function (e) {
            this.setProp('a.hi-icon', { color: $(e.currentTarget).val() });
        }, this))
            .trigger('change')
            .bind('change', $.proxy(function () {
                this.setProp('figcaption', { opacity: 1 });
            }, this))
            .on('focusout', $.proxy(function () {
                this.setProp('figcaption', { opacity: '' });
            }, this));

        $(getSelector(fields.hoverIconsColor)).bind('change paste keyup', $.proxy(function (e) {
            $('a.hi-icon').on('mouseover', $.proxy(function() {
                this.setProp('a.hi-icon', { color: $(e.currentTarget).val() });
            }, this))
            $('a.hi-icon').on('mouseleave', $.proxy(function() {
                this.setProp('a.hi-icon', { color: $(getSelector(fields.iconsColor)).val() });
            }, this))
        }, this))
            .trigger('change')
            .bind('change', $.proxy(function () {
                this.setProp('figcaption', { opacity: 1 });
            }, this))
            .on('focusout', $.proxy(function () {
                this.setProp('figcaption', { opacity: '' });
            }, this));

        $(getSelector(fields.bgColor)).bind('change paste keyup', $.proxy(function (e) {
            this.setProp('figcaption', { backgroundColor: $(e.currentTarget).val() });
        }, this))
            .trigger('change')
            .bind('change', $.proxy(function () {
                this.setProp('figcaption', { opacity: 1 });
            }, this))
            .on('focusout', $.proxy(function () {
                this.setProp('figcaption', { opacity: '' });
            }, this));

        $(getSelector(fields.hoverBgColor)).bind('change paste keyup', $.proxy(function (e) {
            $('a.hi-icon').on('mouseover', $.proxy(function() {
                this.setProp('figcaption', { backgroundColor: $(e.currentTarget).val() });
            }, this))
            $('a.hi-icon').on('mouseleave', $.proxy(function() {
                this.setProp('figcaption', { backgroundColor: $(getSelector(fields.bgColor)).val() });
            }, this))
        }, this))
            .trigger('change')
            .bind('change', $.proxy(function () {
                this.setProp('figcaption', { opacity: 1 });
            }, this))
            .on('focusout', $.proxy(function () {
                this.setProp('figcaption', { opacity: '' });
            }, this));

        $(getSelector(fields.iconsSize)).bind('change paste keyup', $.proxy(function (e) {
            this.setProp('a.hi-icon', { width: $(e.currentTarget).val()*9, height: $(e.currentTarget).val()*9  });
        }, this))
            .trigger('change')
            .bind('change', $.proxy(function () {
                this.setProp('figcaption', { opacity: 1 });
            }, this))
            .on('focusout', $.proxy(function () {
                this.setProp('figcaption', { opacity: '' });
            }, this));
    });

    ImagePreview.prototype.initShadow = (function () {
        var _this = this;

        var fieldNames = {
                color: getSelector('thumbnail[shadow][color]'),
                blur: getSelector('thumbnail[shadow][blur]'),
                x: getSelector('thumbnail[shadow][x]'),
                y: getSelector('thumbnail[shadow][y]')
            },
            selectors = $.map(fieldNames, function(item) {
                return item;
            });

        updateShadowProp = function(properties) {
            _this.setProp('figure', properties);
        }

        $(selectors.join(',')).off('change paste keyup').on('change paste keyup', function() {
            boxShadow = $(fieldNames.x).val() + 'px ' + $(fieldNames.y).val() + 'px ' + $(fieldNames.blur).val() + 'px ' + $(fieldNames.color).val();
            _this.$window.find('figure')
            .attr('data-box-shadow', boxShadow)
            .trigger('shadowChange');
        });

        $('[name="use_shadow"]').off('change').on('change', function() {
            if ($(this).val() == 1) {
                $(fieldNames.x).trigger('change');
            } else {
                updateShadowProp({
                    boxShadow: 'none'
                });
            }
        });

        $('[name="use_shadow"]:checked').trigger('change');
    });

    ImagePreview.prototype.initMouseShadow = (function() {
        var self = this,
        wrapper = {
            element: '#preview figure.grid-gallery-caption',
            $node: $('#preview figure.grid-gallery-caption'),
            toggleEvents: function() {
                this.$node.off('hover', showOver);
                this.$node.off('hover', hideOver);
            }
        },
        shadow = wrapper.$node.data('box-shadow'),
        showOver = function(event) {
            if (event.type === 'mouseenter') {
                $(this).css('box-shadow', shadow);
            } else {
                $(this).css('box-shadow', 'none');
            }
        },
        hideOver = function(event) {
            if (event.type === 'mouseenter') {
                $(this).css('box-shadow', 'none');
            } else {
                $(this).css('box-shadow', shadow);
            }
        };

        wrapper.$node.on('shadowChange', function() {
            shadow = $(this).attr('data-box-shadow');
            $('#useMouseOverShadow').trigger('change');
        });

        $('#useMouseOverShadow').on('change',function() {
            value = parseInt($('#useMouseOverShadow option:selected').val(), 10);
            wrapper.toggleEvents();

            if (value == 1) {
                wrapper.$node.css('box-shadow', 'none');
                wrapper.$node.on('hover', showOver);
            }

            if (value == 2) {
                wrapper.$node.css('box-shadow', shadow);
                wrapper.$node.on('hover', hideOver);
            }

            if(!value) {
                wrapper.$node.css('box-shadow', shadow);
            }

        }).trigger('change');
    });

    ImagePreview.prototype.initOverlayShadow = (function() {
        var wrapper = {
            element: '.grid-gallery-caption img',
            $node: $('#preview figure.grid-gallery-caption')
        },
        $toggle = $('[name="thumbnail[shadow][overlay]"]'),
        self = this,
        overlayShadow = function(event) {
            if (event.type === 'mouseenter') {
                self.setProp(wrapper.element, {opacity: '1.0'});
            } else {
                 self.setProp(wrapper.element, {opacity: '0.2'});
            }
        };

        $toggle.on('change', function() {
            var value = parseInt($('option:selected', $toggle).val(), 10);
            wrapper.$node.off('hover', overlayShadow);
            if (value) {
                self.setProp(wrapper.element, {opacity: '0.2'});
                wrapper.$node.on('hover', overlayShadow);
            } else {
                self.setProp(wrapper.element , {opacity: '1.0'});
            }
        }).trigger('change');
    });

    ImagePreview.prototype.previewCaptionHide = function() {
        $('.gallery-preview .grid-gallery-caption')
            .data('grid-gallery-type', 'none')
            .attr('data-grid-gallery-type', 'none');
        this.initCaptionEffects();
        $('#preview figcaption').hide();
    };

    ImagePreview.prototype.previewCaptionShow = function(fields) {
        $('#preview figcaption').show();
        this.setDataset('figure', 'data-grid-gallery-type', $('#overlayEffect').val());

        $('#effectsPreview').find('figure').bind('click', $.proxy(function (e) {
            this.setDataset('figure', 'data-grid-gallery-type', $(e.currentTarget).data('grid-gallery-type'));
        }, this)).trigger('change');

        $(getSelector(fields.bg)).bind('change', $.proxy(function (e) {
            var color = hexToRgb($(e.currentTarget).val());
            backgroundColor = 'transparent';
            if (color) {
                backgroundColor = 'rgba(' + color.r + ',' + color.g + ',' + color.b + ',' +
                    (1 - $(getSelector(fields.opacity)).val() / 10) + ')';
            }

            if(this.$window.find('figure').data('grid-gallery-type') == 'polaroid'){
                this.setProp('figcaption', {
                    backgroundColor: 'transparent'
                });
            } else {
                this.setProp('figcaption', {
                    backgroundColor: backgroundColor
                });
            }

            this.$window.trigger('updateCaptionBackground', backgroundColor);
        }, this)).trigger('change');

        $(getSelector(fields.fg)).bind('change', $.proxy(function (e) {
            this.setProp('figcaption', { color: $(e.currentTarget).val() });
        }, this)).trigger('change');

        function hexToRgb(hex) {
            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }

        $(getSelector(fields.opacity)).bind('change', $.proxy(function (e) {
            $(getSelector(fields.bg)).trigger('change');
        }, this));

        $(getSelector(fields.size) + ',' + getSelector(fields.sizeUnit))
            .bind('change', $.proxy(function (e) {
                var $size = $(getSelector(fields.size)),
                    $unit = $(getSelector(fields.sizeUnit)),
                    unitValue = 'px';

                switch (parseInt($unit.val(), 10)) {
                    case 0:
                        unitValue = 'px';
                        break;
                    case 1:
                        unitValue = '%';
                        break;
                    case 2:
                        unitValue = 'em';
                        break;
                }

                this.setProp('figcaption', { fontSize: $size.val() + unitValue });
            }, this)).trigger('change');

        $(getSelector(fields.align)).on('change', $.proxy(function (e) {
            var value = '';

            if($(e.currentTarget).val() != 'auto') {
                value = $(e.currentTarget).val();
            }

            this.setProp('figcaption', { textAlign: value });
        }, this)).trigger('change');

        $(getSelector(fields.fontFamily)).on('change', $.proxy(function (e) {
            var fontFamily = $(getSelector(fields.fontFamily)).val();
			if(!fontFamily || fontFamily == 'Default') return;
            WebFont.load({
                google: {
                    families: [fontFamily]
                }
            });
            this.setProp('figcaption', { fontFamily: '"' + fontFamily + '"' + ', serif' });
        }, this)).trigger('change');

        $(getSelector(fields.position)).on('change', $.proxy(function (e) {
            var position = $(getSelector(fields.position)).val();
            this.setProp('.grid-gallery-figcaption-wrap', { verticalAlign: position });
        }, this)).trigger('change');

        var hideFigcaptionTimer;
        $elements = $();
        for (var i in fields) {
            $.merge($elements, $(getSelector(fields[i])));
        };

        $elements.on('change keyup input paste', $.proxy(function () {
            self = this;
            $('#preview .grid-gallery-caption').addClass('hovered')
            clearTimeout(hideFigcaptionTimer);
            hideFigcaptionTimer = setTimeout(function() {
                $('#preview .grid-gallery-caption').removeClass('hovered');
            }, 3000);
        }, this));
    };

    ImagePreview.prototype.initCaption = (function () {
        var fields = {
            effect: 'thumbnail[overlay][effect]',
            position: 'thumbnail[overlay][position]',
            bg: 'thumbnail[overlay][background]',
            fg: 'thumbnail[overlay][foreground]',
            opacity: 'thumbnail[overlay][transparency]',
            size: 'thumbnail[overlay][text_size]',
            sizeUnit: 'thumbnail[overlay][text_size_unit]',
            align: 'thumbnail[overlay][text_align]',
            fontFamily: 'thumbnail[overlay][font_family]'
        };

        $('[name="thumbnail[overlay][enabled]"]').on('change', $.proxy(function(event) {
            if(event.target.value == 'true') {
                this.previewCaptionShow(fields);
            } else {
                this.previewCaptionHide();
            }
        }, this));

        if ($('[name="thumbnail[overlay][enabled]"]:checked').val() == 'true') {
            this.previewCaptionShow(fields);
        } else {
            this.previewCaptionHide();
        }
    });

    ImagePreview.prototype.init = (function () {
        //this.$window.draggable();
        this.initBorder();
        this.initShadow();
        this.initMouseShadow();
        this.initOverlayShadow();
        //this.initIcons();
        this.initCaption();
        this.initCaptionEffects();
    });

    ImagePreview.prototype.captionCalculations = (function() {
        var heightRecalculate = function() {
            var figcaption = $('div#preview > figure > figcaption'),
                captionStyle = {
                    'height': figcaption.innerHeight(),
                    'display': 'table'
                },
                wrap = figcaption.find('div.grid-gallery-figcaption-wrap');
            figcaption.css(captionStyle);
            wrap.css('display', 'table-cell');
        };
        $('div#preview > figure').on('change', function() {
            heightRecalculate();
        });
    });

    ImagePreview.prototype.checkDirection = function($element, e) {
        var w = $element.width(),
            h = $element.height(),
            x = ( e.pageX - $element.offset().left - ( w / 2 )) * ( w > h ? ( h / w ) : 1 ),
            y = ( e.pageY - $element.offset().top - ( h / 2 )) * ( h > w ? ( w / h ) : 1 );

        return Math.round(( ( ( Math.atan2(y, x) * (180 / Math.PI) ) + 180 ) / 90 ) + 3) % 4;
    };

    ImagePreview.prototype.initCaptionEffects = (function () {
        var self = this, figure = $('figure.grid-gallery-caption');

        if (!this.defaultStyles) {
            this.defaultStyles = {
                figureStyle: figure.attr('style'),
                imageStyle: figure.find('img').attr('style')
            }
        };

        figure.each(function() {
            $(this).removeAttr('style').attr('style', self.defaultStyles.figureStyle);
            $(this).find('img').removeAttr('style').attr('style', self.defaultStyles.imageStyle);
            if(!$(this).find('.polaroid-bg-wrap').length){
                $(this).find('img').wrap('<div class="polaroid-bg-wrap">');
            }
            // $(this).off('mouseenter mouseleave');
            $(this).find('figcaption').removeClass();
            $(this).off('mouseenter mouseleave');

            if ($(this).data('grid-gallery-type') == 'cube') {
                $(this).on('mouseenter mouseleave', function(e) {
                    var $figcaption = $(this).find('figcaption'),
                        direction = self.checkDirection($(this), e),
                        classHelper = null;

                    switch (direction) {
                        case 0:
                            classHelper = 'cube-' + (e.type == 'mouseenter' ? 'in' : 'out') + '-top';
                            break;
                        case 1:
                            classHelper = 'cube-' + (e.type == 'mouseenter' ? 'in' : 'out') + '-right';
                            break;
                        case 2:
                            classHelper = 'cube-' + (e.type == 'mouseenter' ? 'in' : 'out') + '-bottom';
                            break;
                        case 3:
                            classHelper = 'cube-' + (e.type == 'mouseenter' ? 'in' : 'out') + '-left';
                            break;
                    }
                    $figcaption.removeClass()
                        .addClass(classHelper);
                });
            }

            if ($(this).data('grid-gallery-type') == 'polaroid' &&
                $(this).closest('#preview').length > 0) {
                var frameWidth = $('[name="thumbnail[overlay][polaroidFrameWidth]"]').val(),
                    $img = $(this).find('img'),
                    width = $(this).width() || $img.width(),
                    scaleRatio = $img.width() / $img.height(),
                    imageWidth = $img.width() - frameWidth * 2,
                    imageHeight = imageWidth / scaleRatio,
                    $figcaption = $(this).find('figcaption');

                $img[0].style.setProperty('width', imageWidth + 'px', 'important');
                $img[0].style.setProperty('height', imageHeight + 'px', 'important');
                $img[0].style.setProperty('margin', '0 auto', 'important');
                $img[0].style.setProperty('padding-top', frameWidth + 'px', 'important');

                $(this).css({
                    'width': $(this).width(),
                });

                $figcaption.css('padding', frameWidth + 'px');

                if ($('#polaroid-scattering select').val() == 'true') {
                    $(this).css({
                        'transform': 'rotate(' + (-3 + Math.random() * (10 - 3)) + 'deg)'
                    });
                    $('#preview .grid-gallery-caption').addClass('polaroid-scattering');
                } else {
                    $('#preview .grid-gallery-caption').removeClass('polaroid-scattering');
                }

                if ($('#polaroid-animation select').val() == 'true') {
                    $('#preview .grid-gallery-caption').addClass('polaroid-animation');
                } else {
                    $('#preview .grid-gallery-caption').removeClass('polaroid-animation');
                }
            }
        });
    });

    $(document).ready(function () {
        jQuery('input#cmn-preview').click(function() {
            if($(this).is(':checked')) {
                jQuery('#preview figure').show();
            } else {
                jQuery('#preview figure').hide();
            }
        });

        Gallery.ImagePreview = new ImagePreview(true);
    });
}(jQuery, window.SupsysticGallery = window.SupsysticGallery || {}));
