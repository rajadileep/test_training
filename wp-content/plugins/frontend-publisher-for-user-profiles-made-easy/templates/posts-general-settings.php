<?php 
    global $upfp_settings_data,$upme_roles; 
    extract($upfp_settings_data);

    $post_statuses = array( 'pending'   => __('Pending Review','upfp'), 
                            'draft'     => __('Draft','upfp'), 
                            'publish'   => __('Published','upfp'), 
                            'private'   => __('Private','upfp'), 
                          );

    $user_roles_array = $upme_roles->upme_available_user_roles_view_profile();

?>

<form method="post" action="">
<table class="form-table">
    
    <tr>
        <th><label for=""><?php _e('Default Post Status for New Posts','upas'); ?></label></th>
        <td style="width:500px;">
            <select id="upfp_default_new_post_status" name="upfp_posts_general[default_new_post_status]" class="chosen-admin_setting" >
                <?php foreach($post_statuses as $post_status_key => $post_status) { ?>
                    <option value="<?php echo $post_status_key; ?>" <?php selected($post_status_key,$default_new_post_status); ?> ><?php echo $post_status; ?></option>
                <?php } ?>
            
            </select>
        </td>
    </tr>
    
    <tr>
        <th><label for=""><?php _e('Default Post Status for Edit Posts','upas'); ?></label></th>
        <td style="width:500px;">
            <select id="upfp_default_edit_post_status" name="upfp_posts_general[default_edit_post_status]" class="chosen-admin_setting" >
                <?php foreach($post_statuses as $post_status_key => $post_status) { ?>
                    <option value="<?php echo $post_status_key; ?>" <?php selected($post_status_key,$default_edit_post_status); ?> ><?php echo $post_status; ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
    
    <tr>
        <th><label for=""><?php _e('Number of Posts Per Page','upas'); ?></label></th>
        <td style="width:500px;">
            <input type="text" id="upfp_posts_per_page" name="upfp_posts_general[posts_per_page]" value="<?php echo $posts_per_page; ?>" />
        </td>
    </tr>
    
    
    <tr>
        <th><label for=""><?php _e('Enable New Posts for','upfp'); ?></label></th>
        <td style="width:500px;">
            <select id="upfp_new_post_allowed_type" name="upfp_posts_general[new_post_allowed_type]" class="chosen-admin_setting" >
                <option value="0" <?php selected('0',$new_post_allowed_type); ?> ><?php _e('Disabled for All LoggedIn Users','upfp'); ?></option>
                <option value="1" <?php selected('1',$new_post_allowed_type); ?> ><?php _e('Enabled for All LoggedIn Users','upfp'); ?></option>
                <option value="2" <?php selected('2',$new_post_allowed_type); ?> ><?php _e('Enabled for User Roles','upfp'); ?></option>
            </select>
        </td>
    </tr>

    <?php 
        $display_new_post_allowed_roles = 'display:none';
        if($new_post_allowed_type == '2'){
            $display_new_post_allowed_roles = '';
        }
    ?>
    <tr style="<?php echo $display_new_post_allowed_roles; ?>">
        <th><label for=""><?php _e('User Roles Allowed to Create Posts','upfp'); ?></label></th>
        <td style="width:500px;">
            <select id="upfp_new_post_allowed_roles" name="upfp_posts_general[new_post_allowed_roles][]" class="chosen-admin_setting" multiple >
                
                <?php 
                    
                    foreach($user_roles_array as $role_key => $role){ 
                        $selected = ( in_array($role_key,$new_post_allowed_roles) ) ? ' selected=selected ' : '';
                ?>
                    <option value="<?php echo $role_key; ?>" <?php echo $selected; ?> ><?php echo $role; ?></option>
                
                <?php } ?>
            </select>
        </td>
    </tr>
    
    
    <tr >
        <th><label for=""><?php _e('Enable Edit Posts for','upfp'); ?></label></th>
        <td style="width:500px;">
            <select id="upfp_edit_post_allowed_type" name="upfp_posts_general[edit_post_allowed_type]" class="chosen-admin_setting" >
                <option value="0" <?php selected('0',$edit_post_allowed_type); ?> ><?php _e('Disabled for All LoggedIn Users','upfp'); ?></option>
                <option value="1" <?php selected('1',$edit_post_allowed_type); ?> ><?php _e('Enabled for All LoggedIn Users','upfp'); ?></option>
                <option value="2" <?php selected('2',$edit_post_allowed_type); ?> ><?php _e('Enabled for User Roles','upfp'); ?></option>
            </select>
        </td>
    </tr>
    
    <?php 
        $display_edit_post_allowed_roles = 'display:none';
        if($edit_post_allowed_type == '2'){
            $display_edit_post_allowed_roles = '';
        }
    ?>
    <tr style="<?php echo $display_edit_post_allowed_roles; ?>">
        <th><label for=""><?php _e('User Roles Allowed to Edit Posts','upfp'); ?></label></th>
        <td style="width:500px;">
            <select id="upfp_edit_post_allowed_roles" name="upfp_posts_general[edit_post_allowed_roles][]" class="chosen-admin_setting" multiple >
                
                <?php 
                    
                    foreach($user_roles_array as $role_key => $role){ 
                        $selected = ( in_array($role_key,$edit_post_allowed_roles) ) ? ' selected=selected ' : '';
                ?>
                    <option value="<?php echo $role_key; ?>" <?php echo $selected; ?> ><?php echo $role; ?></option>
                
                <?php } ?>
            </select>
        </td>
    </tr>
    
    <tr >
        <th><label for=""><?php _e('Enable Delete Posts for','upfp'); ?></label></th>
        <td style="width:500px;">
            <select id="upfp_delete_post_allowed_type" name="upfp_posts_general[delete_post_allowed_type]" class="chosen-admin_setting" >
                <option value="0" <?php selected('0',$delete_post_allowed_type); ?> ><?php _e('Disabled for All LoggedIn Users','upfp'); ?></option>
                <option value="1" <?php selected('1',$delete_post_allowed_type); ?> ><?php _e('Enabled for All LoggedIn Users','upfp'); ?></option>
                <option value="2" <?php selected('2',$delete_post_allowed_type); ?> ><?php _e('Enabled for User Roles','upfp'); ?></option>
            </select>
        </td>
    </tr>
    
    <?php 
        $display_delete_post_allowed_roles = 'display:none';
        if($delete_post_allowed_type == '2'){
            $display_delete_post_allowed_roles = '';
        }
    ?>
    <tr style="<?php echo $display_delete_post_allowed_roles; ?>">
        <th><label for=""><?php _e('User Roles Allowed to Delete Posts','upfp'); ?></label></th>
        <td style="width:500px;">
            <select id="upfp_delete_post_allowed_roles" name="upfp_posts_general[delete_post_allowed_roles][]" class="chosen-admin_setting" multiple >
                
                <?php 
                    
                    foreach($user_roles_array as $role_key => $role){ 
                        $selected = ( in_array($role_key,$delete_post_allowed_roles) ) ? ' selected=selected ' : '';
                ?>
                    <option value="<?php echo $role_key; ?>" <?php echo $selected; ?> ><?php echo $role; ?></option>
                
                <?php } ?>
            </select>
        </td>
    </tr>
    
    <input type="hidden" name="upfp_tab" value="<?php echo $tab; ?>" />

    
</table>

    <?php submit_button(); ?>
</form>