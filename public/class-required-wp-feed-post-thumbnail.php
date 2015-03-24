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
 * Main class used to alter the feed output
 *
 * @package WP_Feed_Post_Thumbnail
 * @author  Silvan Hagen <silvan@required.ch>
 */
class WP_Feed_Post_Thumbnail {
	/**
	 * The plugin's textdomain.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $plugin_slug = 'required-wp-feed-post-thumbnail';

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @var WP_Feed_Post_Thumbnail
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'rss2_ns', array( $this, 'add_feed_namespace' ) );
		add_filter( 'rss2_item', array( $this, 'add_feed_item_media' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Feed_Post_Thumbnail A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin textdomain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
	}

	/**
	 * Add MRSS namespace to feed.
	 *
	 * @since 1.0.0
	 */
	public function add_feed_namespace() {
		echo 'xmlns:media="http://search.yahoo.com/mrss/"';
	}

	/**
	 * Add Media Element to Feed Item
	 *
	 * @since 1.0.0
	 */
	public function add_feed_item_media() {
		global $post;

		$options = get_option( $this->plugin_slug . '_options', array(
			'author'      => 1,
			'description' => 1,
		) );

		$thumbnail = get_post( get_post_thumbnail_id( $post->ID ) );
		$thumbnail = apply_filters( 'required_wp_feed_post_thumbnail_filter', $thumbnail );

		if ( ! $thumbnail ) {
			return;
		}

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