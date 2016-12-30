<?php
    global $upfp_post_list_data,$upfp;
    extract($upfp_post_list_data); 

?>



<?php if(!$more_posts){ ?> 

<div id="upfp-delete-post-ajax-loader">
    <img src="<?php echo UPFP_PLUGIN_URL; ?>img/loading.gif" id="upfp_delete_post_ajax" />
</div>

<div id="upfp-post-list-msg" style="display:none;"></div>
<div class="upme-main upme-main-">
<?php } ?>
    
<?php

    if ($query->have_posts()) {
        while ($query->have_posts()) : $query->the_post();
?>
            <div class="upme-field  upme-custom-post-list-field">
                <div class="upme-post-field-type"><i class="upme-icon upme-icon-file-text"></i></div>
                <div class="upfp-post-field-value">
                    <span>
                    <a href="<?php echo get_permalink(); ?>" target="_blank"><?php echo get_the_title(); ?></a>
                    </span>
                    
                </div>
                <div class="upfp-post-field-buttons">
                    <?php if($post_edit_permission_status) { ?>
                    <a class="upfp-post-edit" href="" data-post-id="<?php echo get_the_ID(); ?>" ><i class="upme-icon upme-icon-pencil"></i></a>
                    <?php } ?>
                    
                    <?php if($post_delete_permission_status) { ?>
                    <a  data-post-id="<?php echo get_the_ID(); ?>" class="upfp-post-delete" href="javascript:void(0);"><i class="upme-icon upme-icon-remove"></i> </a>
                    <?php } ?>
                </div>
            </div>
<?php
        endwhile;
        wp_reset_query();
    }
?>
    <?php wp_nonce_field( 'upfp_delete_post_nonce', 'upfp_delete_post_nonce_field' ); ?>
<?php if(!$more_posts){ ?> 
    <a href="javascript:void(0);"><div id="upfp_more_posts" data-offset="0" class="upme-button" style="display:none"><?php _e('Load More Posts','upfp'); ?></div></a>
    

</div>
<?php } ?>
