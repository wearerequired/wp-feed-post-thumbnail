<?php
/**
 * WP Feed Post Thumbnail
 *
 * @package   WP_Feed_Post_Thumbnail_Admin
 * @author    Silvan Hagen <silvan@required.ch>
 * @license   GPL-2.0+
 * @link      http://required.ch
 * @copyright 2015 required gmbh
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-required-wp-feed-post-thumbnail.php`
 *
 * @package WP_Feed_Post_Thumbnail_Admin
 * @author  Silvan Hagen <silvan@required.ch>
 */
class WP_Feed_Post_Thumbnail_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @var WP_Feed_Post_Thumbnail_Admin
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin            = WP_Feed_Post_Thumbnail::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		add_action( 'admin_init', array( $this, 'add_settings' ) );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links Plugin action links.
	 *
	 * @return array
	 */
	public function add_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-reading.php#' . $this->plugin_slug . '_author' ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Feed_Post_Thumbnail_Admin A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Add new setting under Settings -> Reading.
	 *
	 * @since 1.0.0
	 */
	public function add_settings() {
		register_setting(
			'reading',                           // settings page
			$this->plugin_slug . '_options',     // option name
			array( $this, 'validate_settings' )  // validation callback
		);

		add_settings_field(
			$this->plugin_slug,
			__( 'Feed Post Thumbnail Settings', 'required-wp-feed-post-thumbnail' ),
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
		<p></p><label for="<?php echo esc_attr( $this->plugin_slug . '_author' ); ?>">
			<input type="checkbox" id="<?php echo esc_attr( $this->plugin_slug . '_author' ); ?>" name="<?php echo esc_attr( $this->plugin_slug . '_options[author]' ); ?>" value="1" <?php checked( 1, $author ); ?>>
			<?php _e( 'Show <strong>Author</strong> in the feed media element', 'required-wp-feed-post-thumbnail' ); ?>
		</label></p>
		<p></p><label for="<?php echo esc_attr( $this->plugin_slug . '_description' ); ?>">
			<input type="checkbox" id="<?php echo esc_attr( $this->plugin_slug . '_description' ); ?>" name="<?php echo esc_attr( $this->plugin_slug . '_options[description]' ); ?>" value="1" <?php checked( 1, $description ); ?>>
			<?php _e( 'Show <strong>Description</strong> in the feed media element', 'required-wp-feed-post-thumbnail' ); ?>
		</label></p>
        <p class="description"><?php _e( 'Set attributes of the <code>media</code> element in the feed.', 'required-wp-feed-post-thumbnail' ); ?></p>
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