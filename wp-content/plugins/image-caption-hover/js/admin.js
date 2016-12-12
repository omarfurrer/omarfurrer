jQuery(document).ready( function(){
    // Uploading files

    var image_caption_hover_plugin;
     
    jQuery('.upload_image_button').live('click', function( event ){
     
        event.preventDefault();

        var this_widget = '#' + jQuery(this).closest('.widget').attr('id');
     
     
        // Create the media frame.
        image_caption_hover_plugin = wp.media.frames.image_caption_hover_plugin = wp.media({
          title: jQuery( this ).data( 'title' ),
          button: {
            text: jQuery( this ).data( 'btntext' ),
          },
          multiple: false  // Set to true to allow multiple files to be selected
        });
     
        // When an image is selected, run a callback.
        image_caption_hover_plugin.on( 'select', function() {
          // We set multiple to false so only get one image from the uploader
          attachment = image_caption_hover_plugin.state().get('selection').first().toJSON();
          	
          	
             jQuery(this_widget).find('.image-url').val(attachment.url);
             jQuery(this_widget).find('.image-title').val(attachment.title);
             jQuery(this_widget).find('.alt-text').val(attachment.alt);
             jQuery(this_widget).find('.img-prev').html('<img src="'+attachment.url+'" width="100%">')
        });
     
        // Finally, open the modal
        image_caption_hover_plugin.open();
    });

     function initColorPicker( widget ) {
              widget.find( '.color-picker' ).wpColorPicker();
      }
          function onFormUpdate( event, widget ) {
              initColorPicker( widget );
      }
      jQuery( document ).on( 'widget-added widget-updated', onFormUpdate );

      jQuery( document ).ready( function() {
              jQuery( '#widgets-right .widget:has(.color-picker)' ).each( function () {
                      initColorPicker( jQuery( this ) );                                                   
              } );
      } );
});