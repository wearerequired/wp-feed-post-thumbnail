<?php
/**
 * @package   WP_Feed_Post_Thumbnail
 * @author    Silvan Hagen <email@example.com>
 * @license   GPL-2.0+
 * @link      http://required.ch
 * @copyright 2015 required gmbh
 *
 * @wordpress-plugin
 * Plugin Name:       WP Feed Post Thumbnail
 * Plugin URI:        https://github.com/wearerequired/required-wp-feed-post-thumbnail
 * Description:       Adds MRSS namespace to the feed and uses post-thumbnail as media element in the feed. Settings available under Settings -> Reading.
 * Version:           1.1.0
 * Author:            required gmbh
 * Author URI:        http://required.ch
 * Text Domain:       required-wp-feed-post-thumbnail
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/wearerequired/required-wp-feed-post-thumbnail
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) or die;

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-required-wp-feed-post-thumbnail.php' );
add_action( 'plugins_loaded', array( 'WP_Feed_Post_Thumbnail', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-required-wp-feed-post-thumbnail-admin.php' );
	add_action( 'plugins_loaded', array( 'WP_Feed_Post_Thumbnail_Admin', 'get_instance' ) );
}