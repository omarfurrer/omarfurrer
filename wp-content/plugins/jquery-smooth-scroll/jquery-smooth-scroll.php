<?php 
/*
Plugin Name: jQuery Smooth Scroll
Version: 1.4.1
Plugin URI: http://www.blogsynthesis.com/wordpress-jquery-smooth-scroll-plugin/
Description: The plugin not only add smooth scroll to top feature/link in the lower-right corner of long pages while scrolling but also makes all jump links to scroll smoothly.
Author: BlogSynthesis
Author URI: http://www.blogsynthesis.com/
License: GPL v3

jQuery Smooth Scroll
Copyright (C) 2013-16, Anand Kumar <anand@anandkumar.net>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/****************************************************************		
 *
 *     THERE ARE A FEW THINGS YOU SHOULD MUST KNOW
 *               BEFORE EDITING THE PLUGIN	
 *                                     
 *                  FOR DETAILS VISIT:
 *          http://www.blogsynthesis.com/?p=860
 *								*
 ****************************************************************/
	
// Prevent loading this file directly - Busted!
if ( ! class_exists( 'WP' ) )
{
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}


if ( !class_exists( 'jQuerySmoothScroll' ) ) {
	
	class jQuerySmoothScroll {
	
		public function __construct() {
	
			$blogsynthesis_jss_plugin_url = trailingslashit ( WP_PLUGIN_URL . '/' . dirname ( plugin_basename ( __FILE__ ) ) );
			$pluginname = 'jQuery Smooth Scroll';
			$plugin_version = '1.4.1';

			// load plugin Scripts
			//changed to action 'wp_enqueue_scripts' as its the recommended way to enqueue scripts and styles
			// see http://codex.wordpress.org/Plugin_API/Action_Reference/wp_enqueue_scripts
			add_action( 'wp_enqueue_scripts',  array( &$this, 'wp_head') ); 
			
			// add move to top button at wp_footer
			add_action( 'wp_footer',  array( &$this, 'wp_footer') );

		}

		// load our css to the head
		public function wp_head() {

			if ( !is_admin() ) {
				global $blogsynthesis_jss_plugin_url;

				// register and enqueue CSS
				wp_register_style( 'jquery-smooth-scroll', plugin_dir_url( __FILE__ ) . 'css/jss-style.css', false );
				wp_enqueue_style( 'jquery-smooth-scroll' );
				
				// enqueue script
				wp_enqueue_script('jquery');
				$extension='.min.js';
				if( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) {
					$extension='.js';
				}
				wp_enqueue_script( 'jquery-smooth-scroll',  plugin_dir_url( __FILE__ ) . 'js/jss-script'.$extension, array('jquery'),false, true );
				
				// You may now choose easing effect. For more information visit http://www.blogsynthesis.com/?p=860
				// wp_enqueue_script("jquery-effects-core");
			}
		}

		public function wp_footer() {
			// the html button which will be added to wp_footer ?>
			<a id="scroll-to-top" href="#" title="<?php _e('Scroll to Top','blogsynthesis'); ?>"><?php _e('Top','blogsynthesis'); ?></a>
			<?php
		}

	}


	//////////////////////////////////////////////////////////////////////////////

	add_action('wp_dashboard_setup', 'blogsynthesis_jss_dashboard_widgets');
		
	function blogsynthesis_jss_dashboard_widgets() {
			global $wp_meta_boxes;
			wp_add_dashboard_widget('blogsynthesisshfswidget', 'Latest from BlogSynthesis', 'blogsynthesis_jss_widget');
	}		

	function blogsynthesis_jss_widget() {		
		include_once( ABSPATH . WPINC . '/feed.php' );
		
		$rss = fetch_feed( 'http://feeds2.feedburner.com/blogsynthesis' );
		
		if ( ! is_wp_error( $rss ) ) :

			// Figure out how many total items there are, but limit it to 10. 
			$maxitems = $rss->get_item_quantity( 10 ); 

			// Build an array of all the items, starting with element 0 (first element).
			$rss_items = $rss->get_items( 0, $maxitems );

		endif; 
		
		{ ?>
			<div class="rss-widget">
				<a href="http://www.blogsynthesis.com/support/#utm_source=wpadmin&utm_medium=dashboardwidget&utm_term=newsitemlogo&utm_campaign=jss" title="BlogSynthesis - For Bloggers" target="_blank"><img src="<?php  echo plugin_dir_url( __FILE__ ); ?>images/blogsynthesis-100px.png"  class="alignright" alt="BlogSynthesis"/></a>	
				<ul>
					<?php if ( $maxitems == 0 ) : ?>
						<li><?php _e( 'No items', 'shfs-text-domain' ); ?></li>
					<?php else : ?>
						<?php // Loop through each feed item and display each item as a hyperlink. ?>
						<?php foreach ( $rss_items as $item ) : ?>
							<li>
								<a href="<?php echo esc_url( $item->get_permalink() ); ?>#utm_source=wpadmin&utm_medium=dashboardwidget&utm_term=newsitem&utm_campaign=shfs"
									title="<?php printf( __( 'Posted %s', 'shfs-text-domain' ), $item->get_date('j F Y | g:i a') ); ?>" target="_blank">
									<?php echo esc_html( $item->get_title() ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
				<div style="border-top: 1px solid #ddd; padding-top: 10px; text-align:center;">
					<span class="addthis_toolbox addthis_default_style" style="float:left;">
					<a class="addthis_button_facebook_follow" addthis:userid="blogsynthesis"></a>
					<a class="addthis_button_twitter_follow" addthis:userid="blogsynthesis"></a>
					<a class="addthis_button_google_follow" addthis:userid="+BlogSynthesis"></a>
					<a class="addthis_button_rss_follow" addthis:userid="http://feeds2.feedburner.com/blogsynthesis"></a>
					</span>
					<a href="http://www.blogsynthesis.com/support/#utm_source=wpadmin&utm_medium=dashboardwidget&utm_term=newsitemlogo&utm_campaign=jss"><img src="<?php  echo plugin_dir_url( __FILE__ ); ?>images/email-16px.png" alt="Subscribe via Email"/>Get Plugin Support</a>
					<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-525ab1d176544441"></script>
				</div>
			</div>
	<?php }
		
	}
	
$jQuerySmoothScroll = new jQuerySmoothScroll();

}