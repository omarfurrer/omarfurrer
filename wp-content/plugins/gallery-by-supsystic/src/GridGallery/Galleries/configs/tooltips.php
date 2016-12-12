<?php

return array(
    'tooltips' => array(
        // Area
        'grid-type' => 'There are 4 gallery types:</br>Fixed, Horizontal, Vertical, Fixed Columns</br><img src=@url/Grid.jpg />',
        'columns' => 'Number of columns with images on gallery page',
        'responsive-columns' => 'The number of columns for a given width of the screen. We specify the standard 1200px for medium-sized screens, 768px for the tablets, 320 for mobile. You can change this sizes if you want.',
        'distance' => '</br><img src=@url/distance_between_photos.jpg />',
        'area-height' => 'Height',
        'area-width' => '</br><img src=@url/gallery-width.jpg />',
        'photo-width' => '</br><img src=@url/width_bet_photos.jpg>',
        'photo-height' => '</br><img src=@url/height_bet_photos.jpg>',
        // Border
        'border-type' => '<p><img src=@url/solid_border.jpg><img src=@url/Dashed_border.jpg></p><p><img src=@url/dotted_border.jpg><img src=@url/double_border.jpg></p>',
        /*'border-color' => 'Select color',*/
        'border-width' => 'This option will work if selected Border type',
        'border-radius' => '</br><img src=@url/image-radius.jpg>',
        'display-first-photo' => 'When this option is enabled, only first picture from this gallery will be seen on the website. The other pictures will be seen in the popup window after clicking on the first picture.',
        'open-by-link' => 'If this option is enabled, then when one clicks on the link, which you can find below, the photos of gallery will be opened directly in popup. Note that the shortcode of this gallery should be added to the page, where you will use gallery link of this option.',
        // Shadow
        /*'shadow-color' => 'Select color',*/
        'shadow-blur' => 'Blur in percents',
        'shadow-x' => 'Offset by X',
        'shadow-y' => 'Offset by Y',
        'slideshow' => 'Start slideshow when open big image in popup',
	    'box-disableHistory' => 'If this option is checked - browser back button will close popup. If it is unchecked - images will be saved in browser history and will be opened on back or forward button click.',
        'mobile' => 'Check if you want to disable popups on mobile devices',
        'captions' => 'Check if you want to hide pagination and image caption on popup window',
        'overlay-personal' => "If option enabled you can choose personal caption effect per image in images list. If option disabled chosen effect will be used for all images",
        'tooltip' => 'If selected Yes tooltip on hovering image will not appear',
        'ismobile' => 'In order to show always captions on mobile devices - select Yes',
        // Uncomment to enable overlay tooltips
        /*'overlay-effect' => 'Overlay effect',
        'overlay-background' => 'Overlay background color',
        'overlay-foreground' => 'Overlay text color',
        'overlay-transparency' => 'Overlay transparency',*/

        //photoIcons
        'photo-icon' => "Select Show icons</br><img src=@url/icons.jpg />",
        //Categories
        'categories-show' => "Select Show categories</br><img src=@url/show_categories.jpg />",
        'animation-duration' => 'Transition/animation speed in milliseconds',
        'enable-shuffling-animation' => 'Animated sorting and laying out a group of images',
        //Pagination
        'pages-show' => "Enable pagination</br><img src=@url/enable_pagination.jpg />",
    ),
    'tooltips_icon' => array(
        'icon' => 'question'
    ),
);