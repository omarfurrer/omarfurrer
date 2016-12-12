<div class="ich-settings-main-wrap">
<div class="caption-settings-wrap">
<?php global $post;
?>
    <div id="wcpinner" class="wcp-main-wrap panel-group">
    <?php

    //get the saved meta as an arry
    $wcp_settings = get_post_meta($post->ID,'ich_cpt',true);

    $column = 1;
    if ( count( $wcp_settings ) > 0 && is_array($wcp_settings)) {
        foreach( $wcp_settings as $key => $options ) {
            include 'settings.php';
            $column = $column + 1;
        }
    } else {
        $key = 1;
        $options = array();
        include 'settings.php';
    }
    ?>
</div>
<br>
<button class="add button button-primary"><?php _e('Add New Image', 'image-caption-hover'); ?></button>
</div>

<div class="ich-rich-editor-wrap">
    <div id="ich-rich-editor"></div>
    
    <button class="btn btn-info ich-editor-insert"><?php _e( 'Insert', 'image-caption-hover' ); ?></button>
    <button class="btn btn-danger ich-editor-cancel"><?php _e( 'Cancel', 'image-caption-hover' ); ?></button>
</div>

</div>
