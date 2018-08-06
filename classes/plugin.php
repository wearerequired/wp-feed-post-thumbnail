<?php

class WP_Feed_Post_Thumbnail_Plugin {

	/**
	 * Plugin version.
	 */
	const VERSION = '2.1.1';

	/**
	 * Plugin slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $plugin_slug = 'wp-feed-post-thumbnail';

	/**
	 * Adds hooks.
	 */
	public function add_hooks() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Modify RSS feed
		add_action( 'rss2_ns', array( $this, 'add_feed_namespace' ) );
		add_action( 'rss2_item', array( $this, 'add_feed_item_media' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( $this->get_path() . 'wp-feed-post-thumbnail.php' );
		add_action( 'plugin_action_links_' . $plugin_basename, array( $this, 'plugin_action_links' ) );

		add_action( 'admin_init', array( $this, 'add_settings' ) );
	}

	/**
	 * Loads textdomain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wp-feed-post-thumbnail' );
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
		echo 'xmlns:media="http://search.yahoo.com/mrss/" ';
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
				<![CDATA[<?php echo sanitize_text_field( apply_filters( 'wp_feed_post_thumbnail_title', $thumbnail->post_title ) ); ?>]]>
			</media:title>
			<media:thumbnail url="<?php echo esc_url( $img_attr_thumb[0] ); ?>" width="<?php echo absint( $img_attr_thumb[1] ); ?>" height="<?php echo absint( $img_attr_thumb[2] ); ?>" />
			<?php if ( isset( $options['description'] ) && $options['description'] ) : ?>
				<media:description type="plain">
					<![CDATA[<?php echo wp_kses_post( apply_filters( 'wp_feed_post_thumbnail_description', $thumbnail->post_content ) ); ?>]]>
				</media:description>
			<?php endif; ?>
			<?php if ( isset( $options['author'] ) && $options['author'] ) : ?>
				<media:copyright>
					<?php echo esc_html( apply_filters( 'wp_feed_post_thumbnail_author', get_the_author() ) ); ?>
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
			__( 'Feed Post Thumbnail', 'wp-feed-post-thumbnail' ),
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

	/**
	 * Returns the path to the plugin directory.
	 *
	 * @return string The absolute path to the plugin directory.
	 */
	protected function get_path() {
		return plugin_dir_path( __DIR__ );
	}
}
