(function ($, undefined) {

    function Gallery(selector, autoInit) {

        autoInit = autoInit || false;
        var $deferred = jQuery.Deferred(),
			self = this;

        this.$container  = $(selector);
        this.$container.addClass('fitvidsignore');
        this.$elements   = this.$container.find('figure.grid-gallery-caption').fadeIn();
        this.initialMargin = this.$elements.first().css('margin-bottom');
        this.$navigation = this.$container.find('nav.grid-gallery-nav');

        this.selectedCategory="";

        this.$qsData = null;
        this.$qsDuration = '750';
        this.$qsEnable = false;
        this.areaPosition = this.$container.data('area-position');	// I think we wil need this in future

        this.pagination = {
            currentPage: 1,
            limit: 0,
            total: this.$elements.length,
            pages: 1,
            $wrapper: this.$container.find('.grid-gallery-pagination-wrap')
        };

        this.loadingText = this.$container.data('show-more-loading-text');

        this.popupTranslates = this.$container.data('popup-i18n-words');
        this.popupTitleAttribute = this.$container.data('popup-image-text');
        this.popupMaxHeight = '90%';
        this.popupMaxWidth = '90%';
        this.popup_opened_image = false;
        this.popupImageDimension = function(){};
        this.resizeColorbox = function(){};

        this.socialSharing = this.$container.data('social-sharing');
        this.socialSharingWrapperClass = 'supsystic-grid-gallery-image-sharing';
        this.socialSharingImageOperators = {
            'pinterest': 'media',
        };
        this.socialButtonsUrl = window.location.href.replace(window.location.hash,'');
        this.socialButtonsUrl = this.removePopUpHashFromUrl(this.socialButtonsUrl);
        if(this.socialButtonsUrl.indexOf('#') + 1 == this.socialButtonsUrl.length){
            this.socialButtonsUrl = this.socialButtonsUrl.substr(0,this.socialButtonsUrl.length-1);
        }

		this.disablePopupHistory = !!this.$container.data('popup-disable-history');

        if (this.isFluidHeight()) {
            this.$elements.addClass('wookmarked');
        }

        $(document).trigger("GalleryExtend", this);

        if (autoInit) {
			// Interval check for init gallery when container becomes visible.
			// This need to properly load gallery from tabs, accordions or any other hidden containers.
			self.$container.data('isVisible', setInterval(function() {
				if (self.$container.is(':visible')) {
					clearInterval(self.$container.data('isVisible'));
					self.init();
				}
			}, 500));
        }


        return $deferred.resolve(); 
    }

    Gallery.prototype.isFluidHeight = (function () {
        return this.$container.is('.grid-gallery-fluid-height');
    });

    Gallery.prototype.isImageOverlay = (function () {
        return this.$container.find('.crop').is('.image-overlay');
    });

    Gallery.prototype.isMouseShadowShow = (function () {
        return this.$container.find('.grid-gallery-caption').is('.shadow-show');
    });

    Gallery.prototype.initQuicksand = (function () {
        if(this.$container.data('quicksand') == 'enabled')  {
            this.$qsEnable = true;
            this.$qsDuration = this.$container.data('quicksand-duration');
            this.$qsHolder = this.$container.find('.grid-gallery-photos:first');
            this.$qsData = this.$container.find('.grid-gallery-photos > a');
        }
    });

    Gallery.prototype.showCaption = (function () {
        this.$container.find('.grid-gallery-figcaption-wrap').each(function() {
            if ($.trim($(this).html()) === '' && !$(this).find('img').length && $(this).has('.hi-icon').length === 0) {
                $(this).closest('figcaption').remove();
            }
        });

        this.$container.find('.gg-image-caption').each(function() {
            var $this = $(this);
            $this.html($this.text().replace(/<a(.*?)>(.*?)<\/a>/gi,"<object type='none'>$&</object>"));
            $this.find('a').on('click', function(event) {
                event.stopPropagation();
            });
        });

        $(document).on('click', '.sliphover-container object a', function(event) {
             event.stopPropagation();
        });
    });

    Gallery.prototype.initWookmark = (function () {

        var self = this,
            width = this.$container.data('width'),
            offset = 0,
            outerOffset = 0,
            spacing,
            windowWidth = $(window).width();

        if (this.$container.data('horizontal-scroll')) {
            return;
        }

        if (this.$container.data('offset')) {
            offset = this.$container.data('offset');
        }

        if (this.$container.data('padding')) {
            outerOffset = parseInt(this.$container.data('padding'));
        }

        if (String(width).indexOf('%') > -1) {
            var imagesPerRow = Math.floor(100 / parseInt(width));

            spacing = (offset * (imagesPerRow - 1)) + outerOffset * 2;
            width = (this.$container.width() - spacing) / 100 * parseInt(width);

            $.each(this.$container.find('img'), function() {
                aspectRatio = $(this).width() / $(this).height();
                $(this).width(width);
                $(this).height(width / aspectRatio);
            });
        }


        function resizeColumns() {
            var columnsNumber = self.getResponsiveColumnsNumber();

            spacing = (offset * (columnsNumber - 1)) + outerOffset * 2;
            width = Math.floor((self.$container.width() - spacing) / 100 * Math.floor(100 / columnsNumber));

            $.each(self.$elements, function(index, el) {

                var $el = $(el),
                    $img = $el.find('img'),
                    imageOriginalSize = self.getOriginalImageSizes($img.get(0)),
                    elWidth = imageOriginalSize.width,
                    elHeight = imageOriginalSize.height,
                    aspectRatio = elWidth / elHeight,
                    height = width / aspectRatio;

                $el.css({
                    width: width,
                    height: height,
                });

            });

            return width;
        }


        if (this.$container.data('columns-number')) {

            self.$container.find('img').css({
                maxWidth: '100%',
                width: '100%',
                height: 'auto'
            });

            resizeColumns();
        }

        if (this.$container.data('width') !== 'auto' && !this.$qsEnable) {


            this.wookmark = this.$elements.filter(':visible').wookmark({
                autoResize:     true,
                container:      this.$container.find('.grid-gallery-photos'),
                direction:      this.areaPosition == 'right' ? 'right' : 'left',
                fillEmptySpace: false,
                flexibleWidth:  !this.$container.data('columns-number'),
                itemWidth:      width,
                offset:         offset,
                align:          this.areaPosition,
                outerOffset:    outerOffset,
                onLayoutChanged: function() {
                    setTimeout(function() {
                        self.$container.trigger('wookmark.changed');
                    }, 50);
                },
                onResize: function() {
                    if ($(window).width() != windowWidth) { // Fix touchscreen resize event issue see #544
                        windowWidth = $(window).width();

                        clearTimeout(self.$container.data('resize.timer'));
                        self.$container.data('resize.timer', setTimeout(function() {

                            var overflow = self.$container.css('overflow');

                            self.$container.removeData('resize.timer');
                            self.$container.css('overflow', 'hidden');

                            if (self.$container.data('columns-number')) {
                                self.$elements.wookmark({
                                    container: self.$container.find('.grid-gallery-photos'),
                                    itemWidth: resizeColumns(),
                                    offset: offset,
                                });
                            }

                            self.$elements.filter(':visible').trigger('refreshWookmark');
                            self.$container.css('overflow', overflow);
                        }, 250));
                    }
                }
            }).css({
                'margin': '0',
                'transition': 'all 0.4s linear',
            });
        }

        this.$container.find('.grid-gallery-photos').css('text-align', this.$container.data('area-position'));
        this.$container.filter(':visible').find('.grid-gallery-photos > *').filter(':visible').css({
            'float': 'none',
            'display': 'inline-block',
            'vertical-align': 'top'
        });
    });

    Gallery.prototype.initControll = (function (){
        $(document).on('click', "#cboxRight", function() {
            $.colorbox.prev();
        });
        $(document).on('click', "#cboxLeft", function() {
            $.colorbox.next();
        });
    });

    Gallery.prototype.getPopupDimensions = (function(width, height){
        var width = $(window).width() < width ? '90%' : width;
        var height = $(window).height() < height ? '90%' : height;


        if(width == '90%') { width = parseFloat(($(window).width() * parseFloat(width) / 100)); }
        if(height == '90%') { height = parseFloat(($(window).height() * parseFloat(height) / 100));}

        return {
            width: width,
            height: height
        };
    });

    /**
     * Get popup title depending on gallery settings
     * @return string title for popup image
     */
    Gallery.prototype.getPopupTitle = (function($element){

        var title,
            $img;

        if ($element.hasClass('hi-icon')) {
            $img = $element.closest('.grid-gallery-caption').find('img')
        } else {
            $img = $element.find('img');
        }

        title = $img.attr(this.popupTitleAttribute);

        if (!title) {
            title = $img.attr('title');
        }

        return title;
    });

    Gallery.prototype.initPopup = (function() {
        var popupType = this.popupType = this.$container.data('popup-type'),
            popupMaxWidth = this.popupMaxWidth,
            popupMaxHeight = this.popupMaxHeight,
            sW = this.$container.data('popup-widthsize'),
            sH = this.$container.data('popup-heightsize'),
            popupOverlayTransper = this.$container.data('popup-transparency'),
            popupBackground = this.$container.data('popup-background'),
            slidePlay = this.$container.data('popup-slideshow') === true,
            slidePlayAuto = slidePlay && this.$container.data('popup-slideshow-auto') === true,
            popupHoverStop = slidePlay && this.$container.data('popup-hoverstop') === true,
            slideshowSpeed = this.$container.data('popup-slideshow-speed'),
            self = this,
            gallerySwipeRecognizer = false;


        //init js lib for gallery swipe recognize
        function initGallerySwipeRecognizer(){
            var _triggerInit = function() {
                if(typeof(Hammer_gg) == 'undefined') {
                    setTimeout(_triggerInit, 100);
                    return;
                }
                gallerySwipeRecognizer = new Hammer_gg(document);
                gallerySwipeRecognizer.get('swipe').set({ direction: Hammer_gg.DIRECTION_HORIZONTAL });
            };
            _triggerInit();
        }

        //add callback on gallery swipe
        function addGallerySwipeCallback(callback){
            var _triggerInit = function() {
                if(!gallerySwipeRecognizer) {
                    setTimeout(_triggerInit, 100);
                    return;
                }
                gallerySwipeRecognizer.on('swipe', callback);
            };
            _triggerInit();
        }

        function generateOverlayColor(selector, background, opacity, optype) {
            var style = selector + '{',
                rgb = self.hex2rgb(background);
            opacity = (100 - opacity) / 100;

            if (background) {
                color = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ', '+ opacity + ')';
                style += 'background-image:none!important; background-color:' + color + '!important;';
            } else {
                if(optype){
                    style += 'opacity:' + opacity + '!important;';
                } else {
                    rgb = self.hex2rgb(self.rgb2hex($(selector).css('backgroundColor')));
                    color = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ', '+ opacity + ')';
                    style += 'background-image:none!important; background-color:' + color + '!important;';
                }
            }
            style += '}';
            $('<style type="text/css"> ' + style + '</style>').appendTo("head");
        }

        if(!!sW && sW !== 'auto'){
            popupMaxWidth = sW;
        }else{
            sW = '90%'
        }
        if(!!sH && sH !== 'auto'){
            popupMaxHeight = sH;
        }else{
            sH = '90%';
        }

        var getImageDimension = function(){
            return self.getPopupDimensions(sW,sH);
        };

        var getColorboxImageDimension = function(){
            var response = self.getPopupDimensions(sW,sH);
            if($(self.popup_opened_image).children('figure').attr('data-linked-images')
                ||
                $(self.popup_opened_image).hasClass('linked-element')
            ){
                response.width-=120;
            }
            return response;
        };

        var delayResize = (function(){
            var timer = 0;
            return function(callback, ms){
                clearTimeout (timer);
                timer = setTimeout(callback, ms);
            };
        })();

        this.resizeColorbox = function(){
            if(self.popup_opened_image === false) return;
            var dimensions = getColorboxImageDimension();
            $(self.popup_opened_image).data('colorbox').maxWidth = dimensions.width;
            $(self.popup_opened_image).data('colorbox').maxHeight = dimensions.height;

            delayResize(function(){
                $.colorbox.resizeResponsive(self.popup_opened_image);
            }, 500);
        };



        //Responsive popup if width or height > window.width
        popupMaxWidth = $(window).width() < popupMaxWidth ? '90%' : popupMaxWidth;
        popupMaxHeight = $(window).height() < popupMaxHeight ? '90%' : popupMaxHeight;

        this.popupMaxWidth = popupMaxWidth;
        this.popupMaxHeight = popupMaxHeight;
        this.popupImageDimension = getImageDimension;
        initGallerySwipeRecognizer();

        if (popupType && popupType !== 'disable') {
            this.$container.parentsUntil('body').each(function() {
                var events = $._data(this, "events"),
                    el = this;

                if (events && events.click) {
                    $.each(events.click, function(index, ev) {
                       if (ev.selector && self.$container.has($(ev.selector)).length) {
                            $(el).off('click', ev.selector);
                       }
                    });
                }
            });
        }

        if(popupType == 'colorbox') {

            var $this = this.$container;
            var colorBoxItemSelector = '.grid-gallery-photos > .gg-colorbox:visible, .hi-icon.gg-colorbox:visible';
            // for popup "Display only first image"
            if($this.hasClass('one-photo') || $this.hasClass('hidden-item')) {
                colorBoxItemSelector = '.grid-gallery-photos > .gg-colorbox, .hi-icon.gg-colorbox';
            }

            if(this.initColorbox) {
                this.$container.find(colorBoxItemSelector).colorbox.remove();
            } else {

				var $colorboxtooltip = $('<div id="cboxTooltip">');

				$(document).on({
					mouseenter: function () {
						$colorboxtooltip.addClass('active');
					},
					mouseleave: function () {
						setTimeout(function() {
							$colorboxtooltip.removeClass('active');
						}, 400);
					}
				}, '#colorbox.theme_3 #cboxTitle');



				$(document).one('cbox_complete', function(event){
					if (!$('#cboxTooltip').length) {
						$("#cboxWrapper").append($colorboxtooltip);
					}
				});


				$(document).on('cbox_complete', function(event) {
					$colorboxtooltip.html($("#cboxTitle").html());
				});
			}

            this.initColorbox = true;
            this.$container.find(colorBoxItemSelector).colorbox({
                fadeOut: this.$container.data('popup-fadeOut'),
                fixed:  true,
                maxHeight: getImageDimension().height,
                maxWidth: getImageDimension().width,
                scalePhotos: true,
                scrolling: false,
                returnFocus: false,
                slideshow: slidePlay && this.$container.data('popup-slideshow-speed'),
                slideshowAuto: slidePlayAuto,
                slideshowSpeed: slideshowSpeed,
                rel: this.$container.attr('id'),
                slideshowStart: self.popupTranslates.start_slideshow,
                slideshowStop: self.popupTranslates.stop_slideshow,
                current: self.popupTranslates.image + " {current} " + self.popupTranslates.of + " {total}",
                previous: self.popupTranslates.previous,
                next: self.popupTranslates.next,
                close: self.popupTranslates.close,
                title: function() {
                    return self.getPopupTitle($(this));
                },
                speed: 350,
                transition: 'elastic',
                onComplete: function(e) {
                    self.changePopUpHash($(e.el).attr('id') || $(e.el).attr('data-id'));
                    self.addSocialShareToPopUp($(e.el), $('#cboxContent'), 'popup');
                    self.$container.find('.grid-gallery-photos > .gg-colorbox, .hi-icon.gg-colorbox')
                        .colorbox.resize();
                    $("#cboxLoadedContent").append("<div id='cboxRight'></div><div id='cboxLeft'></div>");
                },
                onLoad: function(e){
                    if(self.popup_opened_image == e.el) return;
                    self.popup_opened_image = e.el;
                    if($(self.popup_opened_image).children('figure').attr('data-linked-images')){
                        self.resizeColorbox();
                    }
                },
                onOpen: function(e) {
                    //Enable/Disable stop slideshow on mouse hover
                    if(popupHoverStop){
                        var timeoutId = 0;
                        $('#cboxContent').hover(function(){
                            clearTimeout(timeoutId);
                            $('.cboxSlideshow_on #cboxSlideshow').click();
                        },function(){
                            clearTimeout(timeoutId);
                            timeoutId = setTimeout(function(){
                                $('.cboxSlideshow_off #cboxSlideshow').click();
                            },slideshowSpeed);
                        })
                    }
                },
                onClosed: function(){
                    self.popup_opened_image = false;
                    self.clearPopUpHash();
                }
            });



            $(window).resize(function(){
                self.resizeColorbox();
            });

            $('#cboxOverlay').removeClass().addClass($this.data('popup-theme')+'-overlay');
            $('#colorbox').removeClass().addClass($this.data('popup-theme'));


            //Enable gallery swipe for touchscreen
            addGallerySwipeCallback(function(e){
                if(!self.popup_opened_image) return;
                if(e.deltaX < 0){
                    $("#cboxNext").click();
                }else{
                    $("#cboxPrevious").click();
                }
            });

            generateOverlayColor('#cboxOverlay', popupBackground, popupOverlayTransper, true);
        }

        if(popupType == 'pretty-photo') {

            if(!this.prettyPhotoInit) {

                $prettyPhoto = this.$container
                    .find(".grid-gallery-photos > a[data-rel^='prettyPhoto'], .grid-gallery-photos .hi-icon-wrap > a[data-rel^='prettyPhoto']")
                    .prettyPhoto({
                        hook: 'data-rel',
                        theme: 'light_square',
                        allow_resize: true,
                        allow_expand: true,
                        deeplinking: false,
                        slideshow:  slidePlay && this.$container.data('popup-slideshow-speed'),
                        autoplay_slideshow: slidePlayAuto,
                        social_tools: '',
                        default_width: popupMaxWidth,
                        default_height: popupMaxHeight,
                        getImageDimensions : getImageDimension,
                        getTitle : function(){
                        },
                        changepicturecallback: function(element){
                            self.changePopUpHash(element.attr('id') || element.attr('data-id'));
                            self.popup_opened_image = element;
                            $('.pp_close').text(self.popupTranslates.close);
                            $('.pp_description').html(self.getPopupTitle(element)).show();
                            //add social share buttons if enabled
                            self.addSocialShareToPopUp(element,$('.pp_hoverContainer'),'popup');
                            if(!slidePlay){
                                $('.pp_play').hide();
                            }

                            //Enable/Disable stop slideshow on mouse hover
                            if(popupHoverStop){
                                $('.pp_hoverContainer').hover(function(){
                                    $('.pp_nav .pp_pause').click();
                                },function(){
                                    $('.pp_nav .pp_play').click();
                                })
                            }
                            var $_desc = $('.pp_description'),
                                desc_height = parseInt($_desc.height()),
                                desc_line_height = parseInt($_desc.css('font-size'));
                            if(desc_line_height < desc_height){
                                $('.pp_content').height($('.pp_content').height() +
                                    desc_height - desc_line_height);
                            }
                        },
                        callback: function(){
                            self.popup_opened_image = false;
                            self.clearPopUpHash();
                        }
                    });
                $(window).resize(function(){
                    if(!self.popup_opened_image) return;
                    $.prettyPhoto.open(self.popup_opened_image);
                });
                this.prettyPhotoInit = true;
            } else {
                $.prettyPhoto.refresh();
            }

            //Enable gallery swipe for touchscreen
            addGallerySwipeCallback(function(e){
                if(!self.popup_opened_image) return;
                if(e.deltaX < 0){
                    $("a.pp_arrow_next").click();
                }else{
                    $("a.pp_arrow_previous").click();
                }
            });

            generateOverlayColor('.pp_overlay', popupBackground, popupOverlayTransper, true);
        }

        if(popupType == 'photobox') {
            var photoBoxItemSelector = 'a.pbox:visible';
            // for popup "Display only first image"
            if(this.$container.hasClass('one-photo') || this.$container.hasClass('hidden-item')) {
                photoBoxItemSelector = 'a.pbox';
            }

            if (this.initPhotobox) {
                this.$container.find('.grid-gallery-photos').photobox('destroy');
            }
            this.initPhotobox = true;
            this.$container.find('.grid-gallery-photos').photobox(photoBoxItemSelector, {
                autoplay: slidePlayAuto,
                thumb: (this.$container.data('icons')) ? function(link) {
                    return link.closest('.grid-gallery-caption').find('img')[0]
                } : null,
                getTitle: function(el){
                    return self.getPopupTitle($(el));
                },
                beforeShow: function(element){
                    self.changePopUpHash($(element).attr('id') || $(element).attr('data-id'));
                    self.addSocialShareToPopUp($(element),$('#pbCaption'),'photobox',true);
                },
                afterClose: function(){
                    self.clearPopUpHash();
                }
            });

            //Hide autoplay button when slideshow = false
            if(!this.$container.data('popup-slideshow')){
                $("#pbAutoplayBtn").hide();
            }

            //Enable/Disable stop slideshow on mouse hover
            if(popupHoverStop){
                $('.pbWrapper img').hover(function(){
                    $('#pbOverlay .playing').click();
                },function(){
                    $('#pbOverlay .play').click();
                })
            }

            //Enable gallery swipe for touchscreen
            addGallerySwipeCallback(function(e){
                if(e.deltaX < 0){
                    $("#pbNextBtn").click();
                }else{
                    $("#pbPrevBtn").click();
                }
            });

            generateOverlayColor('#pbOverlay', popupBackground, popupOverlayTransper);
        }
    });

    Gallery.prototype.preventImages = (function() {
        var popupType = this.$container.data('popup-type');

        if (popupType == 'disable') {
            this.$container.find('a.gg-link').off('click');
            this.$container.find('a.gg-link:not([data-type=link])').addClass('disabled');
            this.$container.on('click', 'a.gg-link', function(event) {
                if ($(this).data('type') !== 'link') {
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
        }
    });

    Gallery.prototype.getResponsiveColumnsNumber = function() {
        var columnsData = this.$container.data('responsive-colums'),
            settings = [],
            columnsNumber = parseInt(this.$container.data('columns-number'));

        for (var key in columnsData) {
            settings.push(columnsData[key]);
        }

        settings.sort(function(a, b) {
            a.width = Number(a.width);
            b.width = Number(b.width);
            if (a.width > b.width) {
                return 1;
            } else if (a.width < b.width) {
                return -1;
            } else {
                return 0;
            }
        });

        for (var i = 0,
                 len = settings.length,
                 windowWidth = $(window).width(),
                 minBreakpoint = 0; i < len; i++) {
            if (windowWidth > minBreakpoint && windowWidth <= settings[i].width) {
                columnsNumber = Number(settings[i].columns);
                break;
            }
            minBreakpoint = settings[i].width;
        };

        return columnsNumber;
    };

    Gallery.prototype.initRowsMode = function() {
        var columnsNumber = parseInt(this.$container.data('columns-number'));

        if (this.$container.data('horizontal-scroll')) {
            return;
        }

        if (typeof this.$container.data('responsive-colums') == 'object') {
            columnsNumber = this.getResponsiveColumnsNumber();
        }

        if (columnsNumber) {
            var containerWidth = parseInt(this.$container.width()),
                spacing = parseInt(this.$container.data('offset')),
                scaleHeight = parseInt(this.$container.data('width')) / parseInt(this.$container.data('height')),
                elementWidth = null,
                elementHeight = null;

            elementWidth = Math.floor((this.$container.width() - (columnsNumber - 1) * spacing) / columnsNumber);
            elementHeight = Math.floor(elementWidth / scaleHeight);

            this.$elements.each(function() {
                var $this = $(this);
                if (!$this.find('.post-feed-crop').length) {
                        $this.css('width', elementWidth);
                    if (!isNaN(elementHeight)) {
                        $this.css('height', elementHeight);
                    } else {
                        $this.css('height', 'auto');   
                    }
                } else {
                    $this.find('figcaption').css('width', elementWidth);
                }
            });

            this.$elements.find('.crop').css({
                width: 'auto',
                height: 'auto'
            });
        }
    };

    Gallery.prototype.setImagesHeight = (function () {
        var $images = this.$container.find('img');

        if ($images != undefined && $images.length > 0) {
            $images.each(function () {
                var $image = $(this),
                    $wrapper = $image.parent();

                if ($image.height() < $wrapper.height()) {
                    $wrapper.css('height', $image.height());
                }
            });
        }
    });

    Gallery.prototype.setOverlayTransparency = (function () {
        this.$elements.find('figcaption, [class*="caption-with-icons"]').each(function () {
            var $caption = $(this),
                alpha    = (10 - parseInt($caption.data('alpha'), 10)) / 10,
                rgb      = $caption.css('background-color'),
                rgba     = rgb.replace(')', ', ' + alpha + ')').replace('rgb', 'rgba');


            $caption.css('background', rgba);
        });
    });

    Gallery.prototype.setIconsPosition = (function () {
        this.$elements.each(function () {
            var $element = $(this),
                $wrapper = $element.find('div.hi-icon-wrap'),
                $icons   = $element.find('a.hi-icon');

            $icons.each(function () {
                var $icon   = $(this),
                    marginY = ($element.height() / 2) - ($icon.height() / 2) - 10,
                    marginX = $wrapper.data('margin');

                $icon.css({
                    'margin-top':   Math.abs(marginY),
                    'margin-left':  marginX,
                    'margin-right': marginX
                });
            });
        });
    });

    Gallery.prototype.initCategories = (function () {
        var $defaultElement = this.$navigation.find('a[data-tag="__all__"]'),
            $elements = this.$navigation.find('a'),
            $defaultBackground = $elements.first().css('background-color');

        function shadeColor(color, percent) {
            var f=parseInt(color.slice(1),16),t=percent<0?0:255,p=percent<0?percent*-1:percent,R=f>>16,G=f>>8&0x00FF,B=f&0x0000FF;
            return "#" + (0x1000000+(Math.round((t-R)*p)+R)*0x10000+(Math.round((t-G)*p)+G)*0x100+(Math.round((t-B)*p)+B)).toString(16).slice(1);
        }

        bg = shadeColor('#' + this.rgb2hex($elements.first().css('borderTopColor')), 0.3);

        this.$navigation.find('a').on('click', $.proxy(function (event) {
            event.preventDefault();

            var $category   = $(event.currentTarget),
                requested   = String($category.data('tag')),
                _defaultTag = '__all__',
                currentGallery = this.$navigation.parent().attr('id');

            $elements.css('background-color', $defaultBackground);
            $category.css('background-color', bg);

            if (requested == _defaultTag) {

                this.$elements.each(function () {
                    if ($(this).parent().attr('rel')) {
                        $(this).parent().attr('rel', 'prettyPhoto['+currentGallery+']');
                    }
                }).fadeIn();

                this.correctMargin();
                this.initWookmark();

                if (!this.isFluidHeight() && this.$qsEnable) {
                    this.callQuicksand(this.$qsHolder, this.$qsData, this.$qsDuration);
                }
                return false;
            }

            if (!this.isFluidHeight() && this.$qsEnable) {
                var $filteredData = this.$qsData.filter(function () {
                    var tags = $(this).children().data('tags');
                    if (typeof tags !== 'undefined') {
                        tags = tags.split('|');
                    }
                    return ($.inArray(requested, tags) > -1);
                });
                this.callQuicksand(this.$qsHolder, $filteredData, this.$qsDuration);
            } else {
                $hidden = $();
                $visible = $();
                this.$elements.each(function () {
                    var $element = $(this),
                        tags     = $element.data('tags');

                    if (typeof tags != 'string') {
                        tags = String(tags);
                    }

                    if (tags != undefined) {
                        tags = tags.split('|');
                    }
                    if ($.inArray(requested, tags) > -1) {
                        if ($element.parent().attr('rel')) {
                            $element.parent().attr('rel', 'prettyPhoto['+currentGallery+'-'+requested+']');
                        }
                        $visible.push(this);
                    } else {
                        $hidden.push(this);
                    }
                });

                $.when($hidden.fadeOut()).done($.proxy(function(){
                    $visible.fadeIn();
                    this.correctMargin();
                    this.initWookmark();
                }, this));
            }

        }, this));

        $elements.first().trigger('click');
    });

    Gallery.prototype.callQuicksand = function($holder, $filteredData, duration) {
        self = this;

        $filteredData.find('figure.grid-gallery-caption').css('margin', '0 ' + this.initialMargin + ' ' + this.initialMargin + ' 0').parent().css('clear', 'none');

        $holder.quicksand($filteredData, {
                duration: Number(duration),
                easing: 'swing',
                attribute: 'href',
            }, function() {
                $holder.css({
                    width: 'auto',
                    height: 'auto'
                }).append('<div class="grid-gallery-clearfix"></div>');
                self.initPopup();
                self.correctMargin();
            }
        );
    };

    Gallery.prototype.hidePopupCaptions = function() {
        //never show alternative text for popup theme 6 on top of popup
        $('<style type="text/css">.ppt{ display:none!important; }</style>').appendTo("head");
        if (this.$container.data('popup-captions') == 'hide') {
            $('<style type="text/css">#cboxTitle, #cboxCurrent, .pbCaptionText, .ppt, .pp_description { display:none!important; }</style>').appendTo("head");
        }
    };

    Gallery.prototype.hidePaginationControls = (function () {
        return false;
    });

    Gallery.prototype.setImageOverlay = (function() {
        if(this.isImageOverlay()) {
            this.$container.find('.grid-gallery-caption').each(function () {
                var image = $(this).find('img');
                var crop = $(this).find('.image-overlay');
                image.css('opacity', '0.2');
                crop.css('background-color', '#424242');
                $(this).on('mouseenter', function () {
                        image.css('opacity', '1.0');
                        crop.css('background-color', 'inherit');
                    }
                );
                $(this).on('mouseleave', function () {
                    image.css('opacity', '0.2');
                    crop.css('background-color', '#424242');
                });
            });
        }
    });

    Gallery.prototype.setMouseShadow = (function() {
        var shadow = null,
            $selector = null,
            $captions = this.$container.find('.grid-gallery-caption'),
            boxShadow = $captions.filter(':first').css('box-shadow'),
            showOver = function(event) {
                if (event.type === 'mouseenter') {
                    $(this).css('box-shadow', boxShadow);
                } else {
                    $(this).css('box-shadow', 'none');
                }
            },
            hideOver = function(event) {
                if (event.type === 'mouseenter') {
                    $(this).css('box-shadow', 'none');
                } else {
                    $(this).css('box-shadow', boxShadow);
                }
            };

        if ($captions.is('.shadow-show')) {
            $captions.css('box-shadow', 'none');
            $captions.on('hover', showOver);
        } else if ($captions.is('.shadow-hide')) {
            $captions.on('hover', hideOver);
        }
    });

    Gallery.prototype.initPagination = (function () {
        var perPage = parseInt(this.$container.find('.grid-gallery-photos').data('per-page'), 10),
            buffer  = [],
            page    = 1,
            offset  = 0
        self    = this;

        if (isNaN(perPage)) {
            this.$elements.fadeIn();
            return false;
        }

        var showCurrentPage = (function (gallery) {
            gallery.$elements.removeClass('current-page').hide(350);

            $.each(buffer[gallery.pagination.currentPage], function () {
                $(this).addClass('current-page').show(function () {
                    gallery.setIconsPosition();
                    self.correctMargin();
                });
            });
            /*
             if (!gallery.isFluidHeight()) {
             $('.current-page .crop').css('height', function () {
             var height = null;
             $('.crop img').each(function () {
             if($(this).height() && !height) {
             height = $(this).height();
             }
             });
             return height;
             });
             }
             */
        });

        this.pagination.limit = perPage;

        this.$elements.each($.proxy(function (index, el) {
            var currentIndex = index + 1;

            if ((currentIndex - offset) <= this.pagination.limit) {
                if (!$.isArray(buffer[page])) {
                    buffer[page] = [];
                }

                buffer[page].push(el);
            } else {
                offset += this.pagination.limit;
                page   += 1;

                buffer[page] = [el];
            }
        }, this)).hide();

        this.pagination.pages = Math.ceil(this.pagination.total / this.pagination.limit);

        var element=this.pagination.$wrapper.find('a.grid-gallery-page[data-page="1"]');
        element.css('font-size','19pt');

        this.pagination.$wrapper.find('a.grid-gallery-page').on('click', $.proxy(function (e) {
            e.preventDefault();

            var element = $(e.currentTarget);
            var galery = Gallery.prototype;
            this.pagination.$wrapper.find('a.grid-gallery-page').each(function() {
                $(this).css('font-size','inherit');
            });
            galery.selectedCategory = element.data('page');
            element.css('font-size','19pt');

            var $anchor       = $(e.currentTarget),
                requestedPage = $anchor.data('page');

            this.pagination.currentPage = requestedPage;

            showCurrentPage(this);

            return false;
        }, this));

        showCurrentPage(this);
    });

    Gallery.prototype.hex=function(x) {
        return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
    };

    Gallery.prototype.rgb2hex = function(rgb) {
        if(rgb) {
            rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(0\.\d+))?\)$/);
            function hex(x) {
                return ("0" + parseInt(x).toString(16)).slice(-2);
            }
            return hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
        }
    };

    Gallery.prototype.hex2rgb = function(hex) {

        var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
        hex = hex.replace(shorthandRegex, function(m, r, g, b) {
            return r + r + g + g + b + b;
        });

        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    };

    Gallery.prototype.loadFontFamily = (function () {
        font = this.$container.data('caption-font-family');
        if (font && font !== 'Default') {
            WebFont.load({
                google: {
                    families: [font + ':400,800']
                }
            });
        }
    });

    Gallery.prototype.initCaptionCalculations = (function () {
        var self = this;

        this.$container.find('.grid-gallery-caption').each(function () {
            wrap = $(this).find('div.grid-gallery-figcaption-wrap');
            figcaption = $(this).find('figcaption');

            wrap.css({
                'display': 'table-cell',
                'text-align': figcaption.css('text-align')
            });

            wrap.wrap($('<div>', {
                css: {
                    display:'table',
                    height:'100%',
                    width:'100%'
                }
            }));
        });
    });

    Gallery.prototype.checkDirection = function($element, e) {
        var w = $element.width(),
            h = $element.height(),
            x = ( e.pageX - $element.offset().left - ( w / 2 )) * ( w > h ? ( h / w ) : 1 ),
            y = ( e.pageY - $element.offset().top - ( h / 2 )) * ( h > w ? ( w / h ) : 1 );

        return Math.round(( ( ( Math.atan2(y, x) * (180 / Math.PI) ) + 180 ) / 90 ) + 3) % 4;
    };

    Gallery.prototype.initCaptionEffects = (function () {
        var self = this,
            allwaysShowCaptionOnMobile = this.$container.data('caption-mobile'),
            isMobile = !!parseInt(this.$container.data('is-mobile'));

        function generateOverlayColor(overlayColor, alpha) {
            if(typeof(overlayColor) == 'string'){
                overlayColor = overlayColor.split(')')[0].split('(');
                return overlayColor[0] + 'a(' + overlayColor[1] + ', ' + (1 - alpha/10) + ')';
            } else {
                return overlayColor;
            }
        };

        $.each(this.$elements, function(index, el) {
            var $el = $(el),
                overlayColor = $el.find('figcaption').css('backgroundColor'),
                alpha = parseInt($el.find('figcaption').data('alpha'));

            if (isMobile && allwaysShowCaptionOnMobile){
                $el.attr('data-grid-gallery-type', 'none');
            }

            if ($el.data('grid-gallery-type') == 'cube') {
                $el.on('mouseenter mouseleave', function(e) {
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

                    $figcaption.removeClass().addClass(classHelper);
                });
            }

            if ($el.data('grid-gallery-type') == 'polaroid') {
                if (!$(this).find('.post-feed-crop').length && !$el.hasClass('initialized')) {
                    $el.addClass('initialized');

                    var width = $el.width(),
                        frameWidth = parseInt(self.$container.data('polaroid-frame-width'), 10) || 20,
                        $img = $(this).find('img'),
                        $figcaption = $(this).find('figcaption'),
                        scaleRatio = $img.width() / $img.height(),
                        imageWidth = $img.width() - frameWidth * 2,
                        imageHeight = imageWidth / scaleRatio;

                    $img.css({
                        'width': imageWidth + 'px',
                        'height': imageHeight + 'px',
                        'margin': frameWidth + 'px auto 0',
                    });

                    $(this).find('.crop').css({
                        'height': imageHeight + frameWidth + 'px',
                    });

                    $(this).css({
                        'background': overlayColor
                    })

                    $(this).css({
                        'width': $(this).width(),
                        'background': generateOverlayColor(overlayColor, alpha)
                    });

                    $figcaption.css({
                        'padding': frameWidth + 'px',
                        'background': 'none'
                    });

                    if ($figcaption.find('.grid-gallery-figcaption-wrap').text().length === 0) {
                        $figcaption
                            .find('.grid-gallery-figcaption-wrap')
                            .append('<span></span>');
                    }


                    if (self.$container.data('polaroid-animation')) {
                        $el.addClass('polaroid-animation');
                    }

                    if (self.$container.data('polaroid-scattering')) {
                        $(this).css({
                            'transform': 'rotate(' + (-3 + Math.random() * (10 - 3)) + 'deg)'
                        });
                        $el.addClass('polaroid-scattering');
                    }
                }
            }

            if ($el.data('grid-gallery-type') == 'direction-aware') {
                var color = $el.find('figcaption').css('color'),
                    align = $el.find('figcaption').css('text-align');

                $el.attr('data-caption', '<div style="padding:20px;font-family:' +
                    self.$container.data('caption-font-family') + '; font-size:' + self.$container.data('caption-text-size') +'">' +
                    ($el.find('.gg-image-caption').html() || '') + '</div>');

                $el.sliphover({
                    target: $el,
                    backgroundColor: generateOverlayColor(overlayColor, alpha),
                    fontColor: color,
                    textAlign: align,
                    caption: 'data-caption'
                });

            }

            if ($el.data('grid-gallery-type') == '3d-cube'){
                var cubeWidth = $el.width(),
                    cubeHeight = $el.height();
                // $el.addClass('box-3d-cube-scene');
                $el.children('div').addClass('front').addClass('face');
                $el.children('figcaption').addClass('back').addClass('face');
                $el.html('<div class="box-3d-cube-scene"><div class="box-3d-cube">' + $el.html() + '</div></div>');
                // $el.html("<div class='box-3d-cube-scene'><div class='box-3d-cube'><div class='front face'><img src='http://placehold.it/"+cubeWidth+"x"+cubeHeight+"/' alt=''></div><div class='back face'><div>This is back</div></div></div></div>");

                var perspective = Math.max(cubeHeight,cubeWidth) * 2;
                var transformOrigin = '50% 50% -' + Math.round(cubeHeight/2) + 'px';
                $el.find('.box-3d-cube-scene').css({
                    perspective: perspective,
                    '-webkit-perspective': perspective
                });
                $el.find('.box-3d-cube').css({
                    'transform-origin': transformOrigin,
                    '-ms-transform-origin': transformOrigin,
                    '-webkit-transform-origin': transformOrigin,
                }).add('.box-3d-cube .face').css({
                    width: cubeWidth,
                    height: cubeHeight
                });
            }
        });

        $(document).on('click', '.sliphover-container', function(event) {
            event.preventDefault();
            $(this).data('relatedElement').get(0).click();
        });

        var popupType = this.$container.data('popup-type');


        this.$container.find('.grid-gallery-caption').each(function() {
            var caption = this,
                $caption = $(caption),
                hammer = new Hammer_gg(this);

            hammer.on("tap panstart", function(event) {
                if (event.type === 'panstart') {
                    self.$container.find('.grid-gallery-caption').removeClass('hovered');
                }

                if (event.type === 'tap') {
                    if (!$caption.hasClass('hovered')) {
                        event.preventDefault();
                        self.$container.find('.grid-gallery-caption').not(caption).removeClass('hovered');
                        $(caption).addClass('hovered');
                    }
                }
            });
        });

    });

    Gallery.prototype.correctMargin = (function () {
        if(!this.isFluidHeight() && !this.$container.data('horizontal-scroll')) {

            if (this.$qsEnable) {
                this.$elements = this.$container.find('figure.grid-gallery-caption');
            };

            var prevElement = null
                ,	totalElements = this.$elements.filter(':visible').size()
                ,   rowWidth = 0
                ,   maxRowWidth = this.$container.width()
                ,   initialMargin = this.initialMargin;

            this.$elements.css('margin', '0 ' + this.initialMargin + ' ' + this.initialMargin + ' 0');
            this.$elements.parent().css('clear', 'none');

            this.$elements.filter(':visible').each(function(index){

                if (rowWidth + $(this).outerWidth() > maxRowWidth) {
                    $(prevElement).css('margin-right', 0);
                    $(this).css('margin-right', this.initialMargin);
                    $(this).parent().css('clear', 'left');
                    rowWidth = $(this).outerWidth() + parseInt(initialMargin);
                } else if (rowWidth + $(this).outerWidth() == maxRowWidth) {
                    $(this).css('margin-right', 0);
                    rowWidth = 0;
                } else {
                    rowWidth += $(this).outerWidth() + parseInt(initialMargin);
                }

                if(index == totalElements - 1) {
                    $(this).css('margin-right', 0);
                }

                prevElement = this;

            });
        }
    });

    Gallery.prototype.hideTitleTooltip = (function () {
        if(this.$container.data('hide-tooltip') == true) {
            title = '';
            this.$container.find('a, img, div').on('mouseenter', function() {
                title = $(this).attr('title');
                $(this).attr({'title':''});
            }).mouseout(function() {
                $(this).attr({'title':title});
            });
        };
    });

    Gallery.prototype.correctFullscreen = (function () {
        var windowWidth = $(window).width();
        this.$elements.each(function() {
            var coef = parseInt(windowWidth / $(this).width())
                , resultWidth = Math.round(windowWidth / coef);
            $(this).width(resultWidth);
        });
    });

    Gallery.prototype.correctFullScreenWidthGallery = (function(){
        var windowWidth = $(window).width(),
            $parentContainer = this.$container.parent(),
            containerOffset = $parentContainer.offset(),
            containerOffsetLeft = containerOffset.left + parseFloat($parentContainer.css('padding-left'));

        this.$container.find('.grid-gallery-photos').css({
            width: windowWidth
        });

        var cssDirection = this.$container.css('direction');

        if ('ltr' == cssDirection) {
            this.$container.css({
                width: windowWidth,
                left: '-' + containerOffsetLeft + 'px'
            });
        } else {
            this.$container.css({
                width: windowWidth
            }).offset(function(i, coords) {
                return {'top' : coords.top, 'left' : 0};
            });
        }
    });

    Gallery.prototype.getOriginalImageSizes = function (img) {

        var tempImage = new Image(),
            width,
            height;

        if ('naturalWidth' in tempImage && 'naturalHeight' in tempImage) {
            width = img.naturalWidth;
            height = img.naturalHeight;
        } else {
            tempImage.src = img.src;
            width = tempImage.width;
            height = tempImage.height;
        }

        return {
            width: width,
            height: height,
        };
    };

    Gallery.prototype.initHorizontalMode = (function () {
        var horizontalScroll = this.$container.data('horizontal-scroll'),
            height = this.$container.data('height'),
            width = this.$container.data('width'),
            offset = this.$container.data('offset'),
            self = this;

        if (!horizontalScroll) {
            return;
        }

        //Calculate max-height and margin
        if (!height) {
            var elementsHeight = this.$container.find('.grid-gallery-caption>a').map(function() {
                    return $(this).height();
                }).get(),
                height = Math.max.apply(null, elementsHeight);
        } else {
            if(offset && offset > 0){
                height = height + offset*2;
            }
        }

        if (width === 'auto') {
            this.$elements.each(function(index, el) {
                var $figure = $(el),
                    $image = $figure.find('img');
                    sizes = self.getOriginalImageSizes($image.get(0));
                    $image.css('max-width', 'none');
                $figure.width(Math.floor((height / sizes.height) * sizes.width));
            });
        }

        //Fixed IE9 scroll bug
        var isIE9OrBelow = function() {
            return /MSIE\s/.test(navigator.userAgent) && parseFloat(navigator.appVersion.split("MSIE")[1]) < 10;
        }

        if(isIE9OrBelow()){
            this.$container.find('.grid-gallery-photos > *').css('display','table-cell');
        } else {
            this.$container.find('.grid-gallery-photos > *').css('display','inline-block');
        }

        this.$container.find('.grid-gallery-photos > *').css({
            margin:0,
            padding:0,
            float: 'none',
            animate: true,
            'vertical-align': 'middle',
            clear: 'right',
            'margin-right': '-5px',
            'border': 'none',
            'max-width': 'none',
        });

        this.$container.find('.grid-gallery-photos .grid-gallery-caption').css({
            float: 'none',
        });

        // https://github.com/lanre-ade/jQuery-slimScroll
        height = height + 7; //This is scrollbar height;
        var slimScroll = this.$container.find('.grid-gallery-photos').slimScroll({
            height: height,
            width: 'auto', 
            railVisible: true,
            alwaysVisible: true,
            allowPageScroll: true,
            axis: 'x',
            animate: true,
            color: horizontalScroll.color || '#000',
            opacity:(100 - horizontalScroll.transparency) * 0.01,
        });

        // Load more height fix
        if (slimScroll.height() < height) {
            slimScroll.height(height);
            slimScroll.parent().height(height);
        }

    });

    Gallery.prototype.initHorizontalGalleryType = (function () {
        if (this.$container.data('height') && String(this.$container.data('height')).indexOf('%') > -1) {
            var height = this.$elements.first().height();
            this.$elements.find('img').css({
                'max-height': height,
                'min-height': height,
            });
        }
    });

    Gallery.prototype.hidePreloader = function() {
        var preloadEnab = this.$container.attr('data-preloader'),
            preloader = this.$container.find('.gallery-loading'),
            galleryPhotos = this.$container.find('.grid-gallery-photos');

        preloader.hide();
        if(preloadEnab !== '' && preloadEnab === 'true') {
            galleryPhotos.show().fadeTo("slow", 1, function() {
                galleryPhotos.css('opacity','1');
            });
        } else {
            galleryPhotos.show().fadeTo('fast', 1, function() {
                galleryPhotos.css('opacity','1');
            });
        }
    };

    Gallery.prototype.showGalleryParts = function(){
        this.$container.children('.hidden-item').removeClass('hidden-item');
    };

    Gallery.prototype.$getImagesFigureContainer = function(){
        return this.$container.find('figure.grid-gallery-caption');
    };

    Gallery.prototype.initSocialSharing = function(){
        if(!this.socialSharing || !this.socialSharing.enabled){
            return;
        }
        this.initGallerySocialSharing();
        this.initImageSocialSharing();
    };

    Gallery.prototype.initEvent = function($elements){
        $elements.find('.supsystic-social-sharing a.social-sharing-button').on('click',function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            if (e.currentTarget.href.slice(-1) !== '#') {
                window.open(e.currentTarget.href, 'mw' + e.timeStamp, 'left=20,top=20,width=500,height=500,toolbar=1,resizable=0');
            }
        });
    };

    Gallery.prototype.getSocialButtons = function(wrapper_class, url, img_id, img_src, title) {
        title = title || null;

        var html = $('#social-share-html').html();

        if (html !== undefined && html.length){
            html = html.replace(/{url}/g, url).replace(/{title}/g, title);
        }
        
        return $('<div>', {
            class: wrapper_class,
            'data-img-thumbnail': img_src,
            'data-img-id': img_id,
            'data-img-title': title,
        }).html(html);
    };

    Gallery.prototype.getSocialButtonsByImage = function(wrapper_class, $element, popup) {

        var $img = $element.find('img'),
            imgSrc = $element.attr('href'),
            title = $element.attr('title'),
            $captionContainer = $element.find('.gg-image-caption'),
            url = location.href,
            imageId = $element.attr('id').split('-').pop();

        if ($captionContainer.length) {
            var caption = $.trim(
                $captionContainer.clone().html($captionContainer.html()
                    .replace(/<br\s*[\/]?>/gi, ' '))
                    .text()
                    .replace(/\s+/, ' ')
            );

            if (caption.length) {
                title = caption;
            }
        }

        if (imgSrc && imgSrc.indexOf('http') !== 0) {
            imgSrc = 'http:' + imgSrc;
        }

        function updateQueryStringParameter(uri, key, value) {
            var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
            var separator = uri.indexOf('?') !== -1 ? "&" : "?";
            if (uri.match(re)) {
                return uri.replace(re, '$1' + key + "=" + value + '$2');
            } else {
                return uri + separator + key + "=" + value;
            }
        }

        if (popup) {
            url = url.replace('#!', '?');
        }

        url = encodeURIComponent(updateQueryStringParameter(url, 'shared-image', imageId));

        return this.getSocialButtons(wrapper_class, url, $element.attr('id'), imgSrc, title);
    };

    Gallery.prototype.initGallerySocialSharing = function() {

        var gallerySharing = this.socialSharing.gallerySharing;

        if (!parseInt(gallerySharing.enabled)) {
            return;
        }

        var $socialButtons = this.getSocialButtons(
                '',
                this.socialButtonsUrl,
                '',
                '',
                this.$container.data('title')
            );


        if (gallerySharing.position == 'top' || gallerySharing.position == 'all') {
            var buttons = $('#gallery-sharing-top')
                .html($socialButtons.html())
                .find('.supsystic-social-sharing');
            window.initSupsysticSocialSharing(buttons);
        }

        if (gallerySharing.position == 'bottom' || gallerySharing.position == 'all'){
            var buttons = $('#gallery-sharing-bottom')
                .html($socialButtons.html())
                .find('.supsystic-social-sharing');
            window.initSupsysticSocialSharing(buttons);
        }

        this.initEvent($('#gallery-sharing-top,#gallery-sharing-bottom'));
    };

    //init social share for all images in gallery
    Gallery.prototype.initImageSocialSharing = function(){

        var imageSharing = this.socialSharing.imageSharing;

        if(!parseInt(imageSharing.enabled)){
            return;
        }

        var $images = this.$getImagesFigureContainer(),
            socialButtonsClass = 'supsystic-grid-gallery-image-sharing ' + imageSharing.wrapperClass,
            self = this,
            iconsEnabled = this.$container.data('icons');


        $images.each(function() {
            var $this = $(this),
                $el;

            if (!iconsEnabled) {
                $el = $this.parent();
            } else {
                $el = $this.find('a.hi-icon');
            }

            $this.append(
                self.getSocialButtonsByImage(socialButtonsClass, $el)
            );
        });

        this.correctImageSocialButtons($images.children("." + this.socialSharingWrapperClass));
        this.initEvent($images.children("." + this.socialSharingWrapperClass));
    };

    Gallery.prototype.correctImageSocialButtons = function($imageSharing){

        if(!$imageSharing.size()){
            return;
        }
        var $example = $imageSharing.eq(0),
            correctCss = {};

        if($example.hasClass('vertical')){
            var buttonWidth = $example.find('.social-sharing-button').eq(0).outerWidth();
            correctCss.width = buttonWidth;
            $example.width(buttonWidth);
        }

        var width = $example.width();
        var height = $example.height();

        if($example.hasClass('center')){
            $.extend(correctCss,{'margin-left': '-' + (width/2) + 'px'})
        }
        if($example.hasClass('middle')){
            $.extend(correctCss,{'margin-top': '-' + (height/2) + 'px'})
        }

        $imageSharing.find('.social-sharing-button.print').on('click',function(){
            var image_url = $(this).closest('.supsystic-grid-gallery-image-sharing').data('img-url');
            window.open(image_url).print();
        });

        $imageSharing.find('.social-sharing-button.mail').on('click',function(){
            var $infoElement = $(this).closest('.supsystic-grid-gallery-image-sharing'),
                image_id = $infoElement.data('img-id'),
                image_title = $infoElement.data('img-title');
            var url = window.location.href.replace(window.location.hash,'') + '#' + image_id + '/';

            var src = 'mailto:adresse@example.com?subject=' + encodeURIComponent(document.title) + ',' + image_title + '&body=' + url;
            var iframe = $('<iframe id="mailtoFrame" src="' + src + '" width="1" height="1" border="0" frameborder="0"></iframe>');

            $('body').append(iframe);
            window.setTimeout(function(){
                iframe.remove();
            }, 500);
        });

        var self = this;
        $imageSharing.each(function(){

            $(this).css(correctCss);

            var thumbnail = $(this).data('img-thumbnail');

            if (thumbnail) {

                for(var sharingClass in self.socialSharingImageOperators){
                    var $button = $(this).find('.social-sharing-button.'+sharingClass);
                    if($button.size()){
                        var img_url = $(this).data('img-url'),
                            img_id = $(this).data('img-id'),
                            href =  $button.attr('href').replace(
                                img_url,
                                self.addPopUpHashToUrl(self.socialButtonsUrl, img_id)
                            ) +
                            '&' +
                            self.socialSharingImageOperators[sharingClass] +
                            '=' +
                            thumbnail;

                        $button.attr('href', href);
                    }
                }
            }
        });

    };

    Gallery.prototype.addSocialShareToPopUp = function($element, $wrapper, addClass, fixed) {

        if(!this.socialSharing.enabled || !parseInt(this.socialSharing.popupSharing.enabled)){
            return;
        }

        var buttonsClass = 'supsystic-grid-gallery-image-sharing ' + addClass;

        if (!fixed) {
            buttonsClass +=' ' + this.socialSharing.popupSharing.wrapperClass;
        }

        $wrapper.find('.supsystic-grid-gallery-image-sharing').remove();
        $wrapper.prepend(this.getSocialButtonsByImage(buttonsClass, $element, true));

        this.correctImageSocialButtons($wrapper.find('.supsystic-grid-gallery-image-sharing'));

        this.initEvent($wrapper.children("." + this.socialSharingWrapperClass));
    };

    Gallery.prototype.removePopUpHashFromUrl = function(url){
        var match = url.match(/gg-\d+-\d+/);
        return url.replace(url[url.indexOf(match)-1] + match,"");
    };

    Gallery.prototype.addPopUpHashToUrl = function(url, hash){
        if(hash.length = 0){
            return url;
        }
        var prefix = '?';
        if(url.indexOf(prefix) != -1) prefix = '&';
        return url + prefix + hash;
    };

    Gallery.prototype.openHashPopUp = function(){
        var getElementId = function() {

            var search = window.location.search;
            if (search.match(/gg-\d+-\d+/)) {
                return search.match(/gg-\d+-\d+/);
            }

            var hash = window.location.hash;
            if (hash.match(/gg-\d+-\d+/)) {
                return hash.match(/gg-\d+-\d+/);
            }
        };

        var elementId = getElementId(),
			$element = this.$container.find('#' + elementId + ', [data-id="' + elementId + '"]').first();

        if($element.size()){
			$element.click();
			var $figure;

			if ($element.hasClass('hi-icon')) {
				$figure = $element.closest('figure.grid-gallery-caption');
			} else {
				$figure = $element.children('figure');
			}

            $('html, body').animate({
                scrollTop: $figure.offset().top
            }, 100);
        }
    };

	Gallery.prototype.updateQueryParams = function (url, params) {
		for (var param in params) {

			var re = new RegExp("[\\?&]" + param + "=([^&#]*)"),
				match = re.exec(url),
				delimiter,
				value = params[param];


			if (match === null) {

				var hasQuestionMark = /\?/.test(url);
				delimiter = hasQuestionMark ? "&" : "?";

				if (value) {
					url = url + delimiter + param + "=" + value;
				}

			} else {
				delimiter = match[0].charAt(0);

				if (value) {
					url = url.replace(re, delimiter + param + "=" + value);
				} else {
					url = url.replace(re, '');
					if (delimiter === '?' && url.length) {
						url = '?' + url.substr(1);
					}
				}
			}
		}

		return url;
	};

    Gallery.prototype.changePopUpHash = function(hash){
		this.popupIsOpened = true;

		if (this.ignoreStateChange) {
			this.ignoreStateChange = false;
			return;
		}
		var queryParams = this.updateQueryParams(window.location.search, {'_gallery': hash}),
			stateUrl = window.location.pathname + queryParams;

		this.historyStateChange = true;

		if (!this.popupIsInit) {

			if (queryParams === document.location.search) {

				History.replaceState({
					type: 'sc-gallery',
					hash: hash,
					state: 'close'
				}, document.title, window.location.pathname + this.updateQueryParams(window.location.search, {'_gallery': null}));

				History.pushState({
					type: 'sc-gallery',
					hash: hash,
					state: 'init'
				}, document.title,  stateUrl);

			} else {

				History.replaceState({
					type: 'sc-gallery',
					hash: hash,
					state: 'init'
				}, document.title, stateUrl);
			}

			this.popupIsInit = true;

		} else {

			if (this.disablePopupHistory) {
				History.replaceState({
					type: 'sc-gallery',
					hash: hash,
					state: 'change'
				}, document.title, stateUrl);
			} else {
				History.pushState({
					type: 'sc-gallery',
					hash: hash,
					state: 'change'
				}, document.title, stateUrl);
			}
		}

		this.historyStateChange = false;

	};

    Gallery.prototype.clearPopUpHash = function() {
		this.historyStateChange = true;

		if (this.disablePopupHistory) {
			History.replaceState({
				type: 'sc-gallery',
				hash: '',
				state: 'close'
			}, document.title, window.location.pathname + this.updateQueryParams(window.location.search, {'_gallery': null}));
		} else {
			History.pushState({
				type: 'sc-gallery',
				hash: '',
				state: 'close'
			}, document.title, window.location.pathname + this.updateQueryParams(window.location.search, {'_gallery': null}));
		}

		this.historyStateChange = false;
		this.popupIsOpened = false;
	};

    Gallery.prototype.init = (function () {

        this.$container.imagesLoaded($.proxy(function () {

            var self = this;
            // this.setImagesHeight();
            $(document).trigger("GalleryBeforeInit", this);

            this.hidePreloader();
            this.showCaption();
            this.initRowsMode();
            this.initHorizontalGalleryType();
            this.initQuicksand();

            if(this.$container.attr('data-fullscreen') == 'true') {
                this.correctFullScreenWidthGallery();
                $(window).resize(function() {
                    self.correctFullScreenWidthGallery();
                });
            }

            this.initHorizontalMode();
            this.setOverlayTransparency();
            this.initCaptionCalculations();
            this.initCaptionEffects();
            this.hideTitleTooltip();
            this.initPagination();

            this.initPopup();

            this.setMouseShadow();
            this.setImageOverlay();

            this.loadFontFamily();
            this.hidePopupCaptions();
            this.preventImages();
            this.initWookmark();
            this.initCategories();
            this.setIconsPosition();

            this.correctMargin();
            this.initControll();
            this.showGalleryParts();

            this.initSocialSharing();


            // iOS Safari fix
            setTimeout(function() {
                if (self.wookmark) {
                    self.wookmark.trigger('refreshWookmark');
                }
            }, 500);

            $(document).trigger("GalleryAfterInit", this);

            setTimeout(function(){
                self.openHashPopUp();
            },500);

            // this.openHashPopUp();

            var galleryId = this.$container.attr('id').replace('grid-gallery-', ''),
                openByLinkRegexp = new RegExp('#gg-open-' + galleryId + '(?:-(\\d+))*');

			History.Adapter.bind(window, 'statechange', function(event) {
				var state = History.getState();

				// self.historyStateChange if true means manual update state and we need skip this event
				if (!self.historyStateChange) {

					if ((state.data.type !== 'sc-gallery' && self.popupIsOpened) ||
						(state.data.type === 'sc-gallery' && (state.data.state === 'close' || state.data.state ===  'hashchange') && self.popupIsOpened)
					) {

						if (self.popupType == 'pretty-photo') {
							$.prettyPhoto && $.prettyPhoto.close();
						}

						if (self.popupType == 'colorbox') {
							$.colorbox && $.colorbox.close();
						}

						if (self.popupType == 'photobox') {
							window._photobox && window._photobox.close();
						}

					}

					// On history open image
					if (state.data.type === 'sc-gallery' && state.data.hash && state.data.state !== 'close') {

						self.ignoreStateChange = true;

						var $el = self.$container.find('#' + state.data.hash + ', [data-id="' + state.data.hash + '"]').first();

						if (self.popupIsOpened) {

							if (self.popupType == 'pretty-photo') {
								var href = $el.attr('href'),
									index = $.prettyPhoto.getImagesList().indexOf(href);
								$.prettyPhoto.changePage(index);
							}

							if (self.popupType == 'colorbox') {
								$.colorbox.resizeResponsive($el);
							}

							if (self.popupType == 'photobox') {
								var images = window._photobox.getImages(),
									href = $el.attr('href');

								for (var i = 0; i < images.length; i++) {
									if (images[i][0] == href || images[i][0] == 'http:' + href || images[i][0] == 'https:' + href) {
										window._photobox.changeImage(i);
									}
								}
							}

						} else {
							$el.trigger('click');
						}

					}

				}

			});


            $(window).on('hashchange', function(event) {
				var hash = window.location.hash,
                    matches = openByLinkRegexp.exec(hash);
				if (matches) {

					History.replaceState({
						type: 'sc-gallery',
						hash: '',
						state: 'hashchange'
					}, document.title, window.location.pathname);

                    var position = matches[1] ? 'eq(' + (matches[1] - 1) + ')' : 'first';
                    self.$container.find('.gg-link:' + position + ', .hi-icon:' + position).trigger('click');
                }
            });

        }, this));

        $(window).on('resize', $.proxy(function () {
            this.correctMargin();
        }, this));

        //Add init flag
        this.$container.addClass('initialized');
    });

    window.initGridGallery = (function (el, autoInit) {
        var makeSelector = (function (el) {
            return '#' + el.id;
        });
        return new Gallery(makeSelector(el), autoInit);
    });

    window.contentLoaded = (function() {
        var $galleries = $(".grid-gallery:not('.initialized')"),
            $promise = new $.Deferred().resolve();


        if ($galleries.length > 0) {

            $.each($galleries, (function(i, el) {
                $promise = $promise.then(function() {
                    return new Gallery(el, true);
                });
            }));
        }

        $('.crop').css('display', 'block');

    });

    $(document).ready(function () {
        contentLoaded();
    }).ajaxComplete(function() {
        contentLoaded();
    });

    //if a customer enter an e-mail for image link in gallery he'll get a mailto: link
    $('a.gg-link').each(function(){
        var gLink =  $(this).attr('href');
        var reg= /[0-9a-z_]+@[0-9a-z_]+\.[a-z]{2,5}/i;
        if (isEmail=gLink.match(reg)){
            $(this).attr('href','mailto:'+isEmail[0]);
        }
    });

}(jQuery));