# Feed Post Thumbnail #
Contributors: wearerequired, neverything, swissspidy, grapplerulrich  
Tags: rss feed, featured image, feed, thumbnail, mrss  
Requires at least: 6.0  
Tested up to: 6.2  
Requires PHP: 7.4  
Stable tag: 2.1.2  
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html  

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

### Does the RSS feed still validate with this plugin enabled? ###

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

### Can I change which images are shown? ###

Yes, The plugin has two filters available for this:

	// Filters the featured image attachment post object.
	add_filter( 'wp_feed_post_thumbnail_image', function( $thumbnail ) {
		return ''; // Return an empty string or another attachment post object.
	}, 10, 1 );

	// Filters the array of attachment post objects. Defaults to featured image post object if exists.
	add_filter( 'wp_feed_post_thumbnail_images', function( $images ) {
		$attachment_id = '123';
		$images[] =  get_post( $attachment_id ); // Additional attachment post object.
		return images;
	}, 10, 1 );

### Can I change the title, description or author shown with the image? ###

Yes, there is a filter for each of these things:

	// Filters the title on the media:title tag. Defaults to attachment title.
	add_filter( 'wp_feed_post_thumbnail_title', function( $title ) {
		return 'Override title'; // Return any plain text.
	}, 10, 1 );

	// Filters the text on the media:description tag. Defaults to attachment description.
	add_filter( 'wp_feed_post_thumbnail_description', function( $description ) {
		return 'Same description for all images'; // Return any plain string.
	}, 10, 1 );

	// Filters the name of the author on the media:copyright tag. Defaults to attachment author.
	add_filter( 'wp_feed_post_thumbnail_author', function( $author_name ) {
		return 'Matt'; // Return any plain string.
	}, 10, 1 );

## Screenshots ##

1. Reading Settings

## Contribute ##

If you would like to contribute to this plugin, report an issue or anything like that, please note that we develop this plugin on [GitHub](https://github.com/wearerequired/required-wp-feed-post-thumbnail).

Developed by [required](https://required.com/ "Team of experienced web professionals from Switzerland & Germany")

## Changelog ##

### 2.1.2 - 2019-03-11 ###
* Enhancement: Minor code improvements.
* Enhancement: New filter `wp_feed_post_thumbnail_images` to list multiple images
* Changed: minimum PHP version 5.4 & minimum WP version 4.7

### 2.1.1 - 2018-08-06 ###
* Fixed: Improved compatibility with Jetpack.

## Upgrade Notice ##

### 2.1.1 ###
This release includes a small bug fix to improve compatibility with Jetpack.

### 2.0.1 ###
Some minor improvements and bug fixes. 100% compatible with WordPress 4.4.

### 2.0.0 ###
Major rewrite to make the plugin more future-proof. Grab it while it’s hot!
