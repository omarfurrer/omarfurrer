<div class="wrap">
	<h2><?php _e( 'Image Caption Hover Settings', 'image-caption-hover' ); ?></h2>
	<?php 
	    global $wp_roles;
	    $roles = $wp_roles->get_names();
	    $saved_role = get_option( 'wcp_ich_admin_settings' );
	    $saved_all_settings = get_option( 'wcp_ich_admin_settings_all' );
	?>
	<table class="wp-list-table widefat">
		<tr>
			<th><?php _e( 'Who Can Edit?', 'image-caption-hover' ); ?></th>
			<td>
				<select class="who_can_edit widefat">
					<?php 
						foreach ($roles as $key => $role) { ?>
						<option value="<?php echo $key; ?>" <?php selected( $saved_role, $key ); ?>><?php echo $role; ?></option>
					<?php } ?>
				</select>
			</td>
			<td>
				<p class="description"><?php _e( 'Select the Role who can manage Image Caption Hover options.', 'image-caption-hover' ); ?>
				<a target="_blank" href="https://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table">Help</a>
				</p>
			</td>
		</tr>
		<tr>
			<th><?php _e( 'Touch Devices Behavior for Links', 'image-caption-hover' ); ?></th>
			<td>
				<select class="touch_behavior widefat">
					<option value="default" <?php echo (isset($saved_all_settings['touch']) && $saved_all_settings['touch'] == 'default') ? 'selected' : '' ; ?>><?php _e( 'Default', 'image-caption-hover' ); ?></option>
					<option value="first_hover" <?php echo (isset($saved_all_settings['touch']) && $saved_all_settings['touch'] == 'first_hover') ? 'selected' : '' ; ?>><?php _e( 'First Tap Hover, Second Tap Navigate', 'image-caption-hover' ); ?></option>
				</select>				
			</td>
			<td>
				<p class="description">
					<?php _e( 'Here you can control touch behavior for links', 'image-caption-hover' ); ?>
				</p>
			</td>
		</tr>
	</table>
	<br>
	<button class="button-primary wcp-save"><?php _e( 'Save Settings', 'image-caption-hover' ); ?></button>
	<img src="<?php echo plugin_dir_url(__FILE__).'images/ajax-loader.gif' ?>" alt="" class="nm-loading" style="display: none;">
	<span class="nm-saved" style="display: none;"><?php _e( 'Changes Saved', 'image-caption-hover' ); ?>!</span>				
</div>