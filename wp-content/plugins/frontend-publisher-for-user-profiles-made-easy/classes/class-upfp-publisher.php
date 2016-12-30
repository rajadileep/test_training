<?php
class UPFP_Publisher{
    
    public function __construct(){
        
        add_filter('upme_profile_tab_items', array($this,'profile_tab_items'),10,2);
        add_filter('upme_profile_view_forms',array($this,'profile_view_forms'),10,2);
      
        add_action('wp_ajax_upfp_get_posts_list', array($this,'upfp_get_posts_list'));
        add_action('wp_ajax_nopriv_upfp_get_posts_list', array($this,'upfp_get_posts_list'));
        
        add_action('wp_ajax_upfp_get_post_edit_data', array($this,'upfp_get_post_edit_data'));        
        
        add_action('wp_ajax_upfp_create_new_post', array($this, 'upfp_create_new_post'));
        
        add_action('wp_ajax_upfp_edit_post', array($this, 'upfp_edit_post'));
        
        add_action('wp_ajax_upfp_delete_post_featured', array($this, 'upfp_delete_post_featured'));
        
        add_action('wp_ajax_upfp_delete_post', array($this, 'upfp_delete_post'));

    }
    
    public function profile_tab_items($display,$params){
        extract($params);

        $userid = get_current_user_id();
        if($userid != '0' && $userid == $id && $view == ''){
            $display .= '<div title="'.__('Posts','upfp').'" class="upme-profile-tab" data-tab-id="upme-posts-panel" >
                                <i class="upme-profile-icon upme-icon-file-text"></i>
                                <div class="upme-profile-tab-title">'.apply_filters('upme_profile_tab_items_frontend_publisher_title', __('Posts','upfp'),$params).'</div>
                            </div>';
        }

        return $display;
    }

    
    public function profile_view_forms($display,$params){
        global $upfp,$upfp_dashboard_data;
        extract($params);
        
        $args = array(
          'orderby' => 'name',
          'order' => 'ASC'
          );
        $categories = get_categories($args);
        
        $upfp_dashboard_data = array();
        $upfp_dashboard_data['categories'] = $categories;
        
        $upfp_dashboard_data['post_create_permission_status'] = $this->upfp_verify_permission_status('new_post_allowed_type','new_post_allowed_roles');
        $upfp_dashboard_data['post_edit_permission_status'] = $this->upfp_verify_permission_status('edit_post_allowed_type','edit_post_allowed_roles');
        $upfp_dashboard_data['post_delete_permission_status'] = $this->upfp_verify_permission_status('delete_post_allowed_type','delete_post_allowed_roles');
        
                          
       
        $userid = get_current_user_id();
        if($userid != '0' && $userid == $id && $view != 'compact' && $hide_profile_tabs != 'yes' ){
            ob_start();
            $upfp->template_loader->get_template_part('post-dashboard');        
            $display_post = apply_filters('upme_publisher_post_dashboard_template',ob_get_clean()); 
            
            $display .= '<div class="upme-posts-panel upme-profile-tab-panel" style="display:none"  >
                                       '.$display_post.'
                                    </div>';
        }

        return $display;
    }
    
    public function upfp_posts_settings($section){
        global $upfp;
        
        $this->upfp_options = $upfp->upfp_options;
        $posts_settings = isset($this->upfp_options[$section]) ? $this->upfp_options[$section] :  array();
        return $posts_settings;        
    }
    
    public function upfp_get_posts_list(){
        global $upfp,$upfp_post_list_data;
        
        $posts_settings = $this->upfp_posts_settings('posts_general');         
        
        $upfp_post_list_data = array();
        
        $upfp_post_list_data['post_edit_permission_status'] = $this->upfp_verify_permission_status('edit_post_allowed_type','edit_post_allowed_roles');
        $upfp_post_list_data['post_delete_permission_status'] = $this->upfp_verify_permission_status('delete_post_allowed_type','delete_post_allowed_roles');
        
        $userid = get_current_user_id();
        $offset = isset($_POST['upfp_post_offset']) ? $_POST['upfp_post_offset'] : 0;
        $more_posts = isset($_POST['upfp_more_posts']) ? $_POST['upfp_more_posts'] : 0;
        
        $upfp_post_list_data['more_posts'] = $more_posts;
        
        $per_page = isset($posts_settings['posts_per_page']) ? (int) $posts_settings['posts_per_page'] : '25';
        
        $args = array(
            'author' => $userid,
            'posts_per_page' => '-1',
            'post_status' => array( 'pending', 'draft', 'future', 'publish', 'private' ),
            'post_type'  => 'post',
            'orderby' => 'date',
            'order'   => 'DESC',
        );
        
        $count_query = new WP_Query( $args );
        $total_post_count = $count_query->post_count;
        
        $args = array(
            'author' => $userid,
            'posts_per_page' => $per_page,
            'offset' => $offset,
            'post_status' => array( 'pending', 'draft', 'future', 'publish', 'private' ),
            'post_type'  => 'post',
            'orderby' => 'date',
            'order'   => 'DESC',
        );
        
        $query = new WP_Query( $args );
        $post_count = $query->post_count;
        
        $upfp_post_list_data['query'] = $query;    
        
        ob_start();
        $upfp->template_loader->get_template_part('post-list');        
        $display = ob_get_clean();
    
        $offset = $offset + $per_page;
        $load_more = 0;
        if($total_post_count > $post_count){
            $load_more = 1;
        }
         
        echo json_encode(array('status' => 'success', 'msg' => $display, 'offset' => $offset, 'load_more' => $load_more));exit;
    }
    
    public function upfp_create_new_post(){
        
        $posts_settings = $this->upfp_posts_settings('posts_general');
        $post_status_default = isset($posts_settings['default_new_post_status']) ? $posts_settings['default_new_post_status'] : 'pending';

        if ( isset( $_POST ) && isset( $_POST['upfp_new_post_nonce_field'] ) && wp_verify_nonce( $_POST['upfp_new_post_nonce_field'], 'upfp_new_post_nonce' ) ) {
            
            $post_title = isset($_POST['upfp_post_title']) ? $_POST['upfp_post_title'] : '' ;
            $post_content = isset($_POST['upfp_post_content']) ? $_POST['upfp_post_content'] : '' ;
            $post_category = isset($_POST['upfp_post_categories']) ? $_POST['upfp_post_categories'] : array() ;
            $post_tags = isset($_POST['upfp_post_tags']) ? $_POST['upfp_post_tags'] : '' ;
            
            $error = 0;
            $error_msg = '';
            
            if($post_title == ''){
                $error++;
                $error_msg .= '<p>'.__('Post Title is required.','upfp').'</p>';
            }
            if(trim($post_content) == ''){
                $error++;
                $error_msg .= '<p>'.__('Post Content is required.','upfp').'</p>';
            }
            
            $allowed = array("image/gif", "image/jpeg", "image/png");
            if (isset($_FILES['upfp_post_featured']) && $_FILES['upfp_post_featured']['error'] == 0) {
                if (!in_array(strtolower($_FILES['upfp_post_featured']['type']), $allowed)) {
                    $error++;
                    $error_msg .= '<p>'.__('Invalid file type for featured image.','upfp').'</p>';
                }else{
                    $upload_status = $this->upfp_process_upload();
                    
                    if($upload_status['status'] == 'error'){
                        $error++;
                        $error_msg .= '<p>'.$upload_status['msg'].'</p>';
                    }
                }
            }
            
            if($error == '0'){
                $post_information = array(
                    'post_title' => wp_strip_all_tags( $post_title ),
                    'post_content' => $post_content,
                    'post_author'   => get_current_user_id(),
                    'post_category' => $post_category,
                    'tags_input' => $post_tags,
                    'post_type' => 'post',
                    'post_status' => $post_status_default
                );
                $post_id = wp_insert_post( $post_information );
                
                
                if ( ! is_wp_error( $post_id ) ) {
                    $content_post = get_post($post_id);
                    $post_content = $content_post->post_content;
                    
                    if(isset($upload_status['status']) && $upload_status['status'] == 'success'){
                        /************** SET POST FEATURED IMAGE *****/
                        $wp_filetype = wp_check_filetype( $upload_status['base_name'], null );
                        // Set attachment data
                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title'     => sanitize_file_name( $upload_status['base_name'] ),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        );
//echo "<pre>";print_r($attachment);exit;
                        // Create the attachment
                        $attach_id = wp_insert_attachment( $attachment, $upload_status['target_path'], $post_id );
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $upload_status['target_path'] );
                        wp_update_attachment_metadata( $attach_id, $attach_data );
                        set_post_thumbnail( $post_id, $attach_id );
                    }

                    /* Wishlist member integration */
                    //$this->create_wishlistmember_protected_post($post_id);


                    echo json_encode(array('status' => 'success', 'msg' => __('Post Created Successfully','upfp'), 'content' => $post_content));
                }else{
                    echo json_encode(array('status' => 'error', 'msg' => __('Invalid Post Submission','upfp')));
                }
                
            }else{
                echo json_encode(array('status' => 'error', 'msg' => $error_msg));
            }
        }else{
            echo json_encode(array('status' => 'error', 'msg' => __('Invalid Post Submission','upfp')));
        }
        
        exit;
        
    }
    
    public function upfp_edit_post(){
        
        $posts_settings = $this->upfp_posts_settings('posts_general');
        $post_status_default = isset($posts_settings['default_edit_post_status']) ? $posts_settings['default_edit_post_status'] : 'pending';
        
        $user_id = get_current_user_id();
        $post_id = isset($_POST['upfp_edit_post_id']) ? $_POST['upfp_edit_post_id'] : 0;
        
        if($user_id != '' && $user_id != '0'){

            if($post_id != '0'){
                $post_data = get_post($post_id);
                $post_author = $post_data->post_author;
                
                if($post_author == $user_id){
                    
                    if ( isset( $_POST ) && isset( $_POST['upfp_new_post_nonce_field'] ) && wp_verify_nonce( $_POST['upfp_new_post_nonce_field'], 'upfp_new_post_nonce' ) ) {

                        $post_title = isset($_POST['upfp_edit_post_title']) ? $_POST['upfp_edit_post_title'] : '' ;
                        $post_content = isset($_POST['upfp_edit_post_content']) ? $_POST['upfp_edit_post_content'] : '' ;
                        $post_category = isset($_POST['upfp_edit_post_categories']) ? $_POST['upfp_edit_post_categories'] : array() ;
                        $post_tags = isset($_POST['upfp_edit_post_tags']) ? $_POST['upfp_edit_post_tags'] : '' ;

                        $error = 0;
                        $error_msg = '';

                        if($post_title == ''){
                            $error++;
                            $error_msg .= '<p>'.__('Post Title is required.','upfp').'</p>';
                        }
                        if(trim($post_content) == ''){
                            $error++;
                            $error_msg .= '<p>'.__('Post Content is required.','upfp').'</p>';
                        }

                        $allowed = array("image/gif", "image/jpeg", "image/png");
                        if (isset($_FILES['upfp_edit_post_featured']) && $_FILES['upfp_edit_post_featured']['error'] == 0) {
                            if (!in_array(strtolower($_FILES['upfp_edit_post_featured']['type']), $allowed)) {
                                $error++;
                                $error_msg .= '<p>'.__('Invalid file type for featured image.','upfp').'</p>';
                            }else{
                                $upload_status = $this->upfp_process_upload();

                                if($upload_status['status'] == 'error'){
                                    $error++;
                                    $error_msg .= '<p>'.$upload_status['msg'].'</p>';
                                }
                            }
                        }

                        if($error == '0'){
                            $post_information = array(
                                'ID'            => $post_id,
                                'post_title'    => wp_strip_all_tags( $post_title ),
                                'post_content'  => $post_content,
                                'post_author'   => get_current_user_id(),
                                'post_category' => $post_category,
                                'tags_input'    => $post_tags,
                                'post_type'     => 'post',
                                'post_status'   => $post_status_default
                            );
                            $post_id = wp_update_post( $post_information );


                            if ( ! is_wp_error( $post_id ) ) {
                                $content_post = get_post($post_id);
                                $post_content = $content_post->post_content;

                                if(isset($upload_status['status']) && $upload_status['status'] == 'success'){
                                    /************** SET POST FEATURED IMAGE *****/
                                    $wp_filetype = wp_check_filetype( $upload_status['base_name'], null );
                                    // Set attachment data
                                    $attachment = array(
                                        'post_mime_type' => $wp_filetype['type'],
                                        'post_title'     => sanitize_file_name( $upload_status['base_name'] ),
                                        'post_content'   => '',
                                        'post_status'    => 'inherit'
                                    );

                                    // Create the attachment
                                    $attach_id = wp_insert_attachment( $attachment, $upload_status['target_path'], $post_id );
                                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                                    $attach_data = wp_generate_attachment_metadata( $attach_id, $upload_status['target_path'] );
                                    wp_update_attachment_metadata( $attach_id, $attach_data );
                                    set_post_thumbnail( $post_id, $attach_id );
                                }

                                echo json_encode(array('status' => 'success', 'msg' => __('Post Updated Successfully','upfp'), 'content' => $post_content));
                            }else{
                                echo json_encode(array('status' => 'error', 'msg' => __('Invalid Post Submission','upfp')));
                            }

                        }else{
                            echo json_encode(array('status' => 'error', 'msg' => $error_msg));
                        }
                    }else{
                        echo json_encode(array('status' => 'error', 'msg' => __('Invalid Post Submission','upfp')));
                    }
                }else{
                    echo json_encode(array('status' => 'error', 'msg' => __('You don\'t have permission to edit this post.','upfp')));
                }
            }else{
                echo json_encode(array('status' => 'error', 'msg' => __('Invalid post ID.','upfp')));
            }
            
        }else{
            echo json_encode(array('status' => 'error', 'msg' => __('Please login to edit posts.','upfp')));
        }
        exit;
    }
    
    /* Upload featured images for frontend post creation and edit */
    public function upfp_process_upload() {
        $params = array();
        /* File upload conditions */
        $default['allowed_extensions'] = array("image/gif", "image/jpeg", "image/png");
        $default['allowed_exts'] = array('gif','png','jpeg','jpg');

        $settings = get_option('upme_options');
        // Set default to 500KB
        $default['max_size'] = 512000;        
        $default['image_height'] = 0;
        $default['image_width']  = 0;

        // Setting Max File Size set from admin
        if (isset($settings['avatar_max_size']) && $settings['avatar_max_size'] > 0)
            $default['max_size'] = $settings['avatar_max_size'] * 1024 * 1024;
    
        $params = wp_parse_args( $params, $default );
        extract($params);
    
        $errors = '';

        if (isset($_FILES)) {
            foreach ($_FILES as $key => $array) {
                
                extract($array);
                if ($name) {
                    $clean_file = true;

                    if(in_array($type, $allowed_extensions)){
                        $image_data = @getimagesize($tmp_name);                        
                        if (!isset($image_data[0]) || !isset($image_data[1])){
                            $clean_file = false;                            
                        }else{
                            $image_height = $image_data[1];
                            $image_width  = $image_data[0];
                        }
                    }

                    preg_match("/.(".implode("|",$allowed_exts).")$/i",$name, $extstatus_matches);      

                    if (!in_array($type, $allowed_extensions) ) {
                        $errors = __('The file you have selected for has a file extension that is not allowed. Please choose a different file.','upfp').'<br/>';
                    } elseif ($size > $max_size) {
                        $errors = __('The file you have selected for %s exceeds the maximum allowed file size.', 'upfp').'<br/>';
                    } elseif ($clean_file == false) {
                        $errors = __('The file you selected for %s appears to be corrupt or not a real image file.', 'upfp').'<br/>';
                    } 
                    elseif (count($extstatus_matches) == 0) {
						$errors = __('The file you have selected for %s has a file extension that is not allowed. Please choose a different file.', 'upfp').'<br/>';
					} 

                    else {
                        
                            /* Upload image */
                            // Checking for valid uploads folder
                            if ($upload_dir = upme_get_uploads_folder_details()) {
                                $target_path = $upload_dir['path'] . "/";

                                // Checking for upload directory, if not exists then new created.
                                if (!is_dir($target_path))
                                    mkdir($target_path, 0777);

                                $base_name = sanitize_file_name(basename($name));
                                $base_name = preg_replace('/\.(?=.*\.)/', '_', $base_name);

                                $target_path = $target_path . time() . '_' . $base_name;
                                move_uploaded_file($tmp_name, $target_path);

                                do_action('upfp_after_move_uploaded_file', array('file_path' => $target_path,
                                                                                        'base_name' => $base_name,
                                                                                        'user_id' => get_current_user_id()
                                                                                        ) );
                                
                                return array('status' => 'success','base_name' => $base_name, 'target_path' => $target_path, 'msg' => __('File uploaded successfully.','upfp'));
                            }
                    }
                }else{
                    $errors = __('Please select a file to upload.','upfp');
                    return array('status' => 'error', 'msg' => $errors);
                }
            }
        }
        return array('status' => 'error', 'msg' => $errors);
    }
    
    /* Get details about the post for editing in frontend */
    public function upfp_get_post_edit_data(){
        
        $user_id = get_current_user_id();
        
        if($user_id != '' && $user_id != '0'){
            $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
            if($post_id != '0'){
                $post_data = get_post($post_id);
                $post_author = $post_data->post_author;
                
                if($post_author == $user_id){
                    
                    $categories = wp_get_post_categories( $post_id );
                    $post_categories = array();

                    foreach($categories as $c){
                        $cat = get_category( $c );
                        $post_categories[] = array( 'name' => $cat->name, 'id' => $c );
                    }
                    
                    $post_tags = array();
                    $tags = wp_get_post_tags($post_id);
                    if(is_array($tags)){
                        foreach($tags as $tag){
                            $post_tags[] = $tag->name;
                        }
                    }
                    $post_tags = implode(',',$post_tags);
                    
                    
                    $thumb_id = get_post_thumbnail_id($post_id);
                    $thumb_url = '';
                    if($thumb_id){
                        $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);
                        $thumb_url = isset($thumb_url_array[0]) ? $thumb_url_array[0] : ''; 
                    }
                    
                    
               
                    echo json_encode(array('status' => 'success', 'title' => $post_data->post_title , 'id' => $post_data->ID, 'content' => $post_data->post_content, 'categories' => $post_categories, 'tags' => $post_tags, 'featured_image' => $thumb_url ));
                }else{
                    echo json_encode(array('status' => 'error', 'msg' => __('You don\'t have permission to edit this post.','upfp')));
                }
                
            }else{
                echo json_encode(array('status' => 'error', 'msg' => __('Invalid post ID.','upfp')));
            }
            
        }else{
            echo json_encode(array('status' => 'error', 'msg' => __('Please login to edit posts.','upfp')));
        }
        exit;
    }
    
    /* Delete featured image of post from frontend edit screen */
    public function upfp_delete_post_featured(){
        $user_id = get_current_user_id();
        
        if($user_id != '' && $user_id != '0'){
            $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
            if($post_id != '0'){
                if ( isset( $_POST ) && isset( $_POST['upfp_new_post_nonce_field'] ) && wp_verify_nonce( $_POST['upfp_new_post_nonce_field'], 'upfp_new_post_nonce' ) ) {
                    delete_post_thumbnail( $post_id );
                    echo json_encode(array('status' => 'success', 'msg' => __('Featured Image deleted.','upfp')));
                }else{
                    echo json_encode(array('status' => 'error', 'msg' => __('Invalid Request','upfp')));
                }
            }else{
                echo json_encode(array('status' => 'error', 'msg' => __('Invalid post ID.','upfp')));
            }
            
        }else{
            echo json_encode(array('status' => 'error', 'msg' => __('Please login to edit posts.','upfp')));
        }
        exit;
    }
    
    public function upfp_verify_permission_status($allowed_type_key,$allowed_roles_key){
        global $upme_roles;
        
        $user_id = get_current_user_id();
        
        $status = FALSE;
        
        $posts_settings = $this->upfp_posts_settings('posts_general');
        $post_status = isset($posts_settings[$allowed_type_key]) ? $posts_settings[$allowed_type_key] : '0';
        switch($post_status){
            case '0':
                break;
            case '1':
                if($user_id != '0'){
                    $status = TRUE;
                }
                break;
            case '2':
                if($user_id != '0'){
                    $post_roles = isset($posts_settings[$allowed_roles_key]) ? $posts_settings[$allowed_roles_key] : array();
                    
                    $user_roles = $upme_roles->upme_get_user_roles_by_id($user_id);
                    foreach($post_roles as $role){
                        if(in_array($role,$user_roles)){
                            $status = TRUE;
                        }
                    }
                    
                }
                break;
        }
        
        return $status;
    }
    
    public function upfp_delete_post(){
        
        $posts_settings = $this->upfp_posts_settings('posts_general');
        
        $user_id = get_current_user_id();
        $post_id = isset($_POST['upfp_post_id']) ? $_POST['upfp_post_id'] : 0;
        
        if($user_id != '' && $user_id != '0'){

            if($post_id != '0'){
                $post_data = get_post($post_id);
                $post_author = $post_data->post_author;
                
                if($post_author == $user_id){
                    
                    if ( isset( $_POST ) && isset( $_POST['upfp_delete_post_nonce_field'] ) && wp_verify_nonce( $_POST['upfp_delete_post_nonce_field'], 'upfp_delete_post_nonce' ) ) {

                            wp_delete_post( $post_id );
                            echo json_encode(array('status' => 'success', 'msg' => __('Post Deleted Successfully','upfp')));

                    }else{
                        echo json_encode(array('status' => 'error', 'msg' => __('Invalid Post Delete','upfp')));
                    }
                }else{
                    echo json_encode(array('status' => 'error', 'msg' => __('You don\'t have permission to delete this post.','upfp')));
                }
            }else{
                echo json_encode(array('status' => 'error', 'msg' => __('Invalid post ID.','upfp')));
            }
            
        }else{
            echo json_encode(array('status' => 'error', 'msg' => __('Please login to delete posts.','upfp')));
        }
        exit;
    }

    public function create_wishlistmember_protected_post($post_id){
        global $wpdb;
        if (class_exists('WishListMember')) {
            $default_protect = (bool) $this->wishlistmember_get_option('default_protect');

            if($default_protect){
                $query = $wpdb->prepare("INSERT IGNORE INTO ".$wpdb->prefix . "wlm_contentlevels (`content_id`,`level_id`,`type`) VALUES (%d,%s,%s)", $post_id, 'Protection', 'Post');
                $wpdb->query($query);
            }
        }
    }

    public function wishlistmember_get_option($key){
        global $wpdb;
        $table = $wpdb->prefix . 'wlm_options';
        $query = "
                SELECT option_value
                FROM $table
                WHERE option_name = '".$key."'
                ";
        $key   = $wpdb->get_results($query);
        return $key[0]->option_value;
    }
}
?>