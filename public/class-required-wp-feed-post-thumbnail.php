<?php
/**
 * WP Feed Post Thumbnail
 *
 * @package   WP_Feed_Post_Thumbnail
 * @author    Silvan Hagen <silvan@required.ch>
 * @license   GPL-2.0+
 * @link      http://required.ch
 * @copyright 2015 required gmbh
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-required-wp-feed-post-thumbnail-admin.php`
 *
 * @package WP_Feed_Post_Thumbnail
 * @author  Silvan Hagen <silvan@required.ch>
 */
class WP_Feed_Post_Thumbnail {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'required-wp-feed-post-thumbnail';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	public static $default_options = array(
		'author'      => 1,
		'description' => 1,
	);

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		add_action( 'rss2_ns', array( $this, 'add_feed_namespace' ) );
		add_filter( 'rss2_item', array( $this, 'add_feed_item_media' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide       True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide       True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int $blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {

		$options = get_option( 'required-wp-feed-post-thumbnail_options' );

		if ( ! $options ) {

			update_option( 'required-wp-feed-post-thumbnail_options', self::$default_options );

		}

	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// Nothing todo here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Add MRSS namespace to feed
	 *
	 * @since    1.0.0
	 */
	public function add_feed_namespace() {

		echo 'xmlns:media="http://search.yahoo.com/mrss/"';

	}

	/**
	 * Add Media Element to Feed Item
	 *
	 * @since    1.0.0
	 */
	public function add_feed_item_media() {

		global $post;

		$options = get_option( $this->plugin_slug . '_options' );

		$thumbnail = get_post( get_post_thumbnail_id( $post->ID ) );
		$thumbnail = apply_filters( 'required_wp_feed_post_thumbnail_filter', $thumbnail );

		if ( $thumbnail ) {
			$img_attr       = wp_get_attachment_image_src( $thumbnail->ID, apply_filters( 'required_wp_feed_post_thumbnail_filter_size_full', 'full' ) );
			$img_attr_thumb = wp_get_attachment_image_src( $thumbnail->ID, apply_filters( 'required_wp_feed_post_thumbnail_filter_size_thumbnail', 'thumbnail' ) );
			?>
			<media:content url="<?php echo $img_attr[0]; ?>" type="<?php echo $thumbnail->post_mime_type; ?>" medium="image" width="<?php echo $img_attr[1]; ?>" height="<?php echo $img_attr[2]; ?>">
				<media:title type="plain"><![CDATA[<?php echo apply_filters( 'required_wp_feed_post_thumbnail_filter_title', $thumbnail->post_title ); ?>]]></media:title>
				<media:thumbnail url="<?php echo $img_attr_thumb[0]; ?>" width="<?php echo $img_attr_thumb[1]; ?>" height="<?php echo $img_attr_thumb[2]; ?>" />
				<?php if ( $options && array_key_exists( 'description', $options ) ) : ?>
					<media:description type="plain"><![CDATA[<?php echo apply_filters( 'required_wp_feed_post_thumbnail_filter_description', $thumbnail->post_content ); ?>]]></media:description>
				<?php endif; ?>
				<?php if ( $options && array_key_exists( 'author', $options ) ) : ?>
					<media:copyright><?php echo apply_filters( 'required_wp_feed_post_thumbnail_filter_author', get_the_author( $thumbnail->ID ) ); ?></media:copyright>
				<?php endif; ?>
			</media:content>
		<?php
		}
	}

}
