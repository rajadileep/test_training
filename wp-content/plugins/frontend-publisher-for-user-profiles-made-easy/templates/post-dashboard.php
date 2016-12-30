<?php
    global $upfp_dashboard_data,$upfp;
    extract($upfp_dashboard_data);   
?>

<div id="upfp-posts-dashboard">
    <div id="upfp-posts-dashboard-header">
    
    </div>
    <div id="upfp-posts-dashboard-buttons">
        <div id="upfp-posts-new">
                <?php if($post_create_permission_status) { ?> 
                    <input type="button" title="" value="<?php _e('Add New Post','upfp'); ?>" id="upfp_init_create_post" name="upfp_init_create_post" class="   upme-button " />
                <?php } ?>
                <input type="button" title="" value="<?php _e('List Posts','upfp'); ?>" id="upfp_list_posts" name="upfp_list_posts" class="   upme-button " />
            
        </div>
    </div>
    
    <!--- PANEL 1 - List Post --->
    <div id="upfp-posts-list-panel" style="display:block;">
        
    </div>
    
    <!--- PANEL 2 - Create Post --->
    <?php if($post_create_permission_status) { ?> 
                
    <div id="upfp-posts-create-form-panel" style="display:none;">
        <form id="upfp-posts-create-form" action="" method="post" >
            <div class="upfp-posts-row ">
                <div id="upfp-new-posts-message"></div>
            </div>
            <div class="upfp-posts-row">
                <div class="upfp-posts-label"><?php _e('Post Title','upfp'); ?><span class="upfp-required">*</span></div>
                <div class="upfp-posts-field"><input class="upme-input" type="text" name="upfp_post_title" id="upfp_post_title" value="" /></div>
                <div class="upfp-clear"></div>
            </div>
            <div class="upfp-posts-row">
                <div class="upfp-posts-label"><?php _e('Post Content','upfp'); ?><span class="upfp-required">*</span></div>
                <div class="upfp-posts-field" style="width:100%;"><?php wp_editor('','upfp_post_content', array('editor_height' => '300')); ?></div>
                <div class="upfp-clear"></div>
            </div>
            <div class="upfp-posts-row">
                <div class="upfp-posts-label"><?php _e('Featured Image','upfp'); ?></div>
                <div class="upfp-posts-field"><input class="upme-input" type="file" name="upfp_post_featured" id="upfp_post_featured" value="" /></div>
                <div class="upfp-clear"></div>
            </div>
            <div class="upfp-posts-row">
                <div class="upfp-posts-label"><?php _e('Categories','upfp'); ?></div>
                <div class="upfp-posts-field">
                    <select multiple class="chosen-field-type upme-input" name="upfp_post_categories[]" id="upfp_post_categories" >
                        <?php foreach($categories as $category) { ?>
                            <option value="<?php echo $category->term_id; ?>" ><?php echo $category->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="upfp-clear"></div>
            </div>
            <div class="upfp-posts-row">
                <div class="upfp-posts-label"><?php _e('Tags','upfp'); ?></div>
                <div class="upfp-posts-field"><input class="upme-input" type="text" name="upfp_post_tags" id="upfp_post_tags" value="" /></div>
                <div class="upfp-clear"></div>
            </div>
            <?php wp_nonce_field( 'upfp_new_post_nonce', 'upfp_new_post_nonce_field' ); ?>
            <div class="upfp-posts-row">
                <div class="upfp-posts-label"></div>
                <div class="upfp-posts-field"><input class="upme-button" type="submit" name="upfp_post_submit" id="upfp_post_submit" value="<?php _e('Create','upfp'); ?>" /><img src="<?php echo UPFP_PLUGIN_URL; ?>img/loading.gif" style="display: none;" id="upfp_new_post_ajax" /></div>
                <div class="upfp-clear"></div>
            </div>
            
        </form>    
    </div>
    <?php } ?>
    
    <!--- PANEL 2 - Create Post --->
    <?php if($post_edit_permission_status) { ?> 
    <div id="upfp-posts-edit-form-panel" style="display:none;">
        <form id="upfp-posts-edit-form" action="" method="post" >
            <div class="upfp-posts-row ">
                <div id="upfp-edit-posts-message"></div>
            </div>
            <div class="upfp-posts-row">
                <div class="upfp-posts-label"><?php _e('Post Title','upfp'); ?><span class="upfp-required">*</span></div>
                <div class="upfp-posts-field"><input class="upme-input" type="text" name="upfp_edit_post_title" id="upfp_edit_post_title" value="" /></div>
                <div class="upfp-clear"></div>
            </div>
            <div class="upfp-posts-row">
                <div class="upfp-posts-label"><?php _e('Post Content','upfp'); ?><span class="upfp-required">*</span></div>
                <div class="upfp-posts-field" style="width:100%;"><?php wp_editor('','upfp_edit_post_content', array('editor_height' => '300')); ?></div>
                <div class="upfp-clear"></div>
            </div>
            <div class="upfp-posts-row">
                <div class="upfp-posts-label"><?php _e('Featured Image','upfp'); ?></div>
                <div class="upfp-posts-field">
                    <input class="upme-input" type="file" name="upfp_edit_post_featured" id="upfp_edit_post_featured" value="" />
                    <div id="upfp_edit_post_featured_image_panel" style="display:none">
                        <img id="upfp_edit_post_featured_image" src='' style="width:100px;height:100px;" />
                        <div id="upfp_edit_post_featured_image_delete"><?php _e('Delete','upfp'); ?></div>
                        
                    </div>
                    <div class="upfp_ajax_panel">
                        <img src="<?php echo UPFP_PLUGIN_URL; ?>/img/loading.gif" style="display: none;" id="upfp_edit_post_featured_ajax_delete">
                    </div>
                </div>
                <div class="upfp-clear"></div>
            </div>
            <div class="upfp-posts-row">
                <div class="upfp-posts-label"><?php _e('Categories','upfp'); ?></div>
                <div class="upfp-posts-field">
                    <select multiple class="chosen-field-type upme-input" name="upfp_edit_post_categories[]" id="upfp_edit_post_categories" >
                        <?php foreach($categories as $category) { ?>
                            <option value="<?php echo $category->term_id; ?>" ><?php echo $category->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="upfp-clear"></div>
            </div>
            <div class="upfp-posts-row">
                <div class="upfp-posts-label"><?php _e('Tags','upfp'); ?></div>
                <div class="upfp-posts-field"><input class="upme-input" type="text" name="upfp_edit_post_tags" id="upfp_edit_post_tags" value="" /></div>
                <div class="upfp-clear"></div>
            </div>
            <?php wp_nonce_field( 'upfp_new_post_nonce', 'upfp_new_post_nonce_field' ); ?>
            <div class="upfp-posts-row">
                <div class="upfp-posts-label"><input type="hidden" name="upfp_edit_post_id" id="upfp_edit_post_id" value="" /></div>
                <div class="upfp-posts-field"><input class="upme-button" type="submit" name="upfp_post_submit" id="upfp_post_submit" value="<?php _e('Update','upfp'); ?>" />
                    <img src="<?php echo UPFP_PLUGIN_URL; ?>/img/loading.gif" style="display: none;" id="upfp_edit_post_update_ajax">
                </div>
                <div class="upfp-clear"></div>
            </div>
            
        </form>    
    </div>
    <?php } ?> 
    
    <div id="upfp-posts-dashboard-list">
    
    </div>
</div>


