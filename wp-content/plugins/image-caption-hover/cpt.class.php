<?php
/**
* Plugin Class for CPT
*/
class Image_Caption_Hover_CPT
{
    
    function __construct(){
        add_action( 'admin_enqueue_scripts', array($this, 'loading_scripts_admin'));
        add_shortcode( 'ichcpt', array($this, 'render_ich_shortcode') );
        add_action( 'init', array($this, 'register_ich_cpt') );
        add_action( 'add_meta_boxes', array($this, 'ich_settings_box') );
        // add_action( 'wp_enqueue_scripts', array($this, 'loading_front_scripts') );
        add_action( 'save_post', array($this, 'saving_ich_cpt') );

        add_filter('manage_ich_cpt_posts_columns', array($this, 'ich_cpt_column_head'));
        add_action('manage_ich_cpt_posts_custom_column', array($this, 'ich_cpt_column_content'), 10, 2);
    }

    /**
    * Registers a new post type
    * @uses $wp_post_types Inserts new post type object into the list
    *
    * @param string  Post type key, must not exceed 20 characters
    * @param array|string  See optional args description above.
    * @return object|WP_Error the registered post type object, or an error object
    */
    function register_ich_cpt() {
    
        $custom_labels = array(
            'name'                => __( 'Image Caption Hover', 'image-caption-hover' ),
            'singular_name'       => __( 'Image Caption Hover', 'image-caption-hover' ),
            'add_new'             => _x( 'Add New', 'image-caption-hover', 'image-caption-hover' ),
            'add_new_item'        => __( 'Add New', 'image-caption-hover' ),
            'name_admin_bar'        => __( 'Image Caption Hover', 'image-caption-hover' ),
            'edit_item'           => __( 'Edit', 'image-caption-hover' ),
            'new_item'            => __( 'New', 'image-caption-hover' ),
            'view_item'           => __( 'View', 'image-caption-hover' ),
            'search_items'        => __( 'Search', 'image-caption-hover' ),
            'not_found'           => __( 'No Image Caption Hover found', 'image-caption-hover' ),
            'not_found_in_trash'  => __( 'No Image Caption Hover found in Trash', 'image-caption-hover' ),
            'parent_item_colon'   => __( 'Parent:', 'image-caption-hover' ),
            'menu_name'           => __( 'Image Caption Hover', 'image-caption-hover' ),
            'all_items'           => __( 'View All', 'image-caption-hover' ),
        );
    
        $anim_args = array(
            'labels'              => $custom_labels,
            'hierarchical'        => false,
            'description'         => 'Image Caption Hover',
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => null,
            'menu_icon'           => 'dashicons-screenoptions',
            'show_in_nav_menus'   => false,
            'publicly_queryable'  => true,
            'exclude_from_search' => true,
            'has_archive'         => false,
            'query_var'           => true,
            'can_export'          => true,
            'rewrite'             => true,
            'capability_type'     => 'post',
            'supports'            => array(
                'title'
                )
        );
    
        register_post_type( 'ich_cpt', $anim_args );
    }
    

    function loading_scripts_admin($check){
        global $post;
        if ( $check == 'post-new.php' || $check == 'post.php' || 'edit.php') {
            if (isset($post->post_type) && 'ich_cpt' === $post->post_type) {
                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_style( 'ichcpt-admin', plugin_dir_url( __FILE__ ). '/css/ichcpt-admin.css');
                wp_enqueue_script( 'wp-color-picker-alpha', plugins_url( 'js/wp-color-picker-alpha.min.js', __FILE__ ), array( 'wp-color-picker' ));
                wp_enqueue_media();

                wp_enqueue_style( 'ich-bootstrap', plugin_dir_url( __FILE__ ). '/summernote/bs/css/bootstrap.min.css');
                wp_enqueue_style( 'summernote-css', plugin_dir_url( __FILE__ ). '/summernote/summernote.css');
                wp_enqueue_script( 'ich-bootstrap', plugin_dir_url( __FILE__ ). '/summernote/bs/js/bootstrap.min.js', array('jquery'));
                wp_enqueue_script( 'summernote-js', plugin_dir_url( __FILE__ ). '/summernote/summernote.min.js', array('jquery'));
                
                wp_enqueue_script( 'ichcpt-admin', plugin_dir_url( __FILE__ ). '/admin/cpt.js', array('jquery', 'wp-color-picker', 'jquery-ui-sortable', 'jquery-ui-accordion'));
            }
        }
    }

    function loading_front_scripts(){

    }

    function render_ich_shortcode($atts){

        wp_enqueue_style( 'wc-simple-grid', plugin_dir_url( __FILE__ ). 'css/simplegrid.css');
        wp_enqueue_style( 'wcp-caption-styles', plugin_dir_url( __FILE__ ) .'css/style.css' );
        wp_enqueue_style( 'wcp-ihover', plugin_dir_url( __FILE__ ) .'css/ihover.min.css' );
        wp_enqueue_script( 'wcp-caption-scripts', plugin_dir_url( __FILE__ ) . 'js/script.js', array('jquery') );
        
        ob_start();
            include 'cpt_inc/render_shortcode.php';
        return ob_get_clean();

    }

    function ich_settings_box() {
        add_meta_box( 'ichcpt_options', 'Images', array($this, 'ichcpt_contents_mb'), 'ich_cpt');
        add_meta_box( 'ichcpt_shortcode', 'Shortcode', array($this, 'ichcpt_shortcode_mb'), 'ich_cpt', 'side');
        add_meta_box( 'ichcpt_settings', 'Settings', array($this, 'ichcpt_settings_mb'), 'ich_cpt');
        add_meta_box( 'ichcpt_pro', 'Image Caption Hover', array($this, 'ichcpt_pro_mb'), 'ich_cpt', 'side');
    }

    function ichcpt_shortcode_mb(){
        global $post;
        if (isset($post->ID)) {
            echo '<p style="text-align:center;">[ichcpt id="'.$post->ID.'"]</p>';
            
        } else {
            echo 'Please Save settings to get shortcode';
        }
    }

    function ichcpt_settings_mb(){
        include 'cpt_inc/settings_box.php';
    }

    function ichcpt_pro_mb(){
        ?>
            <p class="description">
                <?php _e( 'Having trouble setting up?', 'image-caption-hover' ); ?>
                <a target="_blank" href="http://webcodingplace.com/how-to-use-image-caption-hover-wordpress-plugin/"><?php _e( 'How to use', 'image-caption-hover' ); ?></a>
                <?php _e( 'OR', 'image-caption-hover' ); ?>
                <a target="_blank" href="http://webcodingplace.com/contact-us/"><?php _e( 'contact us for help', 'image-caption-hover' ); ?></a>
            </p>
            <hr>
            
            <h3>Image Caption Hover Pro Features</h3>
            <ol>
                <li><?php _e( 'Audio Sound Effects on Hover', 'image-caption-hover' ); ?></li>
                <li><?php _e( 'Responsive PopUp', 'image-caption-hover' ); ?></li>
                <li><?php _e( 'Custom Animation Speed', 'image-caption-hover' ); ?></li>
                <li><?php _e( '60+ More Hover Effects', 'image-caption-hover' ); ?></li>
                <li><?php _e( 'Display Caption at Bottom or Top', 'image-caption-hover' ); ?></li>
                <li><?php _e( 'Custom Width and Height', 'image-caption-hover' ); ?></li>
                <li><?php _e( 'Borders and Frames', 'image-caption-hover' ); ?></li>
                <li><?php _e( 'Your existing Images and Captions will remain saved', 'image-caption-hover' ); ?></li>
            </ol>
            <a style="width: 100%; text-align:center;" target="_blank" class="button button-primary button-hero" href="http://webcodingplace.com/wordpress-pro-plugins/image-caption-hover-pro-wordpress-plugin/">
                <?php _e( 'Unlock Pro Features', 'image-caption-hover' ); ?>
            </a>
        <?php
    }    

    /* Prints the box content */
    function ichcpt_contents_mb() {
        // Use nonce for verification
        wp_nonce_field( plugin_basename( __FILE__ ), 'wcp_ich_nonce' );
        include 'cpt_inc/render_box_contents.php';
    }

    function saving_ich_cpt( $post_id ) {
        // verify if this is an auto save routine. 
        // If it is our form has not been submitted, so we dont want to do anything
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return;

        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        if ( !isset( $_POST['wcp_ich_nonce'] ) )
            return;

        if ( !wp_verify_nonce( $_POST['wcp_ich_nonce'], plugin_basename( __FILE__ ) ) )
            return;

        // OK, we're authenticated: we need to find and save the data

        $wcp_images = $_POST['ich_cpt'];
        $wcp_settings = $_POST['ichcpt_settings'];

        update_post_meta($post_id,'ich_cpt',$wcp_images);
        update_post_meta($post_id,'ichcpt_settings',$wcp_settings);
    }

    function ich_cpt_column_head($defaults){
        $defaults['fgg_col'] = 'Shortcode';
        return $defaults;       
    }

    function ich_cpt_column_content($column_name, $gallery_id){
        if ($column_name == 'fgg_col') {
            echo '[ichcpt id="'.$gallery_id.'"]';
        }
    }

    function get_all_fields_data(){
        $all_fields = array(
            
            array(
                'type' => 'image', 
                'name' => 'imageurl', 
                'title' => __( 'Image URL', 'image-caption-hover' ),
                'help' => __( 'Provide image url, you can also use media library to insert url', 'image-caption-hover' ), 
            ),

            array(
                'type' => 'text', 
                'name' => 'imagetitle', 
                'title' => __( 'Title', 'image-caption-hover' ),
                'help' => __( 'It will be used as title attribute of image tag. Square and Circle Effects will take it as heading', 'image-caption-hover' ), 
            ),

            array(
                'type' => 'text', 
                'name' => 'imagealt', 
                'title' => __( 'Alternate Text', 'image-caption-hover' ),
                'help' => __( 'It will be used as alt attribute of image tag', 'image-caption-hover' ), 
            ),

            array(
                'type' => 'text',
                'name' => 'boxwidth',
                'title' => __( 'Width', 'image-caption-hover' ),
                'help' => __( 'Provide width for image Eg: 150px or 50%. Leave blank for responsive (available in pro version)', 'image-caption-hover' ), 
                'disabled' => 'true',
            ),

            array(
                'type' => 'text',
                'name' => 'boxheight',
                'title' => __( 'Height', 'image-caption-hover' ),
                'help' => __( 'Provide height for image Eg: 150px or 50%. Leave blank for responsive (available in pro version)', 'image-caption-hover' ), 
                'disabled' => 'true',
            ),

            array(
                'type' => 'caption',
                'name' => 'captiontext',
                'title' => __( 'Caption', 'image-caption-hover' ),
                'help' => __( 'Provide caption text here, you can also use rich editor to insert caption. It should be plain text if you are using circle or square effects', 'image-caption-hover' ),
            ),

            array(
                'type' => 'select',
                'name' => 'captionwrap',
                'options' => array(
                    'div' => __( 'None', 'image-caption-hover' ),
                    'h1' => __( 'Heading 1', 'image-caption-hover' ),
                    'h2' => __( 'Heading 2', 'image-caption-hover' ),
                    'h3' => __( 'Heading 3', 'image-caption-hover' ),
                    'h4' => __( 'Heading 4', 'image-caption-hover' ),
                    'h5' => __( 'Heading 5', 'image-caption-hover' ),
                    'h6' => __( 'Heading 6', 'image-caption-hover' ),
                ),
                'title' => __( 'Caption Wrapper', 'image-caption-hover' ),
                'help' => __( 'Wrap caption in markup. Please select none if you are using editor', 'image-caption-hover' ),
            ),

            array(
                'type' => 'select',
                'name' => 'captionalignment',
                'options' => array(
                    'auto' => __( 'Auto', 'image-caption-hover' ),
                    'center' => __( 'Center', 'image-caption-hover' ),
                    'right' => __( 'Right', 'image-caption-hover' ),
                    'left' => __( 'Left', 'image-caption-hover' ),
                    'justify' => __( 'Justify', 'image-caption-hover' ),
                ),
                'title' => __( 'Caption Alignment', 'image-caption-hover' ),
                'help' => __( 'Wrap caption in markup. Please select auto if you are using editor', 'image-caption-hover' ),
            ),

            array(
                'type' => 'color',
                'name' => 'captioncolor',
                'title' => __( 'Text Color', 'image-caption-hover' ),
                'help' => __( 'Choose text color for caption', 'image-caption-hover' ),
            ),

            array(
                'type' => 'color',
                'name' => 'captionbg',
                'title' => __( 'Background Color', 'image-caption-hover' ),
                'help' => __( 'Choose background color for caption', 'image-caption-hover' ),
            ),

            array(
                'type' => 'select',
                'name' => 'captionoverlay',
                'options' => array(
                    'full' => __( 'Full', 'image-caption-hover' ),
                    'top disabled' => __( 'Top', 'image-caption-hover' ),
                    'bottom disabled' => __( 'Bottom', 'image-caption-hover' ),
                ),
                'title' => __( 'Overlay Position', 'image-caption-hover' ),
                'help' => __( 'Choose caption overlay position (available in pro version)', 'image-caption-hover' ),
            ),

            array(
                'type' => 'text',
                'name' => 'captionlink',
                'title' => __( 'Link To', 'image-caption-hover' ),
                'help' => __( 'Provide URL here or leave blank to disable link', 'image-caption-hover' ),
            ),

            array(
                'type' => 'select',
                'name' => 'captiontarget',
                'options' => array(
                    '_blank' => __( 'New Window', 'image-caption-hover' ),
                    '_self' => __( 'Same Window', 'image-caption-hover' ),
                    '_parent' => __( 'Parent frameset', 'image-caption-hover' ),
                    '_top' => __( 'Full body of the window', 'image-caption-hover' ),
                ),
                'title' => __( 'Link Target', 'image-caption-hover' ),
                'help' => __( 'Choose to open link in new tab or same', 'image-caption-hover' ),
            ),

            array(
                'type' => 'checkbox',
                'name' => 'lightbox',
                'title' => __( 'LightBox', 'image-caption-hover' ),
                'help' => __( 'It will open above link in popup on clicking (available in pro version)', 'image-caption-hover' ),
            ),

            array(
                'type' => 'hovereffects',
                'name' => 'hovereffect',
                'title' => __( 'Hover Effect', 'image-caption-hover' ),
                'help' => __( 'Choose hover style', 'image-caption-hover' ),
            ),

            array(
                'type' => 'text',
                'name' => 'animationspeed',
                'title' => __( 'Animation Speed', 'image-caption-hover' ),
                'help' => __( 'Provide animation speed Eg: 500ms or 1s (available in pro version)', 'image-caption-hover' ),
                'disabled' => 'true',
            ),

            array(
                'type' => 'text',
                'name' => 'audio',
                'title' => __( 'Audio', 'image-caption-hover' ),
                'help' => __( 'Provide mp3 music to play on hover (available in pro version)', 'image-caption-hover' ),
                'disabled' => 'true',
            ),

            array(
                'type' => 'text',
                'name' => 'borderwidth',
                'title' => __( 'Border Width', 'image-caption-hover' ),
                'help' => __( 'Provide border width Eg: 5px, leave blank to disable border (available in pro version)', 'image-caption-hover' ),
                'disabled' => 'true',
            ),

            array(
                'type' => 'text',
                'name' => 'bordercolor',
                'title' => __( 'Border Color', 'image-caption-hover' ),
                'help' => __( 'Choose color for border (available in pro version)', 'image-caption-hover' ),
                'disabled' => 'true',
            ),

            array(
                'type' => 'text',
                'name' => 'borderradius',
                'title' => __( 'Border Radius', 'image-caption-hover' ),
                'help' => __( 'Provide radius for border eg: 5px or 50% (available in pro version)', 'image-caption-hover' ),
                'disabled' => 'true',
            ),


            array(
                'type' => 'select',
                'name' => 'borderstyle',
                'options' => array(
                    'solid' => __( 'Solid', 'image-caption-hover' ),
                    'dotted disabled' => __( 'Dotted', 'image-caption-hover' ),
                    'dashed disabled' => __( 'Dashed', 'image-caption-hover' ),
                    'double disabled' => __( 'Double', 'image-caption-hover' ),
                    'groove disabled' => __( 'Groove', 'image-caption-hover' ),
                    'ridge disabled' => __( 'Ridge', 'image-caption-hover' ),
                    'inset disabled' => __( 'Inset', 'image-caption-hover' ),
                    'outset disabled' => __( 'Outset', 'image-caption-hover' ),
                ),                
                'title' => __( 'Border Type', 'image-caption-hover' ),
                'help' => __( 'Choose border type (available in pro version)', 'image-caption-hover' ),
            ),

            array(
                'type' => 'text',
                'name' => 'boxshadow',
                'title' => __( 'Shadow', 'image-caption-hover' ),
                'help' => __( 'Shadow for images (available in pro version)', 'image-caption-hover' ),
                'disabled' => 'true',
            ),
        );

        return $all_fields;
    }

    function render_settings_fields($key){
        global $post;
        $all_fields = $this->get_all_fields_data();
        $wcp_settings = get_post_meta($post->ID, 'ich_cpt' ,true);
        $saved_opt = (isset($wcp_settings[$key])) ? $wcp_settings[$key] : array() ;

        foreach ($all_fields as $field) {

            $field_name = 'ich_cpt['.$key.']['.$field['name'].']';
            $field_id = $field['name'].$key;
            $field_value = (isset($saved_opt[$field['name']])) ? $saved_opt[$field['name']] : '' ;            
            $disabled = (isset($field['disabled']) && $field['disabled'] == 'true') ? 'disabled' : '' ;

            switch ($field['type']) {

                case 'image': ?>

                    <div class="form-group">
                        <label for="<?php echo $field_id; ?>" class="col-sm-3 control-label">
                            <?php echo $field['title']; ?>
                        </label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="text" class="form-control input-sm image-url" id="<?php echo $field_id; ?>"
                                name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>">
                                <span class="input-group-btn">
                                    <button class="btn btn-info btn-sm upload_image_button" type="button">
                                        <?php _e( 'Media', 'image-caption-hover' ); ?></button>
                                </span>
                            </div>
                            <span class="help-block"><?php echo $field['help']; ?></span>
                        </div>
                    </div>
                    <?php break;

                case 'text': ?>

                    <div class="form-group">
                        <label for="<?php echo $field_id; ?>" class="col-sm-3 control-label"><?php echo $field['title']; ?></label>
                        <div class="col-sm-9">
                            <input type="text" <?php echo $disabled; ?> name="<?php echo $field_name; ?>" class="form-control input-sm" id="<?php echo $field_id; ?>" value="<?php echo $field_value; ?>">
                            <span class="help-block"><?php echo $field['help']; ?></span>
                        </div>
                    </div>
                    <?php break;

                case 'caption': ?>

                    <div class="form-group">
                        <label for="<?php echo $field_id; ?>" class="col-sm-3 control-label"><?php echo $field['title']; ?></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <textarea id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" class="form-control custom-control" rows="2" style="resize:none"><?php echo stripcslashes($field_value); ?></textarea>     
                                <span class="input-group-addon btn btn-info ich-open-editor"><?php _e( 'Editor', 'image-caption-hover' ); ?></span>
                            </div>                            
                            <span class="help-block"><?php echo $field['help']; ?></span>
                        </div>
                    </div>
                    <?php break;

                case 'select': ?>

                    <div class="form-group">
                        <label for="<?php echo $field_id; ?>" class="col-sm-3 control-label"><?php echo $field['title']; ?></label>
                        <div class="col-sm-9">
                            <select name="<?php echo $field_name; ?>" id="<?php echo $field_id; ?>" class="form-control input-sm">
                                <?php
                                if (isset($field['options']) && $field['options'] != '') {
                                    foreach ($field['options'] as $val => $label) {
                                        $selected = ($field_value == $val) ? 'selected' : '' ;
                                        $disabled = (strpos($val, 'disabled')) ? 'disabled' : '' ;

                                        echo '<option value="'.$val.'" '.$selected.' '.$disabled.'>'.$label.'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <span class="help-block"><?php echo $field['help']; ?></span>
                        </div>
                    </div>
                    <?php break;

                case 'color': ?>

                    <div class="form-group">
                        <label for="<?php echo $field_id; ?>" class="col-sm-3 control-label"><?php echo $field['title']; ?></label>
                        <div class="col-sm-9">
                            <span class="wcp-color-wrap">
                                <input type="text" name="<?php echo $field_name; ?>" id="<?php echo $field_id; ?>" value="<?php echo $field_value; ?>" class="colorpicker" data-alpha="true">
                            </span>
                            <span class="help-block"><?php echo $field['help']; ?></span>
                        </div>
                    </div>
                    <?php break;

                case 'checkbox': ?>

                    <div class="form-group">
                        <label for="<?php echo $field_id; ?>" class="col-sm-3 control-label"><?php echo $field['title']; ?></label>
                        <div class="col-sm-9">
                            <div class="checkbox">
                                <label>
                                    <?php $checked = ($field_value != '') ? 'checked' : '' ; ?>
                                    <input type="checkbox" disabled name="<?php echo $field_name; ?>" id="<?php echo $field_id; ?>" <?php echo $checked; ?>> <?php _e( 'Enable', 'image-caption-hover' ); ?>
                                </label>
                                <span class="help-block"><?php echo $field['help']; ?></span>
                            </div>                            
                        </div>
                    </div>
                    <?php break;

                case 'hovereffects': ?>

                    <div class="form-group">
                        <label for="<?php echo $field_id; ?>" class="col-sm-3 control-label"><?php echo $field['title']; ?></label>
                        <div class="col-sm-9">
                            <select name="<?php echo $field_name; ?>" id="<?php echo $field_id; ?>" class="form-control input-sm">
                                <?php
                                $free_hovers = $this->get_free_effects();
                                foreach ($free_hovers as $className) {
                                    $selected = ($field_value == $className) ? 'selected' : '' ; ?>
                                    <option value="<?php echo $className; ?>" <?php echo $selected; ?>><?php echo ucwords(str_replace("-"," ",$className)) ?></option>
                                <?php }
                                $free_ihovers = $this->get_ihover_effects();
                                foreach ($free_ihovers as $className) {
                                    $selected = ($field_value == $className) ? 'selected' : '' ; ?>
                                    <option value="<?php echo $className; ?>" <?php echo $selected; ?>><?php echo ucwords(str_replace("_"," ",$className)) ?></option>
                                <?php }
                                ?>
                            </select>
                            <span class="help-block"><?php echo $field['help']; ?></span>
                        </div>
                    </div>
                    <?php break;
                
                default:
                    
                    break;
            }
        }
    }

    function get_free_effects(){
        $hoverEffects = array(
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
                'no-hover-still-caption',
        );

        return $hoverEffects;
    }

    function get_ihover_effects(){
        $hoverEffects = array(
            'circle effect2 left_to_right',
            'circle effect2 right_to_left',
            'circle effect2 top_to_bottom',
            'circle effect2 bottom_to_top',
            'circle effect3 left_to_right',
            'circle effect3 right_to_left',
            'circle effect3 bottom_to_top',
            'circle effect3 top_to_bottom',
            'circle effect4 left_to_right',
            'circle effect4 right_to_left',
            'circle effect4 top_to_bottom',
            'circle effect4 bottom_to_top',
            'circle effect5',
            'circle effect6 scale_up',
            'circle effect6 scale_down',
            'circle effect6 scale_down_up',
            'circle effect7 left_to_right',
            'circle effect7 right_to_left',
            'circle effect7 top_to_bottom',
            'circle effect7 bottom_to_top',
            'circle effect8 left_to_right',
            'circle effect8 right_to_left',
            'circle effect8 top_to_bottom',
            'circle effect8 bottom_to_top',
            'circle effect9 left_to_right',
            'circle effect9 right_to_left',
            'circle effect9 top_to_bottom',
            'circle effect9 bottom_to_top',
            'circle effect10 top_to_bottom',
            'circle effect10 bottom_to_top',
            'circle effect11 left_to_right',
            'circle effect11 right_to_left',
            'circle effect11 top_to_bottom',
            'circle effect11 bottom_to_top',
            'circle effect12 left_to_right',
            'circle effect12 right_to_left',
            'circle effect12 top_to_bottom',
            'circle effect12 bottom_to_top',
            'circle effect13 from_left_and_right',
            'circle effect13 top_to_bottom',
            'circle effect13 bottom_to_top',
            'circle effect14 left_to_right',
            'circle effect14 right_to_left',
            'circle effect14 top_to_bottom',
            'circle effect14 bottom_to_top',
            'circle effect15 left_to_right',
            'circle effect16 left_to_right',
            'circle effect16 right_to_left',
            'circle effect17',
            'circle effect18 bottom_to_top',
            'circle effect18 left_to_right',
            'circle effect18 right_to_left',
            'circle effect18 top_to_bottom',
            'circle effect19',
            'circle effect20 top_to_bottom',
            'circle effect20 bottom_to_top',

            'square effect1 left_and_right',
            'square effect1 top_to_bottom',
            'square effect1 bottom_to_top',
            'square effect2',
            'square effect3 bottom_to_top',
            'square effect3 top_to_bottom',
            'square effect4',
            'square effect5 left_to_right',
            'square effect5 right_to_left',
            'square effect6 from_top_and_bottom',
            'square effect6 from_left_and_right',
            'square effect6 top_to_bottom',
            'square effect6 bottom_to_top',
            'square effect7',
            'square effect8 scale_up',
            'square effect8 scale_down',
            'square effect9 bottom_to_top',
            'square effect9 left_to_right',
            'square effect9 right_to_left',
            'square effect9 top_to_bottom',
            'square effect10 left_to_right',
            'square effect10 right_to_left',
            'square effect10 top_to_bottom',
            'square effect10 bottom_to_top',
            'square effect11 left_to_right',
            'square effect11 right_to_left',
            'square effect11 top_to_bottom',
            'square effect11 bottom_to_top',
            'square effect12 left_to_right',
            'square effect12 right_to_left',
            'square effect12 top_to_bottom',
            'square effect12 bottom_to_top',
            'square effect13 left_to_right',
            'square effect13 right_to_left',
            'square effect13 top_to_bottom',
            'square effect13 bottom_to_top',
            'square effect14 left_to_right',
            'square effect14 right_to_left',
            'square effect14 top_to_bottom',
            'square effect14 bottom_to_top',
            'square effect15 left_to_right',
            'square effect15 right_to_left',
            'square effect15 top_to_bottom',
            'square effect15 bottom_to_top',
        );

        return $hoverEffects;
    }

    function has_info_class($hovereffect){
        $hoverEffects = array(
            'circle effect5',
            'circle effect13 from_left_and_right',
            'circle effect13 top_to_bottom',
            'circle effect13 bottom_to_top',
            'circle effect18 bottom_to_top',
            'circle effect18 left_to_right',
            'circle effect18 right_to_left',
            'circle effect18 top_to_bottom',
            'circle effect20 top_to_bottom',
            'circle effect20 bottom_to_top',

            'square effect9 bottom_to_top',
            'square effect9 left_to_right',
            'square effect9 right_to_left',
            'square effect9 top_to_bottom',
        );

        if (in_array($hovereffect, $hoverEffects)) {
            return true;           
        } else {
            return false;
        }
    }

}
?>