# WP Feed Post Thumbnail #
Contributors:      wearerequired  
Donate link:       http://required.ch  
Tags:  
Requires at least: 4.0  
Tested up to:      4.2  
Stable tag:        0.1.0  
License:           GPLv2 or later  
License URI:       http://www.gnu.org/licenses/gpl-2.0.html  

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

None so far. But you can ask as any time on [twitter](https://twitter.com/wearerequired)!

### Question ###

Answer

## Screenshots ##

1. Description of first screenshot

## Changelog ##

### 2.0.0 ###
* Enhancement: Major rewrite using the `grunt-wp-plugin` template. Breaks backwards compatibility due to renamed options.
* Fixed: Prevent notices in the RSS feed output.

### 1.1.1 ###
* Added missing method WP_Feed_Post_Thumbnail->get_plugin_slug();

### 1.1.0 ###
* Code cleanup

### 1.0.0 ###
* Initial Release

## Upgrade Notice ##

### 2.0.0 ###
Major rewrite to make the plugin more future-proof. Grab it while it’s hot!
