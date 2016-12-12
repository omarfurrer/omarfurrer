<?php 

/**
 * new WordPress Widget format
 * Wordpress 2.8 and above
 * @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
 */
class Image_Caption_Hover_Widget extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     **/
    function Image_Caption_Hover_Widget() {
        $widget_ops = array( 'classname' => 'wcp_caption', 'description' => 'Image with hover effects' );
        parent::__construct( 'wcp_caption', 'Image Caption Hover', $widget_ops );
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     * @return void Echoes it's output
     **/
    function widget( $args, $instance ) {
        extract( $args, EXTR_SKIP );
        extract($instance);
?>

	<div class="wcp-caption-plugin" id="<?php echo $widget_id; ?>" data-height="responsive">
	    <div class="image-caption-box">  
	        <div class="caption <?php echo $imagestyle; ?>" style="background-color: <?php echo $wcp_hover_bg ?>; color: <?php echo $wcp_hover_color ?>; opacity: <?php echo $wcp_hover_bg_opacity ?>;">  
	        	<div style="display:table;height:100%;width: 100%;">
		        	<div class="centered-text" style="text-align: <?php echo $wcp_caption_alignment; ?>; padding: 5px;"><?php echo $wcp_hover_caption; ?></div>
		        </div>
	        </div>  
	        <img class="wcp-caption-image" src="<?php echo $wcp_caption_image_url; ?>" title="<?php echo $image_title ?>" alt="<?php echo $image_alt; ?>"/>  
	    </div>
	</div>
<?php

    }

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings
     * @return array The validated and (if necessary) amended settings
     **/
    function update( $new_instance, $old_instance ) {

        // update logic goes here
        $updated_instance = $new_instance;
        return $updated_instance;
    }

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     **/
    function form( $instance ) {
        extract($instance);
?>
	<p>
		<p style="text-align:center;"><?php _e( 'Image', 'image-caption-hover' ); ?></p><hr />
		<label for="<?php echo $this->get_field_id('wcp_caption_image_url'); ?>"><?php _e( 'Paste URL or use from Media', 'image-caption-hover' ); ?></label>
	    <input 	type="text"
				class="image-url"
				name="<?php echo $this->get_field_name('wcp_caption_image_url'); ?>"
				id="<?php echo $this->get_field_id('wcp_caption_image_url'); ?>"
				value="<?php if (isset($wcp_caption_image_url)) echo esc_attr($wcp_caption_image_url); ?>"
		/>
	    <input id="media-upload" data-title="Image Caption Hover" data-btntext="Select it" class="button upload_image_button" type="button" value="<?php _e( 'Media', 'image-caption-hover' ); ?>" />
	</p>
	<p class="img-prev">
		<?php if (isset($wcp_caption_image_url)) { echo '<img src="'.$wcp_caption_image_url.'" style="max-width: 100%;">';} ?>
	</p>	
	<p>
		<label for="<?php echo $this->get_field_id('image_title'); ?>"><?php _e( 'Title', 'image-caption-hover' ); ?>:</label>
		<input type="text"
			class="image-title widefat"
			name="<?php echo $this->get_field_name('image_title'); ?>"
			id="<?php echo $this->get_field_id('image_title'); ?>"
			value="<?php if (isset($image_title)) echo esc_attr($image_title); ?>"
		/>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('image_alt'); ?>"><?php _e( 'Alt', 'image-caption-hover' ); ?>:</label>
		<input type="text"
			class="alt-text widefat"
			name="<?php echo $this->get_field_name('image_alt'); ?>"
			id="<?php echo $this->get_field_id('image_alt'); ?>"
			value="<?php if (isset($image_alt)) echo esc_attr($image_alt); ?>"
		/>
	</p>

	<p style="text-align:center;"><?php _e( 'Caption', 'image-caption-hover' ); ?></p><hr />
	<p>
		<label for="<?php echo $this->get_field_id('wcp_hover_caption'); ?>"><?php _e( 'You can also use HTML tags here', 'image-caption-hover' ); ?>:</label>
		<textarea
			class="widefat"
			name="<?php echo $this->get_field_name('wcp_hover_caption'); ?>"
			id="<?php echo $this->get_field_id('wcp_hover_caption'); ?>"
		><?php if (isset($wcp_hover_caption)) echo esc_attr($wcp_hover_caption); ?></textarea>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('wcp_caption_alignment'); ?>"><?php _e( 'Caption alignment', 'image-caption-hover' ); ?>: </label>
		<select class="widefat" name="<?php echo $this->get_field_name('wcp_caption_alignment'); ?>" id="<?php echo $this->get_field_id('wcp_caption_alignment'); ?>">
			<option value="auto" <?php if(isset($wcp_caption_alignment) && $wcp_caption_alignment == 'auto'){echo 'selected';} ?>><?php _e( 'Auto', 'image-caption-hover' ); ?></option>
			<option value="center" <?php if(isset($wcp_caption_alignment) && $wcp_caption_alignment == 'center'){echo 'selected';} ?>><?php _e( 'Center', 'image-caption-hover' ); ?></option>
			<option value="right" <?php if(isset($wcp_caption_alignment) && $wcp_caption_alignment == 'right'){echo 'selected';} ?>><?php _e( 'Right', 'image-caption-hover' ); ?></option>
			<option value="left"<?php if(isset($wcp_caption_alignment) && $wcp_caption_alignment == 'left'){echo 'selected';} ?>><?php _e( 'Left', 'image-caption-hover' ); ?></option>
			<option value="justify"<?php if(isset($wcp_caption_alignment) && $wcp_caption_alignment == 'justify'){echo 'selected';} ?>><?php _e( 'Justify', 'image-caption-hover' ); ?></option>
		</select>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'wcp_hover_bg' ); ?>"><?php _e( 'Caption background color:' ); ?></label><br>
		<input type="text" name="<?php echo $this->get_field_name( 'wcp_hover_bg' ); ?>" class="color-picker" id="<?php echo $this->get_field_id( 'wcp_hover_bg' ); ?>" value="<?php if (isset($wcp_hover_bg)) echo esc_attr($wcp_hover_bg); ?>" data-default-color="#fff" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'wcp_hover_bg_opacity' ); ?>"><?php _e( 'Caption background opacity:' ); ?></label><br>
		<input type="number" name="<?php echo $this->get_field_name( 'wcp_hover_bg_opacity' ); ?>" min="0" max="1" step="0.1" id="<?php echo $this->get_field_id( 'wcp_hover_bg_opacity' ); ?>" value="<?php if (isset($wcp_hover_bg_opacity)) echo esc_attr($wcp_hover_bg_opacity); ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'wcp_hover_color' ); ?>"><?php _e( 'Caption text color:' ); ?></label><br>
		<input type="text" name="<?php echo $this->get_field_name( 'wcp_hover_color' ); ?>" class="color-picker" id="<?php echo $this->get_field_id( 'wcp_hover_color' ); ?>" value="<?php if (isset($wcp_hover_color)) echo esc_attr($wcp_hover_color); ?>" data-default-color="#fff" />
	</p>

	<p style="text-align:center;"><?php _e( 'Hover styles', 'image-caption-hover' ); ?></p>
	<hr>
<?php 
	$wcp_classes = array(
		'slide-left-to-right',
		'slide-right-to-left',
		'slide-top-to-bottom',
		'slide-bottom-to-top',
		'image-flip-up',
		'image-flip-down',
		'image-flip-right',
		'image-flip-left',
		'rotate-image-down',
		'image-turn-around',
		'zoom-and-pan',
		'tilt-image',
		'morph',
		'move-image-right',
		'move-image-left',
		'move-image-top',
		'move-image-bottom',
		'image-squeez-right',
		'image-squeez-left',
		'image-squeez-top',
		'image-squeez-bottom',
		'zoom-in',
		'zoom-out',
		'zoom-in-twist',
		'zoom-out-twist',
		'zoom-caption-in-image-out',
		'zoom-caption-out-image-in',
		'zoom-image-out-caption-twist',
		'zoom-image-in-caption-twist',	
		'no-effect',
	);
?>
	<p>
		<label for="<?php echo $this->get_field_id('imagestyle'); ?>"><?php _e( 'Hover style', 'image-caption-hover' ); ?>: </label>
		<select class="widefat" name="<?php echo $this->get_field_name('imagestyle'); ?>">
			<?php foreach ($wcp_classes as $className) { ?>
				<option value="<?php echo $className; ?>" <?php if(isset($imagestyle) && $imagestyle == $className){echo 'selected';} ?>><?php echo ucwords(str_replace("-"," ",$className)); ?></option>
			<?php } ?>
		</select>
	</p>

<?php
    }
}
// End of Plugin Class

add_action( 'widgets_init', create_function( '', "register_widget( 'Image_Caption_Hover_Widget' );" ) );

add_action( 'admin_enqueue_scripts', 'enqueue_script_media_uploader' );
add_action( 'wp_head', 'wcp_caption_styles' );

/*
*	Script for Media uploader
 */
function enqueue_script_media_uploader($hook){
    if ( 'widgets.php' != $hook ) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wcp_uploader', plugin_dir_url( __FILE__ ) . 'js/admin.js', array('jquery','wp-color-picker') );
}
function wcp_caption_styles(){
	wp_enqueue_style( 'wcp-caption-styles', plugin_dir_url( __FILE__ ) .'css/style.css' );
    wp_enqueue_script( 'wcp-caption-scripts', plugin_dir_url( __FILE__ ) . 'js/script.js', array('jquery') );
    wp_localize_script( 'wcp-caption-scripts','wcp_all_settings', get_option( 'wcp_ich_admin_settings_all' ) );
}
?>