<?php

class WP_Feed_Post_Thumbnail_Plugin extends WP_Stack_Plugin2 {

	/**
	 * @var self
	 */
	protected static $instance;

	/**
	 * Plugin version.
	 */
	const VERSION = '2.0.0';

	/**
	 * Plugin slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $plugin_slug = 'wp-feed-post-thumbnail';

	/**
	 * Constructs the object, hooks in to `plugins_loaded`.
	 */
	protected function __construct() {
		$this->hook( 'plugins_loaded', 'add_hooks' );
	}

	/**
	 * Adds hooks.
	 */
	public function add_hooks() {
		$this->hook( 'init' );

		// Modify RSS feed
		$this->hook( 'rss2_ns', 'add_feed_namespace' );
		$this->hook( 'rss2_item', 'add_feed_item_media' );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( $this->get_path() . 'wp-feed-post-thumbnail.php' );
		$this->hook( 'plugin_action_links_' . $plugin_basename, 'plugin_action_links' );

		$this->hook( 'admin_init', 'add_settings' );
	}

	/**
	 * Initializes the plugin, registers textdomain, etc.
	 */
	public function init() {
		$this->load_textdomain( 'wp-feed-post-thumbnail', '/languages' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since 2.0.1
	 *
	 * @param array $links Plugin action links.
	 * @return array Modified plugin action links.
	 */
	public function plugin_action_links( array $links ) {
		return array_merge(
			array(
				'settings' => sprintf(
					'<a href="%s">%s</a>',
					admin_url( 'options-reading.php' ),
					__( 'Settings', 'wp-feed-post-thumbnail' )
				),
			),
			$links
		);
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

		if ( ! has_post_thumbnail( $post->ID ) ) {
			return;
		}

		$options = get_option( $this->plugin_slug . '_options', array(
			'author'      => true,
			'description' => true,
		) );

		$thumbnail = get_post( get_post_thumbnail_id( $post->ID ) );
		$thumbnail = apply_filters( 'wp_feed_post_thumbnail_image', $thumbnail );

		if ( ! $thumbnail instanceof WP_Post ) {
			return;
		}

		$img_attr       = wp_get_attachment_image_src( $thumbnail->ID, apply_filters( 'wp_feed_post_thumbnail_image_size_full', 'full' ) );
		$img_attr_thumb = wp_get_attachment_image_src( $thumbnail->ID, apply_filters( 'wp_feed_post_thumbnail_image_size_thumbnail', 'thumbnail' ) );
		?>
		<media:content url="<?php echo esc_url( $img_attr[0] ); ?>" type="<?php echo esc_attr( $thumbnail->post_mime_type ); ?>" medium="image" width="<?php echo absint( $img_attr[1] ); ?>" height="<?php echo absint( $img_attr[2] ); ?>">
			<media:title type="plain">
				<![CDATA[<?php echo apply_filters( 'wp_feed_post_thumbnail_title', $thumbnail->post_title ); ?>]]>
			</media:title>
			<media:thumbnail url="<?php echo esc_url( $img_attr_thumb[0] ); ?>" width="<?php echo absint( $img_attr_thumb[1] ); ?>" height="<?php echo absint( $img_attr_thumb[2] ); ?>" />
			<?php if ( isset( $options['description'] ) && $options['description'] ) : ?>
				<media:description type="plain">
					<![CDATA[<?php echo apply_filters( 'wp_feed_post_thumbnail_description', $thumbnail->post_content ); ?>]]>
				</media:description>
			<?php endif; ?>
			<?php if ( isset( $options['author'] ) && $options['author'] ) : ?>
				<media:copyright>
					<?php echo apply_filters( 'wp_feed_post_thumbnail_author', get_the_author() ); ?>
				</media:copyright>
			<?php endif; ?>
		</media:content>
		<?php
	}

	/**
	 * Add new setting under Settings -> Reading.
	 *
	 * @since 1.0.0
	 */
	public function add_settings() {
		register_setting(
			'reading',
			$this->plugin_slug . '_options',
			array( $this, 'validate_settings' )
		);

		add_settings_field(
			$this->plugin_slug,
			__( 'Feed Post Thumbnail Settings', 'wp-feed-post-thumbnail' ),
			array( $this, 'render_settings' ),
			'reading'
		);
	}

	/**
	 * Render new setting fields.
	 *
	 * @since 1.0.0
	 */
	public function render_settings() {
		$options = (array) get_option( $this->plugin_slug . '_options', array(
			'author'      => 1,
			'description' => 1,
		) );

		$description = '';
		$author      = '';

		if ( array_key_exists( 'description', $options ) ) {
			$description = $options['description'];
		}

		if ( array_key_exists( 'author', $options ) ) {
			$author = $options['author'];
		}

		?>
		<p>
			<label for="<?php echo esc_attr( $this->plugin_slug . '_author' ); ?>">
				<input type="checkbox" id="<?php echo esc_attr( $this->plugin_slug . '_author' ); ?>" name="<?php echo esc_attr( $this->plugin_slug . '_options[author]' ); ?>" value="1" <?php checked( 1, $author ); ?>>
				<?php _e( 'Show author information in the feed media element', 'wp-feed-post-thumbnail' ); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->plugin_slug . '_description' ); ?>">
				<input type="checkbox" id="<?php echo esc_attr( $this->plugin_slug . '_description' ); ?>" name="<?php echo esc_attr( $this->plugin_slug . '_options[description]' ); ?>" value="1" <?php checked( 1, $description ); ?>>
				<?php _e( 'Show description in the feed media element', 'wp-feed-post-thumbnail' ); ?>
			</label>
		</p>
		<p class="description">
			<?php
			printf(
				/* translators: %s: 'media' */
				__( 'Set attributes of the %s element in the feed.', 'wp-feed-post-thumbnail' ),
				'<code>media</code>'
			);
			?>
		</p>
		<?php
	}

	/**
	 * Simple validation of the settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings The changed plugin settings.
	 *
	 * @return array
	 */
	public function validate_settings( $settings ) {
		array_map( 'intval', $settings );

		return $settings;
	}

}
