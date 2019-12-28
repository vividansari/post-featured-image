<?php
/**
 * Plugin Name:       Good Read posts
 * Description:       A handy little plugin to get good read post and insert as post;
 * Plugin URI:        http://vividwebsolutions.in/
 * Version:           1.0.0
 * Author:            Team-vivid
 * Author URI:        http://vividwebsolutions.in/
 * Requires at least: 3.0.0
 * Tested up to:      4.4.2
 * 
 * @package grp_post
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'GRP_PLUGIN_DIR', dirname( __FILE__ ) . '/' );
define( 'GRP_IMAGE_DIR', dirname( __FILE__ ) . '/images/' );
define( 'GRP_FONT_DIR', dirname( __FILE__ ) . '/fonts/' );
define( 'GRP_PLUGIN_INC_DIR', dirname( __FILE__ ) . '/custom/inc/' );
define( 'GRP_PLUGIN_URL', plugins_url( "", __FILE__ ) . '/' );


/**
 * Main grp_post Class
 *
 * @class grp_post
 * @version	1.0.0
 * @since 1.0.0
 * @package	grp_post
 */
final class grp_post {

	/**
	 * Set up the plugin
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'grp_post_setup' ), -1 );
		require_once( 'custom/functions.php' );
		if(is_admin()){
			require_once('custom/admin/setting.php');
		}
	}

	/**
	 * Setup all the things
	 */
	public function grp_post_setup() {
		add_action( 'wp_enqueue_scripts', array( $this, 'grp_post_css' ), 999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'grp_post_js' ) );
		
	}

	/**
	 * Enqueue the CSS
	 *
	 * @return void
	 */
	public function grp_post_css() {
		wp_enqueue_style( 'custom-css', plugins_url( '/custom/assets/css/style.css', __FILE__ ) );
	}

	/**
	 * Enqueue the Javascript
	 *
	 * @return void
	 */
	public function grp_post_js() {
		wp_enqueue_script( 'custom-js', plugins_url( '/custom/assets/js/custom.js', __FILE__ ), array( 'jquery' ) );
	}
	
} // End Class

/**
 * The 'main' function
 *
 * @return void
 */
function grp_post_main() {
	new grp_post();
}

/**
 * Initialise the plugin
 */
add_action( 'plugins_loaded', 'grp_post_main' );
?>