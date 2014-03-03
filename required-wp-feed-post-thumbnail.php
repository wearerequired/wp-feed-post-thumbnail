<?php
/**
 * @package   WP_Feed_Post_Thumbnail
 * @author    Silvan Hagen <email@example.com>
 * @license   GPL-2.0+
 * @link      http://required.ch
 * @copyright 2014 required gmbh
 *
 * @wordpress-plugin
 * Plugin Name:       r+ WP Feed Post Thumbnail
 * Plugin URI:        https://github.com/wearerequired/required-wp-feed-post-thumbnail
 * Description:       @TODO
 * Version:           1.0.0
 * Author:            hubeRsen, neverything
 * Author URI:        http://required.ch
 * Text Domain:       required-wp-feed-post-thumbnail
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/wearerequired/required-wp-feed-post-thumbnail
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-required-wp-feed-post-thumbnail.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'WP_Feed_Post_Thumbnail', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WP_Feed_Post_Thumbnail', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'WP_Feed_Post_Thumbnail', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-required-wp-feed-post-thumbnail-admin.php' );
	add_action( 'plugins_loaded', array( 'WP_Feed_Post_Thumbnail_Admin', 'get_instance' ) );

}
