<h3 class="tab-head"><?php echo ($data['refname'] != '') ? $data['refname'] : 'Image Caption Hover' ; ?></h3>
<div class="tab-content">
    <h3><?php _e( 'Image', 'image-caption-hover' ); ?></h3>
    <table class="widefat">
        <tr>
            <td><?php _e( 'Paste URL or use from Media', 'image-caption-hover' ); ?>
            <td>
                <input type="text" class="imageurl" value="<?php echo $data['imageurl']; ?>">
                <button class="button-secondary upload_image_button"
                    data-title="<?php _e( 'Select Image', 'image-caption-hover' ); ?>"
                    data-btntext="<?php _e( 'Select', 'image-caption-hover' ); ?>"><?php _e( 'Media', 'image-caption-hover' ); ?></button>
            </td>
            <td colspan="2">
                <p class="description"><?php _e( 'Use media to upload image', 'image-caption-hover' ); ?>.</p>
            </td>
        </tr>
        <tr>
            <td><?php _e( 'Title (img tag title attribute)', 'image-caption-hover' ); ?></td>
            <td>
                <input type="text" class="imagetitle widefat" value="<?php echo $data['imagetitle']; ?>">
            </td>
            <td><?php _e( 'Alternate Text (img tag alt attribute)', 'image-caption-hover' ); ?></td>
            <td>
                <input type="text" class="imagealt widefat" value="<?php echo $data['imagealt']; ?>">
            </td>
        </tr>
        <tr>
            <td><?php _e( 'iLightBox Shortcode', 'image-caption-hover' ); ?></td>
            <td>
                <input type="text" class="wcpilight widefat" value="<?php echo (isset($data['wcpilight'])) ? stripslashes($data['wcpilight']) : '' ; ?>">
            </td>
            <td colspan="2">
                <p class="description"><?php _e( 'Eg: [ilightbox id="7"], Leave blank if not sure', 'image-caption-hover' ); ?>.</p>
            </td>
        </tr>
        <tr>
            <td><?php _e( 'Width eg: 100px or 50%', 'image-caption-hover' ); ?><br>
                <?php _e( 'Leaving blank will take container\'s width', 'image-caption-hover' ); ?>
            </td>
            <td><input type="text" class="widefat imagewidth" value="<?php echo (isset($data['imagewidth'])) ? $data['imagewidth'] : '' ; ?>"></td>
            <td><?php _e( 'Height eg: 100px or 50%', 'image-caption-hover' ); ?></td>
            <td><input type="text" class="widefat imageheight" value="<?php echo (isset($data['imageheight'])) ? $data['imageheight'] : '' ; ?>"></td>
        </tr>
    </table>
    <h3><?php _e( 'Caption', 'image-caption-hover' ); ?></h3>
    <table class="widefat">
        <tr>
            <td><?php _e( 'Caption Text (HTML tags can be used)', 'image-caption-hover' ); ?></td>
            <td><textarea class="captiontext widefat"><?php echo stripslashes($data['captiontext']); ?></textarea></td>
            <td><?php _e( 'Caption Alignment', 'image-caption-hover' ); ?></td>
            <td>
                <select class="captionalignment widefat">
                    <option value="auto" <?php selected( $data['captionalignment'], 'auto' ); ?>><?php _e( 'Auto', 'image-caption-hover' ); ?></option>
                    <option value="center" <?php selected( $data['captionalignment'], 'center' ); ?>><?php _e( 'Center', 'image-caption-hover' ); ?></option>
                    <option value="right" <?php selected( $data['captionalignment'], 'right' ); ?>><?php _e( 'Right', 'image-caption-hover' ); ?></option>
                    <option value="left" <?php selected( $data['captionalignment'], 'left' ); ?>><?php _e( 'Left', 'image-caption-hover' ); ?></option>
                    <option value="justify" <?php selected( $data['captionalignment'], 'justify' ); ?>><?php _e( 'Justify', 'image-caption-hover' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php _e( 'Caption Background Color', 'image-caption-hover' ); ?></td>
            <td class="insert-picker-bg"><input class="captionbg colorpicker" data-alpha="true" type="text" value="<?php echo $data['captionbg']; ?>"></td>
            <td><?php _e( 'Caption Text Color', 'image-caption-hover' ); ?></td>
            <td class="insert-picker-color"><input class="captioncolor colorpicker" data-alpha="true" type="text" value="<?php echo $data['captioncolor']; ?>"></td>
        </tr>
        <tr>
            <td><?php _e( 'Background Opacity', 'image-caption-hover' ); ?></td>
            <td><input class="captionopacity widefat" type="number" min="0" max="1" step="0.1" value="<?php echo $data['captionopacity']; ?>"></td>
            <td><?php _e( 'Title (for your reference)', 'image-caption-hover' ); ?></td>
            <td><input class="refname widefat" type="text" value="<?php echo $data['refname']; ?>"></td>
        </tr>
        <tr>
            <td><?php _e( 'Link (paste URL here or leave blank)', 'image-caption-hover' ); ?></td>
            <td><input class="captionlink widefat" type="text" value="<?php echo $data['captionlink']; ?>"></td>
            <td><?php _e( 'Link Target (write _blank for opening link in new window)', 'image-caption-hover' ); ?></td>
            <td><input class="captiontarget widefat" type="text" value="<?php echo $data['captiontarget']; ?>"></td>
        </tr>
    </table>
    <h3><?php _e( 'Border', 'image-caption-hover' ); ?></h3>
    <table class="widefat">
        <tr>
            <td><?php _e( 'Border Width', 'image-caption-hover' ); ?></td>
            <td>
                <input type="text" class="borderwidth widefat" value="<?php echo (isset($data['borderwidth'])) ? $data['borderwidth'] : '' ; ?>">
            </td>
            <td>
                <p class="description"><?php _e( 'Width of border, eg: 15px. Leaving blank will disable border.', 'image-caption-hover' ); ?></p>
            </td>
        </tr>
        <tr>
            <td><?php _e( 'Border Style', 'image-caption-hover' ); ?> <b><?php _e( '(Pro Feature)', 'image-caption-hover' ); ?></b></td>
            <td>
                <select class="bordertype widefat">
                    <option value="dotted" <?php echo (isset($data['bordertype']) && $data['bordertype'] == 'dotted') ? 'selected' : '' ; ?> disabled><?php _e( 'Dotted', 'image-caption-hover' ); ?></option>
                    <option value="dashed" <?php echo (isset($data['bordertype']) && $data['bordertype'] == 'dashed') ? 'selected' : '' ; ?> disabled><?php _e( 'Dashed', 'image-caption-hover' ); ?></option>
                    <option value="solid" <?php echo (isset($data['bordertype']) && $data['bordertype'] == 'solid') ? 'selected' : '' ; ?>><?php _e( 'Solid', 'image-caption-hover' ); ?></option>
                    <option value="double" <?php echo (isset($data['bordertype']) && $data['bordertype'] == 'double') ? 'selected' : '' ; ?> disabled><?php _e( 'Double', 'image-caption-hover' ); ?></option>
                    <option value="groove" <?php echo (isset($data['bordertype']) && $data['bordertype'] == 'groove') ? 'selected' : '' ; ?> disabled><?php _e( 'Groove', 'image-caption-hover' ); ?></option>
                    <option value="ridge" <?php echo (isset($data['bordertype']) && $data['bordertype'] == 'ridge') ? 'selected' : '' ; ?> disabled><?php _e( 'Ridge', 'image-caption-hover' ); ?></option>
                    <option value="inset" <?php echo (isset($data['bordertype']) && $data['bordertype'] == 'inset') ? 'selected' : '' ; ?> disabled><?php _e( 'Inset', 'image-caption-hover' ); ?></option>
                    <option value="outset" <?php echo (isset($data['bordertype']) && $data['bordertype'] == 'outset') ? 'selected' : '' ; ?> disabled><?php _e( 'Outset', 'image-caption-hover' ); ?></option>
                </select>
            </td>
            <td>
                <p class="description"><?php _e( 'Some styles may depend on border color.', 'image-caption-hover' ); ?></p>
            </td>
        </tr>
        <tr>
            <td><?php _e( 'Border Color', 'image-caption-hover' ); ?></td>
            <td>
                <input type="text" class="bordercolor widefat" value="<?php echo (isset($data['bordercolor'])) ? $data['bordercolor'] : '' ; ?>">
            </td>
            <td>
                <p class="description"><?php _e( 'Name of color or color code.', 'image-caption-hover' ); ?></p>
            </td>
        </tr>
        <tr>
            <td><?php _e( 'Border Radius', 'image-caption-hover' ); ?> <b><?php _e( '(Pro Feature)', 'image-caption-hover' ); ?></b></td>
            <td>
                <input disabled type="text" class="borderradius widefat" value="<?php echo (isset($data['borderradius'])) ? $data['borderradius'] : '' ; ?>">
            </td>
            <td>
                <p class="description"><?php _e( 'Radius of border eg: 5px or 50%.', 'image-caption-hover' ); ?></p>
            </td>
        </tr>
        <tr>
            <td><?php _e( 'Shadow', 'image-caption-hover' ); ?> <b><?php _e( '(Pro Feature)', 'image-caption-hover' ); ?></b></td>
            <td>
                <input disabled type="text" class="boxshadow widefat" value="<?php echo (isset($data['boxshadow'])) ? $data['boxshadow'] : '' ; ?>">
            </td>
            <td>
                <p class="description"><?php _e( 'Box Shadow for border. (h-shadow v-shadow blur spread color)', 'image-caption-hover' ); ?></p>
            </td>
        </tr>
    </table>
    <h3><?php _e( 'Hover', 'image-caption-hover' ); ?></h3>
    <table class="widefat">
        <tr>
            <td><?php _e( 'Hover Style', 'image-caption-hover' ); ?></td>
            <td>
                <select class="hoverstyle">
                    <?php foreach ($wcp_classes as $className) { ?>
                    <option value="<?php echo $className; ?>" <?php if($data['hoverstyle'] == $className){echo 'selected';} ?>><?php echo ucwords(str_replace("-"," ",$className)) ?></option>
                    <?php } ?>
                </select>
            </td>
            <td>
                <?php _e( 'Animation Speed (eg: 500ms or 1s)', 'image-caption-hover' ); ?>
                <b>(<?php _e( 'Pro Feature', 'image-caption-hover' ); ?>)</b>
            </td>
            <td>
                <input type="text" value="1s" class="widefat" disabled>
            </td>            
        </tr>
    </table>
    <h3><?php _e( 'Preview', 'image-caption-hover' ); ?></h3>
    <p class="text-center"><button class="button-secondary update-preview"><?php _e( 'Refresh Preview', 'image-caption-hover' ); ?></button></p>
    <div class="insert-preview" style="max-width: 300px; width: 100%; margin: 0 auto;">						
    </div>
    <div class="clearfix"></div>
    <hr style="margin-bottom: 10px;">
    <button class="button btndelete"><span class="dashicons dashicons-dismiss" title="Delete"></span><?php _e( 'Delete', 'image-caption-hover' ); ?></button>
    <button class="button btnadd"><span class="dashicons dashicons-admin-page"></span><?php _e( 'Duplicate', 'image-caption-hover' ); ?></button>&nbsp;
    <button class="button btnnew"><span class="dashicons dashicons-plus-alt"></span><?php _e( 'Add New', 'image-caption-hover' ); ?></button>&nbsp;
    <p class="wcp-shortc"><button class="button-primary fullshortcode" id="<?php echo $data['counter']; ?>"><?php _e( 'Get Shortcode', 'image-caption-hover' ); ?></button></p>
    <div class="clearfix"></div>
    <br>
    <br>
</div>