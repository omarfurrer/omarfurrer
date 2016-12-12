<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$hugeit_lightbox_values = $this->model->get_list();

require_once 'free_banner.php';
?>
<div id="post-body-heading" class="post-body-line">
	<h3>General Options</h3>
	<a onclick="document.getElementById('adminForm').submit()" class="save-lightbox-options button-primary">Save</a>
</div>
<div id="lightbox-options-list">
	<form action="<?php echo wp_nonce_url('admin.php?page=huge_it_light_box&hugeit_task=save', 'save_settings', 'hugeit_lightbox_save_settings_nonce') ?>" method="post" id="adminForm" name="adminForm">
		<div class="options-block">
			<h3>Main Features</h3>
			<div class="has-background">
				<label for="light_box_style">Lightbox style
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Choose the style of your popup</p>
						</div>
					</div>
				</label>
				<select id="light_box_style" name="params[light_box_style]">
					<option <?php if($hugeit_lightbox_values['light_box_style'] == '1') echo 'selected="selected"';  ?> value="1">1</option>
					<option <?php if($hugeit_lightbox_values['light_box_style'] == '2') echo 'selected="selected"';  ?> value="2">2</option>
					<option <?php if($hugeit_lightbox_values['light_box_style'] == '3') echo 'selected="selected"';  ?> value="3">3</option>
					<option <?php if($hugeit_lightbox_values['light_box_style'] == '4') echo 'selected="selected"';  ?> value="4">4</option>
					<option <?php if($hugeit_lightbox_values['light_box_style'] == '5') echo 'selected="selected"';  ?> value="5">5</option>
				</select>
				<div id="view-style-block">
					<span class="view-style-eye"><?php _e( 'Preview', 'hugeit_lightbox' ); ?></span><ul>
						<li data-id="1" class="active"><img src="<?php echo plugins_url('../../images/view1.jpg', __FILE__); ?>"></li>
						<li data-id="2"><img src="<?php echo plugins_url('../../images/view2.jpg', __FILE__); ?>"></li>
						<li data-id="3"><img src="<?php echo plugins_url('../../images/view3.jpg', __FILE__); ?>"></li>
						<li data-id="4"><img src="<?php echo plugins_url('../../images/view4.jpg', __FILE__); ?>"></li>
						<li data-id="5"><img src="<?php echo plugins_url('../../images/view5.jpg', __FILE__); ?>"></li>
					</ul>
				</div>

			</div>
			<div>
				<label for="light_box_transition">Transition type
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set the way of opening the popup.</p>
						</div>
					</div>
				</label>
				<select id="light_box_transition" name="params[light_box_transition]">
					<option <?php if($hugeit_lightbox_values['light_box_transition'] == 'elastic') echo 'selected="selected"';  ?> value="elastic">Elastic</option>
					<option <?php if($hugeit_lightbox_values['light_box_transition'] == 'fade') echo 'selected="selected"';  ?> value="fade">Fade</option>
					<option <?php if($hugeit_lightbox_values['light_box_transition'] == 'none') echo 'selected="selected"';  ?> value="none">none</option>
				</select>
			</div>
			<div class="has-background">
				<label for="light_box_speed">Opening speed
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set the speed of opening the popup in milliseconds..</p>
						</div>
					</div>
				</label>
				<input type="number" name="params[light_box_speed]" id="light_box_speed" value="<?php echo esc_attr($hugeit_lightbox_values['light_box_speed']); ?>" class="text">
				<span>ms</span>
			</div>
			<div>
				<label for="light_box_fadeout">Closing speed
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set the speed of closing the popup in milliseconds.</p>
						</div>
					</div>
				</label>
				<input type="number" name="params[light_box_fadeout]" id="light_box_fadeout" value="<?php echo esc_attr($hugeit_lightbox_values['light_box_fadeout']); ?>" class="text">
				<span>ms</span>
			</div>
			<div class="has-background">
				<label for="light_box_title">Show the title
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Choose whether to display the content title.</p>
						</div>
					</div>
				</label>
				<input type="hidden" value="false" name="params[light_box_title]" />
				<input type="checkbox" id="light_box_title" <?php if($hugeit_lightbox_values['light_box_title']  == 'true') echo 'checked="checked"'; ?>  name="params[light_box_title]" value="true" />
			</div>
		</div>
		<div class="options-block hugeit-lightbox-pro-option">
			<h3>Additional Options<img src="<?php echo plugins_url('../../images/pro-icon.png', __FILE__) ?>" class="hugeit_lightbox_pro_logo"></h3>
			<div class="has-background hugeit-lightbox-pro-option">
				<label for="light_box_opacity">Overlay transparency
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Change the level of popup background transparency.</p>
						</div>
					</div>
				</label>
				<div class="slider-container">
					<input id="light_box_opacity" data-slider-highlight="true"  data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true" value="20" disabled="disabled" />
					<span>20%</span>
				</div>
			</div>
			<div class="hugeit-lightbox-pro-option">
				<label for="light_box_open">Auto open
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Choose for automatically opening the firs content after reloading.</p>
						</div>
					</div>
				</label>
				<input type="hidden" value="false"  />
				<input type="checkbox" id="light_box_open" value="true" disabled="disabled" />
			</div>
			<div class="has-background hugeit-lightbox-pro-option">
				<label for="light_box_overlayclose">Overlay close
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Choose to close the content by clicking on the overlay.</p>
						</div>
					</div>
				</label>
				<input type="hidden" value="false"  />
				<input type="checkbox" id="light_box_overlayclose" value="true" checked="checked" disabled="disabled" />
			</div>
			<div class="hugeit-lightbox-pro-option">
				<label for="light_box_esckey">EscKey close
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Choose to close the content with esc button.</p>
						</div>
					</div>
				</label>
				<input type="hidden" value="false"  />
				<input type="checkbox" id="light_box_esckey" value="true" disabled="disabled" />
			</div>
			<div class="has-background hugeit-lightbox-pro-option">
				<label for="light_box_arrowkey">Keyboard navigation
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set to change the images with left and right buttons.</p>
						</div>
					</div>
				</label>
				<input type="hidden" value="false"  />
				<input type="checkbox" id="light_box_arrowkey" value="true" disabled="disabled" />
			</div>
			<div class="hugeit-lightbox-pro-option">
				<label for="light_box_loop">Loop content
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>Loop content. If �true� give the ability to move from the last
							image to the first image while navigation..</p>
						</div>
					</div>
				</label>
				<input type="hidden" value="false"  />
				<input type="checkbox" id="light_box_loop" value="true" checked="checked" disabled="disabled" />
			</div>
			<div class="has-background hugeit-lightbox-pro-option">
				<label for="light_box_closebutton">Show close button
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Choose whether to display close button.</p>
						</div>
					</div>
				</label>
				<input type="hidden" value="false" />
				<input type="checkbox" id="light_box_closebutton" value="true" checked="checked" disabled="disabled" />
			</div>
		</div>
		<div class="options-block hugeit-lightbox-pro-option">
			<h3>Dimensions<img src="<?php echo plugins_url('../../images/pro-icon.png', __FILE__) ?>" class="hugeit_lightbox_pro_logo"></h3>

			<div class="has-background">
				<label for="light_box_size_fix">Popup size fix
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Choose to fix the popup width and high.</p>
						</div>
					</div>
				</label>
				<input type="hidden" value="false"  />
				<input type="checkbox" id="light_box_size_fix" value="true" disabled="disabled" />
			</div>

			<div class="fixed-size" >
				<label for="light_box_width">Popup width
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Change the width of content.</p>
						</div>
					</div>
				</label>
				<input type="number"  id="light_box_width" value="500" class="text" disabled="disabled" />
				<span>px</span>
			</div>

			<div class="has-background fixed-size">
				<label for="light_box_height">Popup height
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Change the high of content.</p>
						</div>
					</div>
				</label>
				<input type="number"  id="light_box_height" value="500" class="text" disabled="disabled" />
				<span>px</span>
			</div>

			<div class="not-fixed-size">
				<label for="light_box_maxwidth">Popup maxWidth
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set unfix content max width.</p>
						</div>
					</div>
				</label>
				<input type="number"  id="light_box_maxwidth" value="768" class="text" disabled="disabled" />
				<span>px</span>
			</div>

			<div class="has-background not-fixed-size">
				<label for="light_box_maxheight">Popup maxHeight
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set unfix max hight.</p>
						</div>
					</div>
				</label>
				<input type="number"  id="light_box_maxheight" value="500" class="text"  disabled="disabled" />
				<span>px</span>
			</div>

			<div>
				<label for="light_box_initialwidth">Popup initial width
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set the initial size of opening.</p>
						</div>
					</div>
				</label>
				<input type="number"  id="light_box_initialwidth" value="300" class="text"  disabled="disabled" />
				<span>px</span>
			</div>

			<div class="has-background">
				<label for="light_box_initialheight">Popup initial height
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set the initial high of opening.</p>
						</div>
					</div>
				</label>
				<input type="number"  id="light_box_initialheight" value="100" class="text"  disabled="disabled" />
				<span>px</span>
			</div>
		</div>
		<div class="options-block hugeit-lightbox-pro-option">
			<h3>Slideshow<img src="<?php echo plugins_url('../../images/pro-icon.png', __FILE__) ?>" class="hugeit_lightbox_pro_logo"></h3>
			<div class="has-background">
				<label for="light_box_slideshow">Slideshow
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Select to enable slideshow.</p>
						</div>
					</div>
				</label>
				<input type="hidden" value="false"  />
				<input type="checkbox" id="light_box_slideshow" value="true" checked="checked" disabled="disabled" />
			</div>
			<div>
				<label for="light_box_slideshowspeed">Slideshow interval
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set the time between each slide.</p>
						</div>
					</div>
				</label>
				<input type="number"  id="light_box_slideshowspeed" value="2500" class="text" disabled="disabled" />
				<span>ms</span>
			</div>
			<div class="has-background">
				<label for="light_box_slideshowauto">Slideshow auto start
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>If �true� it works automatically.</p>
						</div>
					</div>
				</label>
				<input type="hidden" value="false"  />
				<input type="checkbox" id="light_box_slideshowauto" value="true" checked="checked" disabled="disabled" />
			</div>
			<div>
				<label for="light_box_slideshowstart">Slideshow start button text
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set the text on start button.</p>
						</div>
					</div>
				</label>
				<input type="text"  id="light_box_slideshowstart" value="start slideshow" class="text"  disabled="disabled" />
			</div>
			<div class="has-background">
				<label for="light_box_slideshowstop">Slideshow stop button text
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set the text of stop button.</p>
						</div>
					</div>
				</label>
				<input type="text"  id="light_box_slideshowstop" value="stop slideshow" class="text" disabled="disabled"/>
			</div>
		</div>
		<div class="options-block hugeit-lightbox-pro-option" style="margin-top:0px;">
			<h3>Positioning<img src="<?php echo plugins_url('../../images/pro-icon.png', __FILE__) ?>" class="hugeit_lightbox_pro_logo"></h3>

			<div class="has-background">
				<label for="light_box_fixed">Fixed position
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>If �true� the popup does not change it�s position while scrolling up or down.</p>
						</div>
					</div>
				</label>
				<input type="hidden" value="false"  />
				<input type="checkbox" id="light_box_fixed" checked="checked" value="true" disabled="disabled" />
			</div>
			<div class="has-height">
				<label for="">Popup position
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set the position of popup.</p>
						</div>
					</div>
				</label>
				<div>
					<table class="bws_position_table">
						<tbody>
						<tr>
							<td><input type="radio" value="1" id="slideshow_title_top-left" disabled="disabled" /></td>
							<td><input type="radio" value="2" id="slideshow_title_top-center" disabled="disabled" /></td>
							<td><input type="radio" value="3" id="slideshow_title_top-right" disabled="disabled" /></td>
						</tr>
						<tr>
							<td><input type="radio" value="4" id="slideshow_title_middle-left" disabled="disabled" /></td>
							<td><input type="radio" value="5" id="slideshow_title_middle-center" checked="checked" disabled="disabled" /></td>
							<td><input type="radio" value="6" id="slideshow_title_middle-right" disabled="disabled" /></td>
						</tr>
						<tr>
							<td><input type="radio" value="7" id="slideshow_title_bottom-left" disabled="disabled" /></td>
							<td><input type="radio" value="8" id="slideshow_title_bottom-center" disabled="disabled" /></td>
							<td><input type="radio" value="9" id="slideshow_title_bottom-right" disabled="disabled" /></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-----------------------Lightbox Watermark html----------------------------------->
		<div class="options-block hugeit-lightbox-pro-option">
			<h3>Lightbox Watermark styles<img src="<?php echo plugins_url('../../images/pro-icon.png', __FILE__) ?>" class="hugeit_lightbox_pro_logo"></h3>
			<div class="has-background">
				<label for="watermarket_image">Show Watermark Image
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Enable watermark on lightbox</p>
						</div>
					</div>
				</label>
				<input type="hidden" value="false"  />
				<input type="checkbox" id="watermarket_image" value="true" disabled="disabled" />
			</div>
			<div class="has-height">
				<label for="">Lightbox Watermark position
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set the position of lightbox watermark.</p>
						</div>
					</div>
				</label>
				<table class="bws_position_table">
					<tbody>
					<tr>
						<td><input type="radio" value="1" id="lightbox_watermark_position-left" disabled="disabled" /></td>
						<td><input type="radio" value="2" id="lightbox_watermark_position-center" disabled="disabled" /></td>
						<td><input type="radio" value="3" id="lightbox_watermark_position-right" checked="checked" disabled="disabled" /></td>
					</tr>
					<tr>
						<td><input type="radio" value="4" id="lightbox_watermark_position-left" disabled="disabled" /></td>
						<td style="visibility: hidden;"><input type="radio" value="4" id="lightbox_watermark_position-left" disabled="disabled" /></td>
						<td><input type="radio" value="6" id="lightbox_watermark_position-right" disabled="disabled" /></td>
					</tr>
					<tr>
						<td><input type="radio" value="7" id="lightbox_watermark_position-left" disabled="disabled" /></td>
						<td><input type="radio" value="8" id="lightbox_watermark_position-center" disabled="disabled" /></td>
						<td><input type="radio" value="9" id="lightbox_watermark_position-right" disabled="disabled" /></td>
					</tr>
					</tbody>
				</table>
			</div>

			<div class="has-background">
				<label for="watermark_width">Lightbox Watermark width
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set the widtht of Lightbox watermark.</p>
						</div>
					</div>
				</label>
				<input type="number"  id="watermark_width" value="30" class="text" disabled="disabled" />
				<span>px</span>
			</div>
			<div>
				<label for="watermark_transparency">Lightbox Watermark transparency
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set the transparency of Lightbox Watermark.</p>
						</div>
					</div>
				</label>
				<div class="slider-container">
					<input  id="watermark_transparency" data-slider-highlight="true"  data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true" value="100" disabled="disabled" />
					<span>100%</span>
				</div>
			</div>
			<div class="has-background" style="height:auto;">
				<label for="watermark_image_btn">Select Watermark Image
					<div class="help">?
						<div class="help-block">
							<span class="pnt"></span>
							<p>Set the image of Lightbox watermark.</p>
						</div>
					</div>
				</label>
				<img src="<?php echo $hugeit_lightbox_values['watermark_img_src']; ?>" id="watermark_image" style="width:120px;height:auto;">
				<input type="button" class="button wp-media-buttons-icon" style="margin-left: 63%;width: auto;display: inline-block;"  id="watermark_image_btn" value="Change Image" disabled="disabled" />
				<input type="hidden" id="img_watermark_hidden"  value="<?php echo $hugeit_lightbox_values['watermark_img_src']; ?>">
			</div>
	</form>
</div>
<div id="post-body-heading" class="post-body-line">
	<a onclick="document.getElementById('adminForm').submit()" class="save-lightbox-options button-primary">Save</a>
</div>

<script>
	/**********************Lightbox Watermark script*************************************/
	jQuery(document).ready(function() {
		var custom_uploader;
		var watermark_img_url = "";
		jQuery('#watermark_image_btn').click(function(e) {
			e.preventDefault();

			//If the uploader object has already been created, reopen the dialog
			if (custom_uploader) {
				custom_uploader.open();
				return;
			}

			//Extend the wp.media object
			custom_uploader = wp.media.frames.file_frame = wp.media({
				title: 'Choose file',
				button: {
					text: 'Choose file'
				},
				multiple: false
			});

			//When a file is selected, grab the URL and set it as the text field's value
			custom_uploader.on('select', function() {
				attachment = custom_uploader.state().get('selection').first().toJSON();
				jQuery("#watermark_image").attr("src", attachment.url);
				jQuery('#img_watermark_hidden').attr('value', attachment.url);
			});
			custom_uploader.open();
		});
	});
</script>