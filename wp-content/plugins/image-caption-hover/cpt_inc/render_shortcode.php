<div class="ich-prefix">
<?php
    $fgg_images = get_post_meta( $atts['id'], 'ich_cpt', true );
    $fgg_settings = get_post_meta( $atts['id'], 'ichcpt_settings', true );
    // var_dump($fgg_images);

    $css_class = 'col-1-'.$fgg_settings['columns'];
    
    $padding = (isset($fgg_settings['col_space'])) ? $fgg_settings['col_space'] : '20px' ; ?>
    <div class="grid grid-pad wcp-fgg-wrap" style="padding-top: <?php echo $padding; ?>;
    padding-left: <?php echo $padding; ?>;
    padding-right: 0px;">

        <?php if (isset($fgg_settings['custom_css']) && $fgg_settings['custom_css'] != '') { ?>
            <style>
                <?php echo stripcslashes($fgg_settings['custom_css']); ?>
            </style>
        <?php } ?>


        <?php foreach ($fgg_images as $key => $data) { ?>
            <?php
                extract($data);
                $border_styling = '';  
                $trspeed = '';
                $caption_style_cus = '';
                $image_style_cus = '';
                ?>
            <div class="<?php echo $css_class; ?>" style="padding-right: <?php echo $padding; ?>; padding-bottom: <?php echo $padding; ?>;">
                <?php if (strpos($hovereffect, 'square') !== false || strpos($hovereffect, 'circle') !== false) { ?>
                    <div class="ih-item <?php echo $hovereffect; ?>">
                        <div class="wcp-ih-inner">
                        <?php if (isset($captionlink) && $captionlink != '') {
                            $popup =  (isset($data['lightbox'])) ? 'rel="prettyPhoto"' : '' ;
                            echo   '<a '.$popup.' href="'.$captionlink.'" target="'.$captiontarget.'">';
                        }
                            $infoBack = $this->has_info_class($hovereffect);
                        ?>
                            <div class="img">
                                <img src="<?php echo $imageurl; ?>" title="<?php echo $imagetitle; ?>" alt="<?php echo $imagealt; ?>" />                
                            </div>
                            <div class="info" <?php echo (!$infoBack) ? 'style="background-color: '.$captionbg.';color:'.$captioncolor.';"' : '' ; ?>>
                                <div <?php echo ($infoBack) ? 'style="background-color: '.$captionbg.';color:'.$captioncolor.';" class="info-back"' : '' ; ?>>
                                    <h3><?php echo (isset($imagetitle) && $imagetitle != '') ? $imagetitle : 'TITLE HERE' ; ?></h3>
                                    <p style="text-align: <?php echo $captionalignment; ?>;">
                                        <?php
                                            if (isset($fgg_settings['caption_shortcodes'])) {
                                                echo apply_filters('the_content', $captiontext);
                                            } else {
                                                echo $captiontext;
                                            }
                                         ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($captionlink) && $captionlink != '') {
                            echo   '</a>';
                        } ?>
                    </div>
                    
                <?php } else { ?>            
                <div class="wcp-caption-plugin" <?php echo $caption_style_cus; ?> ontouchstart="" id="wcp-widget-<?php echo $atts['id']; ?>" style="<?php echo $border_styling; ?>">
                    <?php if (isset($captionlink) && $captionlink != '') {
                        $popup =  (isset($data['lightbox'])) ? 'rel="prettyPhoto"' : '' ;
                        echo   '<a '.$popup.' href="'.$captionlink.'" target="'.$captiontarget.'">';
                    } ?>
                        <div class="image-caption-box">
                            <div class="caption <?php echo $hovereffect; ?>"
                                style="background-color: <?php echo $captionbg; ?>;
                                    color: <?php echo $captioncolor; ?>;
                                    transition-duration: <?php // echo $animationspeed; ?>;
                                    -webkit-transition-duration: <?php // echo $animationspeed; ?>;">
                                <div style="display:table;height:100%;width: 100%;">
                                    <<?php echo $captionwrap; ?> class="centered-text" style="text-align: <?php echo $captionalignment; ?>; padding: 5px;">
                                        <?php
                                            if (isset($fgg_settings['caption_shortcodes'])) {
                                                echo apply_filters('the_content', $captiontext);
                                            } else {
                                                echo $captiontext;
                                            }
                                         ?>
                                    </<?php echo $captionwrap; ?>>
                                </div>
                            </div>
                            <img class="wcp-caption-image" src="<?php echo $imageurl; ?>" title="<?php echo $imagetitle; ?>" alt="<?php echo $imagealt; ?>" style="<?php echo $trspeed; echo $image_style_cus; ?>"/>
                        </div>
                
                    <?php if (isset($captionlink) && $captionlink != '') {
                        echo   '</a>';
                    } ?>
                </div>
                <?php } ?>
            </div>
            
        <?php } ?>
    </div>
    
    <div class="clearfix"></div>
    
</div>