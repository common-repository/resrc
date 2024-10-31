<?php
/**
 * ReSRC for WordPress
 *
 * The ReSRC for WordPress plugin optimizes and delivers perfect images on any device by using our responsive image engine. Get started for free! You will be up and running in minutes. http://www.resrc.it
 *
 * @package   ReSRC_For_WordPress
 * @author    ReSRC <team@resrc.it>
 * @license   GPL-2.0+
 * @link      http://www.resrc.it/wordpress
 * @copyright 2014 ReSRC
 *
 * @wordpress-plugin
 * Plugin Name:       ReSRC
 * Plugin URI:        http://www.resrc.it/wordpress
 * Description:       The ReSRC for WordPress plugin optimizes and delivers perfect images on any device by using our responsive image engine. Get started for free! You will be up and running in minutes. http://www.resrc.it
 * Version:           1.0.0
 * Author:            ReSRC
 * Author URI:        http://www.resrc.it
 * Text Domain:       resrc
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/resrcit/wp-resrc
 * GitHub Branch:     master
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-resrc.php' );
// require_once ( plugin_dir_path( __FILE__ ) .'class-settings-api.php' );


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
 
register_activation_hook( __FILE__, array( 'ReSRC_For_WordPress', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'ReSRC_For_WordPress', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'ReSRC_For_WordPress', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-resrc-admin.php' );
	add_action( 'plugins_loaded', array( 'ReSRC_For_WordPress_Admin', 'get_instance' ) );

}