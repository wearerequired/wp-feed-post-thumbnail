<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   WP_Feed_Post_Thumbnail
 * @author    Silvan Hagen <silvan@required.ch>
 * @license   GPL-2.0+
 * @link      http://required.ch
 * @copyright 2015 required gmbh
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'required-wp-feed-post-thumbnail_options' );