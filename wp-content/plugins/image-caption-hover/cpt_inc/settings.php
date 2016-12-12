<div class="group panel panel-default" id="<?php echo $key; ?>">
	<h3 class="panel-heading"><?php echo (isset($options['imagetitle']) && $options['imagetitle'] != '') ? $options['imagetitle'] : 'Image Caption Hover' ; ?></h3>
	<div class="panel-body form-horizontal">
		<?php $this->render_settings_fields($key); ?>
		<button class="btn btn-danger pull-right btn-sm button-delete"><?php _e( 'Delete', 'image-caption-hover' ); ?></button>
		<div style="clear: both;"></div>
		<br>
	</div>
</div>