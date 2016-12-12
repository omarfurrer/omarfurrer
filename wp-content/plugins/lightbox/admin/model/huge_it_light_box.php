<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Hugeit_Lightbox_Model
 */
class Hugeit_Lightbox_Model {

	/**
	 * @return array
	 */
	public function get_list() {
		global $wpdb;

		$rows = $wpdb->get_results( "SELECT *  FROM " . $wpdb->prefix . "hugeit_lightbox" );
		$hugeit_lightbox_values = array();

		foreach ( $rows as $row ) {
			$hugeit_lightbox_values[ $row->name ] = $row->value;
		}

		return $hugeit_lightbox_values;
	}

	public function save() {
		global $wpdb;

		$free_features = array('light_box_style', 'light_box_transition', 'light_box_speed', 'light_box_fadeout', 'light_box_title');

		if ( isset( $_POST['params'] ) ) {

			foreach ( $_POST['params'] as $key => $value ) {
				if (!in_array($key, $free_features)) {
					continue;
				}
				if ( $value == '' ) {
					$value = 0;
				}
				$wpdb->update(
					$wpdb->prefix . 'hugeit_lightbox',
					array( 'value' => sanitize_text_field( $value ) ),
					array( 'name' => $key ),
					'%s'
				);
			}
			?>
			<div class="updated"><p><strong><?php _e( 'Item Saved' ); ?></strong></p></div>
			<?php
		}
	}
}
