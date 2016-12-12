<?php
/*
 * Plugin Name: Child Themify
 * Description: Create child themes at the click of a button.
 * Version: 1.2.0
 * Plugin URI: https://github.com/johnpbloch/child-themify
 * Author: John P. Bloch
 * License: GPL-2.0+
 * Text Domain: child-themify
 * Domain Path: /languages
 */

define( 'CTF_PATH', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'CTF_URL', WP_PLUGIN_URL . '/' . basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'CTF_VERSION', '1.2.0' );


function ctf_plugins_loaded() {
	if ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) {
		global $child_themify;
		require_once dirname( CTF_PATH ) . '/includes/plugin.php';
		$child_themify = new Child_Themify();
		add_action( 'init', array( $child_themify, 'init' ) );
	} else {
		require_once dirname( CTF_PATH ) . '/includes/legacy.php';
		add_action( 'init', array( 'CTF_Babymaker', 'init' ) );
	}
}

add_action( 'plugins_loaded', 'ctf_plugins_loaded' );
