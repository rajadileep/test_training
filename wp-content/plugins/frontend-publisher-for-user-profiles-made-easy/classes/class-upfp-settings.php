<?php

class UPFP_Settings{
    
    public function __construct(){
        
        add_action('admin_menu', array(&$this, 'add_menu'), 9);
        add_action('admin_init', array($this,'save_settings_page') );
        
    }
    
    public function add_menu(){
        add_menu_page(__('UPFP Frontend Publisher', "upfp"), __("UPFP Frontend Publisher", "upfp"),'manage_options','upfp-posts-manager',array(&$this,'posts_settings'));
    }
    
    public function posts_settings(){
        global $upfp,$upfp_settings_data;
        
        add_settings_section( 'upfp_section_posts_general', __('Post Settings','upfp'), array( &$this, 'filter_section_general_desc' ), 'upfp-posts-manager' );
        
//        add_settings_section( 'upfp_section_search_features', __('Features Settings','upfp'), array( &$this, 'filter_section_general_desc' ), 'upfp-contacts-manager' );
        
        $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'upfp_section_posts_general';
        $upfp_settings_data['tab'] = $tab;
        
        $tabs = $this->plugin_options_tabs('upfp_posts',$tab);
   
        $upfp_settings_data['tabs'] = $tabs;
        
        $tab_content = $this->plugin_options_tab_content($tab);
        $upfp_settings_data['tab_content'] = $tab_content;
        
        ob_start();
		$upfp->template_loader->get_template_part( 'menu-page-container');
		$display = ob_get_clean();
		echo $display;
        
    
    }

    
    public function plugin_options_tabs($type,$tab) {
        $current_tab = $tab;
        $this->plugin_settings_tabs = array();
        
        switch($type){
            case 'upfp_posts':
                $this->plugin_settings_tabs['upfp_section_posts_general']  = __('Post Settings','upfp');
//                $this->plugin_settings_tabs['upfp_section_search_features']  = __('Features Settings','upfp');
                break;

        }
        
        ob_start();
        ?>

        <h2 class="nav-tab-wrapper">
        <?php 
            foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
            $page = isset($_GET['page']) ? $_GET['page'] : '';
        ?>
                <a class="nav-tab <?php echo $active; ?> " href="?page=<?php echo $page; ?>&tab=<?php echo $tab_key; ?>"><?php echo $tab_caption; ?></a>
            
        <?php } ?>
        </h2>

        <?php
                
        return ob_get_clean();
    }
    
    public function plugin_options_tab_content($tab,$params = array()){
        global $upfp,$upfp_settings_data;
        
        $upfp_options = get_option('upfp_options');
        
        ob_start();
        switch($tab){

            
            case 'upfp_section_posts_general':                
	            $data = isset($upfp_options['posts_general']) ? $upfp_options['posts_general'] : array();
           
                $upfp_settings_data['default_new_post_status'] = isset($data['default_new_post_status']) ? $data['default_new_post_status'] : 'pending';
                $upfp_settings_data['default_edit_post_status'] = isset($data['default_edit_post_status']) ? $data['default_edit_post_status'] : 'pending';
//                $upfp_settings_data['default_delete_post_status'] = isset($data['default_delete_post_status']) ? $data['default_delete_post_status'] : 'pending';
                $upfp_settings_data['posts_per_page'] = isset($data['posts_per_page']) ? $data['posts_per_page'] : '10';
                $upfp_settings_data['new_post_allowed_type'] = isset($data['new_post_allowed_type']) ? $data['new_post_allowed_type'] : '0';
                $upfp_settings_data['edit_post_allowed_type'] = isset($data['edit_post_allowed_type']) ? $data['edit_post_allowed_type'] : '0';
                $upfp_settings_data['delete_post_allowed_type'] = isset($data['delete_post_allowed_type']) ? $data['delete_post_allowed_type'] : '0';
    
                $upfp_settings_data['new_post_allowed_roles'] = isset($data['new_post_allowed_roles']) ? $data['new_post_allowed_roles'] : array('administrator');
                $upfp_settings_data['edit_post_allowed_roles'] = isset($data['edit_post_allowed_roles']) ? $data['edit_post_allowed_roles'] : array('administrator');
                $upfp_settings_data['delete_post_allowed_roles'] = isset($data['delete_post_allowed_roles']) ? $data['delete_post_allowed_roles'] : array('administrator');

                $upfp_settings_data['tab'] = $tab;
            
                $upfp->template_loader->get_template_part('posts-general-settings');            
                break;
            
        }
        
        $display = ob_get_clean();
        return $display;
        
    }
    
    public function save_settings_page(){
        
        $upfp_settings_pages = array('upfp-posts-manager');
        if(isset($_POST['upfp_tab']) && isset($_GET['page']) && in_array($_GET['page'],$upfp_settings_pages)){
            $tab = '';
            if ( isset ( $_POST['upfp_tab'] ) )
               $tab = $_POST['upfp_tab']; 

            if($tab != ''){
                $func = 'save_'.$tab;
                $this->$func();
            }
        }
    }

    
    public function save_upfp_section_posts_general(){
        
        if(isset($_POST['upfp_posts_general'])){
            foreach($_POST['upfp_posts_general'] as $k=>$v){
                $this->settings[$k] = $v;
            }            
            
        }
        
        $upfp_options = get_option('upfp_options');
        $upfp_options['posts_general'] = $this->settings;
        update_option('upfp_options',$upfp_options);
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );  

        
    } 
    
    
    public function admin_notices(){
        ?>
        <div class="updated">
          <p><?php esc_html_e( 'Settings saved successfully.', 'upfp' ); ?></p>
       </div>
        <?php
    }

    
}


