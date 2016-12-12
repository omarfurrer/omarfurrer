<?php 
if (isset($wcpilight) && $wcpilight != '') {
    $caption_class = 'captionna';
}
else {
	$caption_class = 'captiontext';
}
if ($borderwidth != '') {
	$border_styling = 'border: '.$borderwidth.' solid '.$bordercolor.';';
} else {
	$border_styling = '';
}
?>
<?php if (isset($imagewidth) && $imagewidth != '') { ?>
	<div class="wcp-caption-plugin" ontouchstart="" data-height="<?php echo $imageheight; ?>" id="wcp-widget-<?php echo $atts['id']; ?>" style="margin: 0 auto;width: <?php echo $imagewidth; ?>; height: <?php echo $imageheight; ?>; <?php echo $border_styling; ?>">
			<?php if (isset($captionlink) && $captionlink != '') { ?>
				<a href="<?php echo $captionlink; ?>" target="<?php echo $captiontarget; ?>">
			<?php } ?>
				<div class="image-caption-box" style="margin: 0 auto;width: <?php echo $imagewidth; ?>; height: <?php echo $imageheight; ?>;"> 
		        	<div
		        		class="caption <?php echo $hoverstyle; ?> <?php echo $caption_class; ?>"
		        		style="background-color: <?php echo $captionbg; ?>;
		        		    color: <?php echo $captioncolor; ?>;
		        		    opacity: <?php echo $captionopacity; ?>;
		        		    width: <?php echo $imagewidth; ?>;
		        		    height: <?php echo $imageheight; ?>;"
		        	>
		        		<div style="display:table;height:100%;width: 100%;">
				    		<div class="centered-text" style="text-align: <?php echo $captionalignment; ?>; padding: 5px;">
				    			<?php echo stripslashes($captiontext); ?>
				    		</div>
				    	</div>
		        	</div>

		        <?php if (isset($wcpilight) && $wcpilight != '') {
		        	echo do_shortcode( $wcpilight.'<img class="wcp-caption-image" src="'.$imageurl.'" title="'.$imagetitle.'" alt="'.$imagealt.'"/>'.'[/ilightbox]' );
		        } 
		        else { ?>
		        	<img style="width: <?php echo $imagewidth; ?>; height: <?php echo $imageheight; ?>;" class="wcp-caption-image" src="<?php echo $imageurl; ?>" title="<?php echo $imagetitle; ?>" alt="<?php echo $imagealt; ?>"/>
		        <?php } ?>
				</div>

		    <?php if (isset($captionlink) && $captionlink != '') { ?>
				</a>
			<?php } ?>
	</div>
<?php } else { ?>
<div class="wcp-caption-plugin" ontouchstart="" data-height="responsive" id="wcp-widget-<?php echo $atts['id']; ?>" style="<?php echo $border_styling; ?>">
	<div class="wcp-loading" style="background-image: url(<?php echo plugin_dir_url( __FILE__ ); ?>images/ajax-loader.gif)"></div>
		<?php if (isset($captionlink) && $captionlink != '') { ?>
			<a href="<?php echo $captionlink; ?>" target="<?php echo $captiontarget; ?>">
		<?php } ?>
			<div class="image-caption-box"> 
	        	<div
	        		class="caption <?php echo $hoverstyle; ?> <?php echo $caption_class; ?>"
	        		style="background-color: <?php echo $captionbg; ?>;
	        		    color: <?php echo $captioncolor; ?>;
	        		    opacity: <?php echo $captionopacity; ?>;"
	        	>

	        		<div style="display:table;height:100%;width: 100%;">
			    		<div class="centered-text" style="text-align: <?php echo $captionalignment; ?>; padding: 5px;">
			    			<?php echo stripslashes($captiontext); ?>
			    		</div>
		    		</div>
	        	</div>

		        <?php if (isset($wcpilight) && $wcpilight != '') {
		        	echo do_shortcode( $wcpilight.'<img class="wcp-caption-image img-make-responsive" src="'.$imageurl.'" title="'.$imagetitle.'" alt="'.$imagealt.'"/>'.'[/ilightbox]' );
		        } 
		        else { ?>
		        	<img class="wcp-caption-image img-make-responsive" src="<?php echo $imageurl; ?>" title="<?php echo $imagetitle; ?>" alt="<?php echo $imagealt; ?>"/>
		        <?php } ?>
			</div>

	    <?php if (isset($captionlink) && $captionlink != '') { ?>
			</a>
		<?php } ?>
</div>

<?php } ?>