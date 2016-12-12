jQuery(document).ready( function(){

    jQuery('.tab-content:nth-child(2)').addClass('firstelement');
    
    jQuery('#wcp-loader').hide();
    jQuery('#wcp-saved').hide();
    var sCounter = jQuery('#accordion div:last-child').find('button.fullshortcode').attr('id');
    var icons = {
        header: "dashicons dashicons-plus",
        activeHeader: "dashicons dashicons-minus"
    };    

    jQuery( "#accordion" ).accordion({
      collapsible: true,
      icons: icons,
      active: false
    });

    jQuery('.colorpicker').wpColorPicker();
    
    jQuery('#photo-book').on('click', '.save-pages', function(event) {
        event.preventDefault();
        jQuery('#wcp-saved').hide();
        jQuery('#wcp-loader').show();

        var allCaptionHovers = [];

        jQuery('#accordion > div').each(function(index) {
            var widgets = {};

            widgets.shortcode = jQuery(this).find('.fullshortcode').attr('id');
            widgets.counter = jQuery(this).find('.fullshortcode').attr('id');

            widgets.imageurl = jQuery(this).find('.imageurl').val();
            widgets.imagetitle = jQuery(this).find('.imagetitle').val();
            widgets.imagealt = jQuery(this).find('.imagealt').val();
            widgets.wcpilight = jQuery(this).find('.wcpilight').val();
            widgets.imagewidth = jQuery(this).find('.imagewidth').val();
            widgets.imageheight = jQuery(this).find('.imageheight').val();


            widgets.captiontext = jQuery(this).find('.captiontext').val();
            widgets.captionalignment = jQuery(this).find('.captionalignment').val();
            widgets.captionbg = jQuery(this).find('.captionbg').val();
            widgets.captioncolor = jQuery(this).find('.captioncolor').val();
            widgets.captionopacity = jQuery(this).find('.captionopacity').val();
            
            widgets.captionlink = jQuery(this).find('.captionlink').val();
            widgets.captiontarget = jQuery(this).find('.captiontarget').val();

            widgets.borderwidth = jQuery(this).find('.borderwidth').val();
            widgets.bordercolor = jQuery(this).find('.bordercolor').val();

            widgets.refname = jQuery(this).find('.refname').val();
            widgets.hoverstyle = jQuery(this).find('.hoverstyle').val();

            allCaptionHovers.push(widgets);

        });

        // console.log(allbooks);
        var data = {
            action: 'wcp_save_image_caption_hovers',
            widgets: allCaptionHovers,
        }

        jQuery.post(wcpAjax.url, data, function(resp) {
            jQuery('#wcp-loader').hide();
            jQuery('#wcp-saved').show();
        });

    });
  

    jQuery('#accordion .btnadd').click(function(event) {
        event.preventDefault();
        sCounter++;
        jQuery( "#accordion" ).append('<h3>Caption Hover</h3>');
        // jQuery(this).closest('.ui-accordion-content').clone(true).removeClass('firstelement').appendTo('#accordion').find('.shortcode').text(sCounter).closest('.tab-content').find('.wp-picker-container').remove().
        var parent_newly = jQuery(this).closest('.ui-accordion-content').clone(true).removeClass('firstelement').appendTo('#accordion').find('button.fullshortcode').attr('id', sCounter).closest('.tab-content');
        parent_newly.find('.wp-picker-container').remove();
        parent_newly.find('.insert-picker-bg').append('<input type="text" class="captionbg colorpicker" data-alpha="true" value="#000000" />');
        parent_newly.find('.insert-picker-color').append('<input type="text" class="captioncolor colorpicker" data-alpha="true" value="#000000" />');
        jQuery("#accordion").accordion('refresh');
        parent_newly.find('.colorpicker').wpColorPicker();
    });

    jQuery('#accordion .btnnew').click(function(event) {
        event.preventDefault();
        sCounter++;
        jQuery( "#accordion" ).append('<h3>Caption Hover</h3>');
        // jQuery(this).closest('.ui-accordion-content').clone(true).removeClass('firstelement').appendTo('#accordion').find('.shortcode').text(sCounter).closest('.tab-content').find('.wp-picker-container').remove().
        var parent_newly = jQuery(this).closest('.ui-accordion-content').clone(true).removeClass('firstelement').appendTo('#accordion').find('button.fullshortcode').attr('id', sCounter).closest('.tab-content');
        parent_newly.find('.wp-picker-container').remove();
        parent_newly.find('.insert-picker-bg').append('<input type="text" class="captionbg colorpicker" data-alpha="true" value="#000000" />');
        parent_newly.find('.insert-picker-color').append('<input type="text" class="captioncolor colorpicker" data-alpha="true" value="#000000" />');

        parent_newly.find('.insert-preview').html('');
        parent_newly.find('.imageurl').val('');
        parent_newly.find('.imagetitle').val('');
        parent_newly.find('.imagealt').val('');
        parent_newly.find('.captiontext').val('');
        parent_newly.find('.captionalignment').val('');
        parent_newly.find('.captionopacity').val('');
        parent_newly.find('.refname').val('');
        parent_newly.find('.hoverstyle').val('');

        parent_newly.find('.colorpicker').wpColorPicker();
        jQuery("#accordion").accordion('refresh');
    });
    jQuery('#accordion .btndelete').click(function(event) {
        event.preventDefault();
        if (jQuery(this).closest('.ui-accordion-content').hasClass('firstelement')) {
            alert('You can not delete it as it is first element!');
        } else {
            var head = jQuery(this).closest('.ui-accordion-content').prev();
            var body = jQuery(this).closest('.ui-accordion-content');
            head.remove();
            body.remove();
            jQuery("#accordion").accordion('refresh');
        }
    });

    jQuery('button.fullshortcode').click(function(event) {
        event.preventDefault();
        prompt("Copy and use this Shortcode", '[image-caption-hover id="'+jQuery(this).attr('id')+'"]');
    });

    var image_caption_hover_uploader;
     
    jQuery('.upload_image_button').live('click', function( event ){
     
        event.preventDefault();

        var this_widget = jQuery(this).closest('.tab-content');
     
     
        // Create the media frame.
        image_caption_hover_uploader = wp.media.frames.image_caption_hover_uploader = wp.media({
          title: jQuery( this ).data( 'title' ),
          button: {
            text: jQuery( this ).data( 'btntext' ),
          },
          multiple: false  // Set to true to allow multiple files to be selected
        });
     
        // When an image is selected, run a callback.
        image_caption_hover_uploader.on( 'select', function() {
          // We set multiple to false so only get one image from the uploader
          attachment = image_caption_hover_uploader.state().get('selection').first().toJSON();
            
            
             this_widget.find('.imageurl').val(attachment.url);
             this_widget.find('.imagetitle').val(attachment.title);
             this_widget.find('.imagealt').val(attachment.alt);
             this_widget.find('.captiontext').val(attachment.caption);
             this_widget.find('.img-prev').html('<img src="'+attachment.url+'" width="100%">');
        });
     
        // Finally, open the modal
        image_caption_hover_uploader.open();
    });

    jQuery('.tab-content').on('click', '.update-preview', function(event) {
        event.preventDefault();
        var parentdiv = jQuery(this).closest('.tab-content');

        var imageurl = parentdiv.find('.imageurl').val();
        var imagetitle = parentdiv.find('.imagetitle').val();
        var imagealt = parentdiv.find('.imagealt').val();
        var captiontext = parentdiv.find('.captiontext').val();
        var captionalignment = parentdiv.find('.captionalignment').val();
        var captionbg = parentdiv.find('.captionbg').val();
        var captioncolor = parentdiv.find('.captioncolor').val();
        var captionopacity = parentdiv.find('.captionopacity').val();
        var refname = parentdiv.find('.refname').val();
        var hoverstyle = parentdiv.find('.hoverstyle').val(); 

        parentdiv.find('.insert-preview').html('<div class="wcp-caption-plugin" id="wcp-caption1">\
                                <div class="image-caption-box">\
                                    <div class="caption '+hoverstyle+'" style="background-color: '+captionbg+'; color: '+captioncolor+'; opacity: '+captionopacity+';">\
                                        <div class="centered-text" style="text-align: '+captionalignment+'; padding: 5px;">'+captiontext+'</div>\
                                    </div>\
                                    <img class="wcp-caption-image" src="'+imageurl+'" title="'+imagetitle+'" alt="'+imagealt+'"/>\
                                </div>\
                            </div>');
        setTimeout(function() {

            jQuery('.image-flip-up, .image-flip-down, .rotate-image-down, .tilt-image, .image-flip-right, .image-flip-left').closest('.image-caption-box').css('overflow', 'visible');

            parentdiv.find('.image-caption-box').css('width', '100%');

            var current_width = parentdiv.find('.wcp-caption-image').width();
            var current_height = parentdiv.find('.wcp-caption-image').height();
            var current_wraper = parentdiv.find('.wcp-caption-image').closest('.wcp-caption-plugin');
            current_wraper.find('.image-caption-box, .caption').css({
                'width': current_width,
                'height': current_height
            });
                        
            jQuery("#accordion").accordion('refresh');
        }, 50);
    });
});