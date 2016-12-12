<?php
/**
* Plugin Main Class
*/
class Image_Caption_Hover
{
	
	function __construct()
	{
		add_action( 'admin_menu', array( $this, 'ich_admin_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_options_page_scripts' ) );
		add_action('wp_ajax_wcp_save_image_caption_hovers', array($this, 'save_settings'));
		add_action( 'wp_enqueue_scripts', array($this, 'adding_front_scripts') );
		add_shortcode( 'image-caption-hover', array( $this, 'render_all_shortcodes' ) );
		add_action( 'plugins_loaded', array($this, 'wcp_load_plugin_textdomain' ) );

		add_action('wp_ajax_save_ich_settings', array($this, 'save_ich_settings'));
	}

	function adding_front_scripts(){
		wp_register_style( 'image-caption-hover-css', plugins_url( 'css/style.css' , __FILE__ ));
	}

	function admin_options_page_scripts($slug){
		if ($slug == 'ich_cpt_page_ich_admin') {
			wp_enqueue_style( 'image-caption-hover-css', plugins_url( 'css/style.css' , __FILE__ ));
			wp_enqueue_script( 'image-caption-hover-js', plugins_url( 'js/script.js' , __FILE__ ), array('jquery') );
			wp_localize_script( 'image-caption-hover-js','wcp_all_settings', get_option( 'wcp_ich_admin_settings_all' ) );
			wp_enqueue_media();
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker-alpha', plugins_url( 'js/wp-color-picker-alpha.min.js', __FILE__ ), array( 'wp-color-picker' ));
			wp_enqueue_script( 'ich-admin-js', plugins_url( 'admin/script.js' , __FILE__ ), array('jquery', 'jquery-ui-accordion', 'wp-color-picker') );
			wp_enqueue_style( 'ich-admin-css', plugins_url( 'admin/style.css' , __FILE__ ));
			wp_localize_script( 'ich-admin-js', 'wcpAjax', array( 'url' => admin_url( 'admin-ajax.php' ), 'path' => plugin_dir_url( __FILE__ )));
		}
		if ($slug == 'ich_cpt_page_ich_admin_settings') {
			wp_enqueue_script( 'ich-admin-settings-js', plugins_url( 'admin/settings.js' , __FILE__ ), array('jquery') );
		}
		if ($slug == 'ich_cpt_page_ich_grid_builder') {
			wp_enqueue_script( 'ich-admin-builder-js', plugins_url( 'admin/builder.js' , __FILE__ ), array('jquery') );
		}
	}

	function wcp_load_plugin_textdomain(){
		load_plugin_textdomain( 'image-caption-hover', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	function save_settings(){
		if (isset($_REQUEST)) {
			update_option( 'wcp_ich_plugin', $_REQUEST );
		}

		die(0);
	}

	function save_ich_settings(){
		if (isset($_REQUEST['role'])) {
			update_option( 'wcp_ich_admin_settings', $_REQUEST['role'] );
			update_option( 'wcp_ich_admin_settings_all', $_REQUEST );
		}

		die(0);	
	}

	function ich_admin_options(){

		$allCaptions = get_option('wcp_ich_plugin');

		$saved_role = get_option( 'wcp_ich_admin_settings' );
		$role_object = get_role( $saved_role );
		$first_key = '';
		if (isset($role_object->capabilities) && is_array($role_object->capabilities)) {
			reset($role_object->capabilities);
			$first_key = key($role_object->capabilities);
		}
		if ($first_key == '') {
			$first_key = 'manage_options';
		}

		if (isset($allCaptions['widgets'])) {
			add_submenu_page( 'edit.php?post_type=ich_cpt', 'Single Images', 'Single Images', $first_key, 'ich_admin' , array($this, 'render_menu_page'));
			add_submenu_page('edit.php?post_type=ich_cpt', 'Grid Builder', 'Grid Builder', $first_key, 'ich_grid_builder', array($this, 'render_grid_builder') );
			add_submenu_page('edit.php?post_type=ich_cpt', 'Import or Export Image Caption Hover', 'Import/Export', $first_key, 'ich_import_export', array($this, 'render_import_export') );
		}
		add_submenu_page('edit.php?post_type=ich_cpt', 'Image Caption Hover Settings', 'Settings', 'manage_options', 'ich_admin_settings', array($this, 'render_ich_admin_settings') );
	}

	function render_ich_admin_settings(){
		include 'includes/settings.php';
	}

	function render_other_plugins(){
		include 'includes/plugins.php';
	}

	function render_import_export(){
		include 'includes/import_export.php';
	}	

	function render_grid_builder(){
		include ('grid_builder.php');
	}

	function render_menu_page(){
		$allCaptions = get_option('wcp_ich_plugin');
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
			<div class="wrap" id="photo-book">
				<h2><?php _e( 'Image Caption Hover Settings', 'image-caption-hover' ); ?> <a href="http://webcodingplace.com/wordpress-pro-plugins/image-caption-hover-pro-wordpress-plugin/" target="_blank" class="add-new-h2"><?php _e( 'Unlock Pro Features', 'image-caption-hover' ); ?>
				<a href="http://webcodingplace.com/how-to-use-image-caption-hover-wordpress-plugin/" target="_blank" class="add-new-h2"><?php _e( 'How to Use', 'image-caption-hover' ); ?></a></h2>
				<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
					<p>
						Want to create Sortable Image Gallery with Hover Effects? <strong><a target="_blank" href="https://wordpress.org/plugins/fancy-grid-gallery/">Try Fancy Grid Gallery FREE</a>.</strong>
					</p>
					<button type="button" class="notice-dismiss">
						<span class="screen-reader-text">Dismiss this notice.</span>
					</button>
				</div>
				<div id="accordion">
				<?php if (isset($allCaptions['widgets'])) { ?>
				
					<?php foreach ($allCaptions['widgets'] as $key => $data) {
						include 'includes/saved_options.php';
					 } ?>
				<?php } else {
					include 'includes/load_first.php';		
				} ?>
				</div>

				<hr style="clear: both;">
				<button class="button-primary save-pages"><?php _e( 'Save Changes', 'image-caption-hover' ); ?></button>
				<span id="wcp-loader"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/ajax-loader.gif"></span>
				<span id="wcp-saved"><strong><?php _e( 'Changes Saved', 'image-caption-hover' ); ?>!</strong></span>				
			</div>
		<?php
	}

	function render_all_shortcodes($atts, $content, $the_shortcode){

		$allCaptions = get_option('wcp_ich_plugin');
		// print_r($allCaptions);
		if (isset($allCaptions['widgets'])) {
			foreach ($allCaptions['widgets'] as $key => $data) {
				extract($data);
				if ($atts['id'] == $data['counter']) {
					ob_start();
					include 'includes/render_shortcode.php';
					return ob_get_clean();
				}
			}
		}		
	}
}

?>