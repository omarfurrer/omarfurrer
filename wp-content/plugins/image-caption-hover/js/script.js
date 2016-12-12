jQuery(document).ready(function($) {
	jQuery('.image-flip-up, .image-flip-down, .rotate-image-down, .tilt-image, .image-flip-right, .image-flip-left').closest('.image-caption-box').css('overflow', 'visible');

	if(wcp_all_settings.touch == 'first_hover'){
	    jQuery('.wcp-caption-plugin a').on('touchstart', function (e) {
	        'use strict'; //satisfy code inspectors
	        var link = $(this); //preselect the link
	        if (link.hasClass('hover')) {
	            return true;
	        } else {
	            link.addClass('hover');
	            $('a.taphover').not(this).removeClass('hover');
	            e.preventDefault();
	            return false; //extra, and to make sure the function has consistent return points
	        }
	    });		
	}
});