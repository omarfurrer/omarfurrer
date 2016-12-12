<div class="wrap">
	<h2><?php _e( 'Grid Builder', 'image-caption-hover' ); ?> <a href="http://webcodingplace.com/image-caption-hover-pro-wordpress-plugin/" class="add-new-h2"><?php _e( 'It will work in Pro Version', 'image-caption-hover' ); ?></a></h2>
		<p class="description"><?php _e( 'It is used to display single images in a row', 'image-caption-hover' ); ?></p>

		<div class="wrap-cap">
			<select class="allcaptions widefat">
				<?php
					$allCaptions = get_option('wcp_ich_plugin');

					foreach ($allCaptions['widgets'] as $wid) {
						$shortcode = $wid['shortcode'];
						$name = ($wid['refname'] != '') ? $wid['refname'] : 'title not set' ;
						echo '<option value="'.$shortcode.'">'.$name.'</option>';
					}

				?>
			</select>
		</div>
		<table>
			<tr>
				<th><?php _e( 'Number of Columns in Row', 'image-caption-hover' ); ?></th>
				<td>
					<select id="noofcolumns">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
					</select>
				</td>
				<td><button class="button-secondary add-table"><?php _e( 'Insert', 'image-caption-hover' ); ?></button></td>
			</tr>
		</table>
		<div class="append-table">
			
		</div>

		<br>
		<button class="button-primary get-sc"><?php _e( 'Get Shortcode', 'image-caption-hover' ); ?></button>
</div>
<style>
	.wrap-cap select { display: none; }
	.wp-list-table.widefat.fixed {text-align: center;}
</style>
