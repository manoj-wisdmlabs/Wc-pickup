<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://www.linkedin.com/in/manoj-jamble-206ba6227/
 * @since      1.0.0
 *
 * @package    Wc_Pickup
 * @subpackage Wc_Pickup/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wc_Pickup
 * @subpackage Wc_Pickup/admin
 * @author     Manoj Jamble <manoj.jamble@wisdmlabs.com>
 */
class Wc_Pickup_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Pickup_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Pickup_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc-pickup-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Pickup_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Pickup_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wc-pickup-admin.js', array( 'jquery' ), $this->version, false );

	
	}

	/**
	 * Add menu for Pickup plugin on admin side
	 * @since 1.0.0
	 * @return void
	 */
	public function wc_pickup_menu() {
		add_menu_page(
			__( 'Pickup', 'wc-pickup' ),
			__( 'Pickup', 'wc-pickup' ),
			'manage_options',
			'wc-pickup',
			array( $this, 'wc_pickup_page' ),
			'dashicons-cart',
			56
		);

		// View Stores
		add_submenu_page(
			'wc-pickup',
			__( 'Stores', 'wc-pickup' ),
			__( 'Stores', 'wc-pickup' ),
			'manage_options',
			'wc-pickup',
			array( $this, 'wc_pickup_page' )
		);

		// Add New Store
		add_submenu_page(
			'wc-pickup',
			__( 'Add New Store', 'wc-pickup' ),
			__( 'Add New Store', 'wc-pickup' ),
			'manage_options',
			'wc-pickup-add-new-store',
			array( $this, 'wc_pickup_add_new_store' )
		);
	}

	/**
	 * Display Pickup page
	 * @since 1.0.0
	 * @return void
	 */
	public function wc_pickup_page() {
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wc-pickup-stores.php';
	}

	/**
	 * Display Add New Store page
	 * @since 1.0.0
	 * @return void
	 */
	public function wc_pickup_add_new_store() {
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wc-pickup-add-new-store.php';
	}

	/**
	 * Register rest routes
	 */
	public function wc_pickup_register_rest_route() {
		register_rest_route('wc-pickup/v1', '/stores', [
			'methods'  => 'GET',
			'callback' => [$this, 'wc_pickup_get_stores'],
			'permission_callback' => '__return_true',
		]);

		register_rest_route('wc-pickup/v1', '/store', [
			'methods'  => 'POST',
			'callback' => [$this, 'wc_pickup_add_store'],
			'permission_callback' => '__return_true'
		]);
	}

	public function wc_pickup_get_stores() {
		return rest_ensure_response(get_option('wc_pickup_stores', []));
	}

	public function wc_pickup_add_store(WP_REST_Request $request) {
		// Get JSON request data
		$params = $request->get_json_params();
		
		// Extract store details
		$name = sanitize_text_field($params['name'] ?? '');
		$address = sanitize_textarea_field($params['address'] ?? '');
		$latitude = sanitize_text_field($params['latitude'] ?? '');
		$longitude = sanitize_text_field($params['longitude'] ?? '');

		// Validate required fields
		if (!$name || !$address) {
			return new WP_Error('missing_fields', 'Store name and address are required.', ['status' => 400]);
		}

		// Get existing stores from wp_options
		$stores = get_option('wc_pickup_stores', []);

		// Ensure $stores is an array
		if (!is_array($stores)) {
			$stores = [];
		}

		// Generate a unique ID
		$store_id = count($stores);

		// Add new store to the array
		$stores[] = [
			'id'        => $store_id,
			'name'      => $name,
			'address'   => $address,
			'latitude'  => $latitude,
			'longitude' => $longitude,
			'created_at' => current_time('mysql')
		];

		// Save updated stores list
		update_option('wc_pickup_stores', $stores);

		return rest_ensure_response([
			'success' => true,
			'message' => 'Store added successfully!',
			'store'   => $stores[$store_id]
		]);
	}
}
