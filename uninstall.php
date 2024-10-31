<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   ReSRC_For_WordPress
 * @author    ReSRC <team@resrc.it>
 * @license   GPL-2.0+
 * @link      http://www.resrc.it/wordpress
 * @copyright 2014 ReSRC
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}