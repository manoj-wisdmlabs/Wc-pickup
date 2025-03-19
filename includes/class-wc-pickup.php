<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://www.linkedin.com/in/manoj-jamble-206ba6227/
 * @since      1.0.0
 *
 * @package    Wc_Pickup
 * @subpackage Wc_Pickup/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wc_Pickup
 * @subpackage Wc_Pickup/includes
 * @author     Manoj Jamble <manoj.jamble@wisdmlabs.com>
 */
class Wc_Pickup {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wc_Pickup_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WC_PICKUP_VERSION' ) ) {
			$this->version = WC_PICKUP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wc-pickup';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wc_Pickup_Loader. Orchestrates the hooks of the plugin.
	 * - Wc_Pickup_i18n. Defines internationalization functionality.
	 * - Wc_Pickup_Admin. Defines all hooks for the admin area.
	 * - Wc_Pickup_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-pickup-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-pickup-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wc-pickup-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wc-pickup-public.php';

		$this->loader = new Wc_Pickup_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wc_Pickup_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wc_Pickup_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wc_Pickup_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		// add menu
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wc_pickup_menu');

		// Add rest api routes
		$this->loader->add_action( 'rest_api_init', $plugin_admin, 'wc_pickup_register_rest_route');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wc_Pickup_Public( $this->get_plugin_name(), $this->get_version() );
		

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// // 
		// add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
		// 	if (!empty($_POST['wc_pickup_date'])) {
		// 		update_post_meta($order_id, '_wc_pickup_date', sanitize_text_field($_POST['wc_pickup_date']));
		// 	}
		// });

		$this->loader->add_filter( 'woocommerce_checkout_fields', $plugin_public, 'wc_pickup_add_date_field' );
		
		$this->loader->add_action( 'woocommerce_after_order_notes', $plugin_public, 'wc_pickup_store_checkout_field' );
		$this->loader->add_action( 'woocommerce_checkout_update_order_meta', $plugin_public, 'wc_save_pickup_store_order_meta' );
		$this->loader->add_action( 'woocommerce_admin_order_data_after_shipping_address', $plugin_public, 'wc_display_pickup_store_admin_order' );
				// Hook in add_pickup_details_to_email
		$this->loader->add_action( 'woocommerce_email_order_meta', $plugin_public, 'add_pickup_details_to_email' );
		
	
		$this->loader->add_action('wp', $plugin_public, 'schedule_wc_pickup_reminder');
		$this->loader->add_action('woocommerce_thankyou', $plugin_public, 'schedule_wc_pickup_reminder');
		$this->loader->add_action('wc_pickup_reminder_event', $plugin_public, 'send_wc_pickup_reminder_email');
	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wc_Pickup_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
