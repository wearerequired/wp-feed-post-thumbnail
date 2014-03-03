<?php
/**
 * Plugin Name.
 *
 * @package   WP_Feed_Post_Thumbnail_Admin
 * @author    Silvan Hagen <silvan@required.ch>
 * @license   GPL-2.0+
 * @link      http://required.ch
 * @copyright 2014 required gmbh
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-required-wp-feed-post-thumbnail.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package WP_Feed_Post_Thumbnail_Admin
 * @author  Silvan Hagen <silvan@required.ch>
 */
class WP_Feed_Post_Thumbnail_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * @TODO:
		 *
		 * - Rename "WP_Feed_Post_Thumbnail" to the name of your initial plugin class
		 *
		 */
		$plugin = WP_Feed_Post_Thumbnail::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-reading.php' ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function add_settings() {
		
		register_setting(
			'reading',                 			 // settings page
			$this->plugin_slug . '_options',     // option name
			array( $this, 'validate_settings' )  // validation callback
		);

		add_settings_field(
			$this->plugin_slug,      													// id
			__( 'Feed Post Thumbnail Settings', 'required-wp-feed-post-thumbnail' ) ,   // setting title
			array( $this, 'render_settings'),    										// display callback
			'reading',                 													// settings page
			'default'                  													// settings section
		);

	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function render_settings() {
		
		$options = get_option( $this->plugin_slug . '_options' );
		
		$description = '';
		$author = '';

		if ( array_key_exists( 'description' , $options ) ) 
			$description = $options['description'];

		if ( array_key_exists( 'author' , $options ) )
			$author = $options['author'];


	?>
		<label for="<?php echo esc_attr( $this->plugin_slug . '_author' ); ?>">
			<input type="checkbox" id="<?php echo esc_attr( $this->plugin_slug . '_author' ); ?>" name="<?php echo esc_attr( $this->plugin_slug . '_options[author]' ); ?>" value="1" <?php checked( 1, $author ); ?>>
			<?php _e( 'Show post-thumbnail author in feed media element', 'required-wp-feed-post-thumbnail' ); ?>
		</label>
		<label for="<?php echo esc_attr( $this->plugin_slug . '_description' ); ?>">
			<input type="checkbox" id="<?php echo esc_attr( $this->plugin_slug . '_description' ); ?>" name="<?php echo esc_attr( $this->plugin_slug . '_options[description]' ); ?>" value="1" <?php checked( 1, $description ); ?>>
			<?php _e( 'Show post-thumbnail description in feed media element', 'required-wp-feed-post-thumbnail' ); ?>
		</label>
	<?php
	}

}
