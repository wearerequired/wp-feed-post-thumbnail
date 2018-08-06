# WP Feed Post Thumbnail #
* Contributors: wearerequired, neverything, swissspidy
* Tags: rss feed, rss, feed, thumbnail, mrss, media rss
* Requires at least: 4.0
* Tested up to: 4.9
* Requires PHP: 5.3
* Stable tag: 2.1.1
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds MRSS namespace to the feed and uses post-thumbnail as media element in the feed. Settings available under Settings -> Reading.

## Description ##

With this plugin, an MRSS namespace is added to the site’s RSS feed to include each post’s thumbnail.

WP Feed Post Thumbnail is very lightweight and only adds two small options under Settings -> Reading.

## Installation ##

### Manual Installation ###

1. Upload the entire `/wp-feed-post-thumbnail` directory to the `/wp-content/plugins/` directory.
2. Activate WP Feed Post Thumbnail through the 'Plugins' menu in WordPress.
3. Enjoy more awesome RSS feeds

## Frequently Asked Questions ##

### Does the RSS feed still validate with this plugin enabled? ##

Yep, we add the proper XML namespaces for that. Everything just works as expected!

### Any way to force the plugin to always add a certain thumbnail size? ###

Yes. The plugin has two filters available for this:

	// Filters the size on media:content tag. Defaults to 'full'.
	add_filter( 'wp_feed_post_thumbnail_image_size_full', function( $size ) {
		return 'large'; // Return any registered image size.
	}, 10, 1 );
	
	// Filters the size on the media:thumbnail tag. Defaults to 'thumbnail'.
	add_filter( 'wp_feed_post_thumbnail_image_size_thumbnail', function( $size ) {
		return 'medium'; // Return any registered image size.
	}, 10, 1 );

## Screenshots ##

1. Reading Settings

## Contribute ##

If you would like to contribute to this plugin, report an isse or anything like that, please note that we develop this plugin on [GitHub](https://github.com/wearerequired/required-wp-feed-post-thumbnail).

Developed by [required](https://required.com/ "Team of experienced web professionals from Switzerland & Germany")

## Changelog ##

### 2.1.1 ###

* Fixed: Improved compatibility with Jetpack.

### 2.1.0 ###
* Enhancement: Translations moved to https://translate.wordpress.org/projects/wp-plugins/wp-feed-post-thumbnail.
* Enhancement: Simplified code base by removing `WP_Stack_Plugin2` dependency.

### 2.0.1 ###
* Enhancement: Better escaping of feed data.
* Enhancement: Improved translatable strings.
* Fixed: Corrected settings link in the plugin list table.

### 2.0.0 ###
* Enhancement: Major rewrite using the `grunt-wp-plugin` template. Breaks backwards compatibility due to renamed options.
* Fixed: Prevent notices in the RSS feed output.

### 1.1.1 ###
* Added missing method `WP_Feed_Post_Thumbnail->get_plugin_slug()`;

### 1.1.0 ###
* Code cleanup

### 1.0.0 ###
* Initial Release

## Upgrade Notice ##

### 2.1.1 ###

This release includes a small bug fix to improve compatibility with Jetpack.

### 2.0.1 ###
Some minor improvements and bug fixes. 100% compatible with WordPress 4.4.

### 2.0.0 ###
Major rewrite to make the plugin more future-proof. Grab it while it’s hot!
