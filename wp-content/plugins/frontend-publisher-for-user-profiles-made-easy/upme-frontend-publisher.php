<?php
/*
  Plugin Name: UPME Frontend Publisher
  Plugin URI: http://profileplugin.com/frontend-publisher-addon/
  Description: Manage frontend post publishing for your users.
  Version: 1.3
  Author: Rakhitha Nimesh
  Author URI: http://upmeaddons.innovativephp.com
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function upfp_get_plugin_version() {
    $default_headers = array('Version' => 'Version');
    $plugin_data = get_file_data(__FILE__, $default_headers, 'plugin');
    return $plugin_data['Version'];
}

/* Validating existence of required plugins */
add_action( 'plugins_loaded', 'upfp_plugin_init' );

function upfp_plugin_init(){
    if(!class_exists('UPME')){
        add_action( 'admin_notices', 'upfp_plugin_admin_notice' );
    }else{
        UPME_Frontend_Publisher();
    }
}

function upfp_plugin_admin_notice() {
   $message = __('<strong>UPME Frontend Publisher Addon</strong> requires <strong>User Profiles Made Easy</strong> plugin to function properly','upmeinc');
   echo '<div class="error"><p>'.$message.'</p></div>';
}

if( !class_exists( 'UPME_Frontend_Publisher' ) ) {
    
    class UPME_Frontend_Publisher{
    
        private static $instance;
        public $upfp_options;

        public static function instance() {
            
            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof UPME_Frontend_Publisher ) ) {
                self::$instance = new UPME_Frontend_Publisher();
                self::$instance->setup_constants();
                
                

                add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
                self::$instance->includes();
                
                add_action('admin_enqueue_scripts',array(self::$instance,'load_admin_scripts'),9);
                add_action('wp_enqueue_scripts',array(self::$instance,'load_scripts'),9);
                 
                self::$instance->template_loader        = new UPFP_Template_Loader();
                self::$instance->publisher              = new UPFP_Publisher();
                self::$instance->settings               = new UPFP_Settings();

                
            }
            return self::$instance;
        }

        public function setup_constants() {
            $this->upfp_options = get_option('upfp_options');
        }
        
        public function load_scripts(){            
            $upme_settings = get_option('upme_options');
            if (!wp_script_is('upme_fancy_box') && '0' == $upme_settings['disable_fancybox_script_styles']) {
                wp_register_script('upme_fancy_box', upme_url . 'js/upme-fancybox.js', array('jquery'));
                wp_enqueue_script('upme_fancy_box');
            }
            
            wp_register_script('upme_chosen_js', upme_url . 'admin/js/chosen/chosen.jquery.js');
            wp_enqueue_script('upme_chosen_js');

            wp_register_style('upme_chosen_css', upme_url . 'admin/js/chosen/chosen.css');
            wp_enqueue_style('upme_chosen_css');

            wp_register_style('upfp-front-style', UPFP_PLUGIN_URL . 'css/upfp-front.css');
            wp_enqueue_style('upfp-front-style');
            
            wp_register_script('upfp-front', UPFP_PLUGIN_URL . 'js/upfp-front.js', array('jquery'));
            wp_enqueue_script('upfp-front');
            
            wp_localize_script(
                'upfp-front',
                'UPFPFront',
                array(
                    'AdminAjax' => admin_url( 'admin-ajax.php' ), // URL to WordPress ajax handling page
                    'nonce' => wp_create_nonce('upfp-posts'),
                    'confirmPostDelete' => __('Do you want to delete this post?','upfp'),
                    'UPFP_PLUGIN_URL' => UPFP_PLUGIN_URL,
                )
            );
           
        }
        
        public function load_admin_scripts(){
            
            wp_register_style('upfp-admin-style', UPFP_PLUGIN_URL . 'css/upfp-admin.css');
            wp_enqueue_style('upfp-admin-style');
            
            wp_register_script('upfp-admin', UPFP_PLUGIN_URL . 'js/upfp-admin.js', array('jquery'));
            wp_enqueue_script('upfp-admin');
        }
        
        private function includes() {
            
            require_once UPFP_PLUGIN_DIR . 'classes/class-upfp-template-loader.php';
            require_once UPFP_PLUGIN_DIR . 'classes/class-upfp-publisher.php';
            
            require_once UPFP_PLUGIN_DIR . 'classes/class-upfp-settings.php';

            if ( is_admin() ) {
            }
        }

        public function load_textdomain() {
            
        }
        
    }
}

// Plugin version
if ( ! defined( 'UPFP_VERSION' ) ) {
    define( 'UPFP_VERSION', '1.3' );
}

// Plugin Folder Path
if ( ! defined( 'UPFP_PLUGIN_DIR' ) ) {
    define( 'UPFP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin Folder URL
if ( ! defined( 'UPFP_PLUGIN_URL' ) ) {
    define( 'UPFP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

function UPME_Frontend_Publisher() {
    global $upfp;
	$upfp = UPME_Frontend_Publisher::instance();
}

