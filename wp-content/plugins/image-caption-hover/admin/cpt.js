jQuery(document).ready(function($) {

    $('.ich-rich-editor-wrap').hide();
    $('.colorpicker').wpColorPicker();

    var active = false,
        sorting = false;
    
    $( "#wcpinner" )
    .accordion({
        header: "> div > h3",
        collapsible: true,
        activate: function( event, ui){
            //this fixes any problems with sorting if panel was open (remove to see what I am talking about)
            if(sorting)
                $(this).sortable("refresh");   
        }
    })
    .sortable({
        handle: "h3",
        placeholder: "ui-state-highlight",
        start: function( event, ui ){
            //change bool to true
            sorting=true;
            
            //find what tab is open, false if none
            active = $(this).accordion( "option", "active" ); 
            
            //possibly change animation here
            $(this).accordion( "option", "animate", { easing: 'swing', duration: 0 } );
            
            //close tab
            $(this).accordion({ active:false });
        },
        stop: function( event, ui ) {
            ui.item.children( "h3" ).triggerHandler( "focusout" );
            
            //possibly change animation here; { } is default value
            $(this).accordion( "option", "animate", { } );
            
            //open previously active panel
            $(this).accordion( "option", "active", active );
            
            //change bool to false
            sorting=false;
        }
    });

    var count = $('.wcp-main-wrap .group:last-child').attr('id');
    
    $('.wcp-main-wrap .group').each(function(index, el) {
    	if (parseInt($(this).attr('id')) > parseInt(count)) {
    		count = $(this).attr('id');
    	};
    });
    
    $(".add").click(function(event) {
        event.preventDefault();
        count = parseInt(count) + 1;
        var clone_this = $('.wcp-main-wrap div#1').clone(true);
        $(clone_this).attr('id', count);
        $(clone_this).find('input, select, textarea').each(function(index, el) {

            if ($(this).attr('name') !== undefined) {
                var old_name = $(this).attr('name');
                // console.log(old_name);
                var new_name = old_name.replace(/[0-9]/g, count);
                $(this).attr('name', new_name);
            }

            if ($(this).hasClass('colorpicker')) {
                var c_wrap = $(this).closest('.wcp-color-wrap');
                console.log(c_wrap);
                c_wrap.html('<input name="'+new_name+'" type="text" class="colorpicker" data-alpha="true">');
                c_wrap.find('.colorpicker').wpColorPicker();
            }
        });
        $(clone_this).appendTo('.wcp-main-wrap').hide().fadeIn('slow');
    });

    $(".wcp-main-wrap").on('click', '.button-delete', function(event) {
        event.preventDefault();
        var this_col = $(this).closest('.group').attr('id');
        if (this_col == '1' || this_col == 1) {
            alert('Sorry, you can not delete first Column!');
        } else {
            $(this).closest('.group').fadeOut('slow', function() {
               $(this).remove(); 
            });
        }
    });

    // Media Uploader
    var ich_cpt_uploader;
     
    jQuery('.upload_image_button').live('click', function( event ){
     
        event.preventDefault();

        var this_widget = '#' + jQuery(this).closest('.group').attr('id');
     
     
        // Create the media frame.
        ich_cpt_uploader = wp.media.frames.ich_cpt_uploader = wp.media({
          title: jQuery( this ).data( 'title' ),
          button: {
            text: jQuery( this ).data( 'btntext' ),
          },
          multiple: false  // Set to true to allow multiple files to be selected
        });
     
        // When an image is selected, run a callback.
        ich_cpt_uploader.on( 'select', function() {
          // We set multiple to false so only get one image from the uploader
          attachment = ich_cpt_uploader.state().get('selection').first().toJSON();
            
            
             jQuery(this_widget).find('.image-url').val(attachment.url);
             jQuery(this_widget).find('.image-title').val(attachment.title);
             jQuery(this_widget).find('.alt-text').val(attachment.alt);
             // jQuery(this_widget).find('.img-prev').html('<img src="'+attachment.url+'" width="100%">')
        });
     
        // Finally, open the modal
        ich_cpt_uploader.open();
    });

    var current_textarea = '';
    var win_scroll = '';

    $('.caption-settings-wrap').on('click', '.ich-open-editor', function(event) {
        event.preventDefault();

        $('#ich-rich-editor').summernote({
            height: 300,                 // set editor height
            minHeight: null,             // set minimum height of editor
            maxHeight: null,             // set maximum height of editor
            focus: true,
            toolbar: [
                ['style', ['style', 'fontname', 'fontsize' ,'strikethrough', 'superscript', 'subscript', 'paragraph', 'bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['insert', ['link', 'hr']],
                ['screenop', ['fullscreen', 'codeview', 'undo', 'redo']],
            ]        
        });

        current_textarea = $(this).closest('.input-group').find('textarea');
        var curr_contents = $(this).closest('.input-group').find('textarea').val();
        // console.log(curr_contents);
        $('.caption-settings-wrap').hide();
        $('.ich-rich-editor-wrap').show();

        win_scroll = $(window).scrollTop();

        $('#ich-rich-editor').summernote('code', curr_contents);
        jQuery('html, body').animate({
            scrollTop: jQuery('#ichcpt_options').offset().top
        }, 300);
    });

    $('.ich-rich-editor-wrap').on('click', '.ich-editor-insert', function(event) {
        event.preventDefault();
        
        var editor_contents = $('#ich-rich-editor').summernote('code');

        current_textarea.val(editor_contents);
        $('.caption-settings-wrap').show();
        $('#ich-rich-editor').summernote('destroy');
        $('.ich-rich-editor-wrap').hide();
        jQuery('html, body').animate({
            scrollTop: win_scroll
        }, 300);

    });

    $('.ich-rich-editor-wrap').on('click', '.ich-editor-cancel', function(event) {
        event.preventDefault();
        $('.caption-settings-wrap').show();
        $('#ich-rich-editor').summernote('destroy');
        $('.ich-rich-editor-wrap').hide();

        jQuery('html, body').animate({
            scrollTop: win_scroll
        }, 300);
    });

});