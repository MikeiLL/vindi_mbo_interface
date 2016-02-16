<?php
/**
 * This file contains main plugin class and, defines and plugin loader.
 *
 * The mZoo Vindi Mindbody Interface plugin utilizes the Devin Crossman Mindbody API
 * to interface with mindbody's SOAP API so that Vindi payments can be recorded in the MBO
 * API when procesed by Vindi. This particular file is responsible for
 * including the necessary dependencies and starting the plugin.
 *
 * @package MZVINDIAPI
 *
 * @wordpress-plugin
 * Plugin Name: 	mZoo Vindi MBO Interface
 * Description: 	Interface Vindi Payments with MindbodyOnline. Requires mZ-mindbody-api plugin.
 * Version: 		1.0.0
 * Author: 			mZoo.org
 * Author URI: 		http://www.mZoo.org/
 * Plugin URI: 		http://www.mzoo.org/
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: 	mZ-vindi-mbo
 * Domain Path: 	/languages
*/

if ( !defined( 'WPINC' ) ) {
    die;
}

//define plugin path and directory
define( 'MZ_VINDI_MBO_DIR', plugin_dir_path( __FILE__ ) );
define( 'MZ_VINDI_MBO_URL', plugin_dir_url( __FILE__ ) );

add_action( 'admin_init', 'vindi_mbo_has_mindbody_api' );
function vindi_mbo_has_mindbody_api() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'mz-mindbody-api/mZ-mindbody-api.php' ) ) {
        add_action( 'admin_notices', 'child_plugin_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

function child_plugin_notice(){
    ?><div class="error"><p>Sorry, but Child Plugin requires the Parent plugin to be installed and active.</p></div><?php
}

/**
 * The MZ Mindbody API Admin defines all the functionality for the dashboard
 * of the plugin.
 *
 * This class defines version and loads the actions and functions
 * that create the dashboard.
 *
 * @since    2.1.0
 */
class MZ_Vindi_MBO_Admin {
    
    protected $version;
 
    public function __construct( $version ) {
        $this->version = $version;
        $this->load_sections();
        }
        
    public function load_sections() {
        require_once MZ_VINDI_MBO_DIR .'lib/sections.php';
        }
}

/**
 * The MZ Mindbody API Loader class is responsible
 * coordinating most of the actions and filters used in the plugin.
 *
 * This class maintains two internal collections - one for actions, one for
 * hooks - each of which are coordinated through external classes that
 * register the various hooks through this class. Note that the actions
 * specific to the admin sections are loaded in /lib/sections.php
 *
 * @since    2.1.0
 */
class MZ_Vindi_MBO_Loader {
    /**
     * A reference to the collection of actions used throughout the plugin.
     *
     * @access protected
     * @var    array    $actions    The array of actions that are defined throughout the plugin.
     */
    protected $actions;
 
    /**
     * A reference to the collection of filters used throughout the plugin.
     *
     * @access protected
     * @var    array    $actions    The array of filters that are defined throughout the plugin.
     */
    protected $filters;
 
    /**
     * Instantiates the plugin by setting up the data structures that will
     * be used to maintain the actions and the filters.
     */
    public function __construct() {
 
        $this->actions = array();
        $this->filters = array();
 
    }
    
    /**
     * Registers the actions with WordPress and the respective objects and
     * their methods.
     *
     * @param  string    $hook        The name of the WordPress hook to which we're registering a callback.
     * @param  object    $component   The object that contains the method to be called when the hook is fired.
     * @param  string    $callback    The function that resides on the specified component.
     */ 
    public function add_action( $hook, $component, $callback ) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback );
    }

    /**
     * Registers the filters with WordPress and the respective objects and
     * their methods.
     *
     * @param  string    $hook        The name of the WordPress hook to which we're registering a callback.
     * @param  object    $component   The object that contains the method to be called when the hook is fired.
     * @param  string    $callback    The function that resides on the specified component.
     */ 
    public function add_filter( $hook, $component, $callback ) {
        $this->filters = $this->add( $this->filters, $hook, $component, $callback );
    }
    
    /**
     * Registers the filters with WordPress and the respective objects and
     * their methods.
     *
     * @access private
     *
     * @param  array     $hooks       The collection of existing hooks to add to the collection of hooks.
     * @param  string    $hook        The name of the WordPress hook to which we're registering a callback.
     * @param  object    $component   The object that contains the method to be called when the hook is fired.
     * @param  string    $callback    The function that resides on the specified component.
     *
     * @return array                  The collection of hooks that are registered with WordPress via this class.
     */ 
    private function add( $hooks, $hook, $component, $callback ) {
 
        $hooks[] = array(
            'hook'      => $hook,
            'component' => $component,
            'callback'  => $callback
        );
 
        return $hooks;
 
    }
 
     /**
     * Calls the add methods for above referenced filters and actions and registers them with WordPress.
     */
    public function run() {
 		
        foreach ( $this->filters as $hook ) {
            add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
        }
 
        foreach ( $this->actions as $hook ) {
        	if (($hook['callback'] == 'instantiate_mbo_API') && ($hook['component'] == 'MZ_Mindbody_Init')) {
        		add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
        		//mz_pr(MZ_MBO_Instances::$instances_of_MBO);
        	}else{
            	add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
            }
        }
 
    }
 
}


class MZ_Vindi_MBO {
 
    protected $loader;
 
    protected $plugin_slug;
 
    protected $version;
 
    public function __construct() {
 
        $this->plugin_slug = 'mZ-vindi-mbo';
        $this->version = '1.0.0';
 
        $this->load_dependencies();
        $this->define_main_hooks();
        $this->add_shortcodes();
 
    }
 
    private function load_dependencies() {
        	
        //include_once(dirname( __FILE__ ) . '/mindbody-php-api/MB_API.php');

		foreach ( glob( plugin_dir_path( __FILE__ )."inc/*.php" ) as $file )
			include_once $file;
	
		//Functions

		require_once MZ_VINDI_MBO_DIR .'lib/functions.php';
        	
        $this->loader = new MZ_Vindi_MBO_Loader();
    }
 
    private function define_admin_hooks() {
 
        $admin = new MZ_Vindi_MBO_Admin( $this->get_version() );
        $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
        $this->loader->add_action( 'add_meta_boxes', $admin, 'add_meta_box' );
    }
    
    private function define_main_hooks() {
 
        $this->loader->add_action( 'init', $this, 'myStartSession' );
        $this->loader->add_action( 'wp_logout', $this, 'myStartSession' );
        $this->loader->add_action( 'wp_login', $this, 'myEndSession' );
        
        }

    public function myStartSession() {
			if ((function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) {
				  session_start();
				}
		}

    public function myEndSession() {
			session_destroy ();
		}
 
 	private function add_shortcodes() {
 	
 		$mz_vindi_sandbox = new MZ_Vindi_Sandbox();
 		
        add_shortcode('mz-vindi-sandbox', array($mz_vindi_sandbox, 'mZ_vindi_mbo_sandbox'));

    }
 
    public function run() {
        $this->loader->run();
    }
 
    public function get_version() {
        return $this->version;
    }
 
}

function mZ_vindi_MBO_load_textdomain() {
	load_plugin_textdomain('mZ-vindi-mbo',false,dirname(plugin_basename(__FILE__)) . '/languages');
	}
add_action( 'plugins_loaded', 'mZ_vindi_MBO_load_textdomain' );

function mZ_vindi_mbo_activation() {
	//Don't know if there's anything we need to do here.
}

function mZ_vindi_mbo_deactivation() {
	//Don't know if there's anything we need to do here.
}

//register uninstaller
register_uninstall_hook(__FILE__, 'mZ-vindi-mbo_uninstall');

function mZ_vindi_mbo_uninstall(){
	//actions to perform once on plugin uninstall go here
	delete_option('mz_mindbody_options');
}

if (!function_exists( 'mZ_latest_jquery' )){
	function mZ_latest_jquery(){
		//	Use latest jQuery release
		if( !is_admin() ){
			wp_deregister_script('jquery');
			wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"), false, '');
			wp_enqueue_script('jquery');
		}
	}
	add_action('wp_enqueue_scripts', 'mZ_latest_jquery');
}


    
if ( is_admin() )
{     
	$admin_backend = new MZ_Vindi_MBO_Admin('2.1.0');
	//Start Ajax Signup

}
else
{// non-admin enqueues, actions, and filters

function run_mz_vindi_mbo_schedule_api() {
 
    $mz_mbo = new MZ_Vindi_MBO();
    $mz_mbo->run();
 
}
 
run_mz_vindi_mbo_schedule_api();
	
/*	function load_jquery() {
		wp_enqueue_script( 'jquery' );
	}
	add_action( 'wp_enqueue_script', 'load_jquery' );
	*/
	
}//EOF Not Admin


?>
