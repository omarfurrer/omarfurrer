<?php
	global $post;
	$ichcpt_settings = get_post_meta($post->ID, 'ichcpt_settings', true);
	// print_r($ichcpt_settings);
?>
<table style="padding: 5px;width:100%;">
	<tr>
		<td><?php _e( 'Display', 'image-caption-hover' ); ?></td>
		<td>
			<select name="ichcpt_settings[columns]" class="widefat">
				<?php for($i = 1; $i <= 12; $i++) { ?>
                <option value="<?php echo $i; ?>" <?php if(isset($ichcpt_settings['columns']) && $ichcpt_settings['columns'] == $i){echo 'selected';} ?>><?php echo $i; ?></option>
                <?php } ?>
			</select>			
		</td>
		<td>
			<p class="description"><?php _e( 'Images in a row', 'image-caption-hover' ); ?>.</p>
		</td>
	</tr>
	<tr>
		<td><?php _e( 'Space between images', 'image-caption-hover' ); ?></td>
		<td><input name="ichcpt_settings[col_space]" type="text" value="<?php echo (isset($ichcpt_settings['col_space'])) ? $ichcpt_settings['col_space'] : '10px' ; ?>" class="widefat"></td>
		<td>
			<p class="description"><?php _e( 'Space between each column', 'image-caption-hover' ); ?>.</p>
		</td>
	</tr>
	<tr>
		<td><?php _e( 'Shortcodes in Caption', 'image-caption-hover' ); ?></td>
		<td>
			<label>
				<input name="ichcpt_settings[caption_shortcodes]" type="checkbox" <?php echo (isset($ichcpt_settings['caption_shortcodes'])) ? 'checked' : '' ; ?>>
				<?php _e( 'Enable', 'image-caption-hover' ); ?>
			</label>
		</td>
		<td>
			<p class="description"><?php _e( 'Check to enable shortcodes in caption text box', 'image-caption-hover' ); ?>.</p>
		</td>
	</tr>
	<tr>
		<td><?php _e( 'Custom CSS', 'image-caption-hover' ); ?></td>
		<td>
			<label>
				<textarea name="ichcpt_settings[custom_css]" class="widefat"><?php echo (isset($ichcpt_settings['custom_css'])) ? stripcslashes($ichcpt_settings['custom_css']) : '' ; ?></textarea>
			</label>
		</td>
		<td>
			<p class="description"><?php _e( 'You can define your own styles here', 'image-caption-hover' ); ?>.</p>
		</td>
	</tr>
</table>