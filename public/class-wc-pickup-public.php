<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://https://www.linkedin.com/in/manoj-jamble-206ba6227/
 * @since      1.0.0
 *
 * @package    Wc_Pickup
 * @subpackage Wc_Pickup/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wc_Pickup
 * @subpackage Wc_Pickup/public
 * @author     Manoj Jamble <manoj.jamble@wisdmlabs.com>
 */
class Wc_Pickup_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc-pickup-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wc-pickup-public.js', array( 'jquery' ), $this->version, false );

	}


	public function wc_pickup_add_date_field($fields) {
		// error_log("wc_pickup_add_date");
		// $fields['billing']['wc_pickup_date'] = [
		// 	'type'        => 'date',
		// 	'label'       => __('Pickup Date', 'woocommerce'),
		// 	'placeholder' => __('Select a date', 'woocommerce'),
		// 	'required'    => true,
		// 	'class'       => ['form-row-wide'],
		// 	'clear'       => true,
		// ];
		unset($fields['shipping']);
		unset($fields['order']);
		
		return $fields;
	}
	
	public function wc_pickup_store_checkout_field($checkout) {
		// Fetch store locations from wp_options
		$stores = get_option('wc_pickup_stores', []);

		// Ensure we have an array
		if (!is_array($stores)) {
			$stores = [];
		}
	
		echo '<div id="wc_pickup_store_field"><h3>' . esc_html__('Pickup Location', 'your-text-domain') . '</h3>';
	
		echo '<p class="form-row form-row-wide">';
		echo '<label for="wc_pickup_store">' . esc_html__('Select Pickup Location', 'your-text-domain') . '</label>';
		echo '<input list="wc_pickup_store_list" name="wc_pickup_store" id="wc_pickup_store" placeholder="' . esc_attr__('Search store...', 'your-text-domain') . '">';
		echo '<datalist id="wc_pickup_store_list">';
		
		foreach ($stores as $store) {
			$store_id = isset($store['id']) ? $store['id'] + 1 : '';
			$store_name = isset($store['name']) ? $store['name'] : 'Unnamed Store';
			$store_address = isset($store['address']) ? $store['address'] : 'Address not available';
	
			echo '<option value="' . esc_attr($store_id . '-'.$store_name . ' - ' . $store_address) . '" >';
		}
	
		echo '</datalist>';
		echo '</p>';
	
		echo '<p class="form-row form-row-wide">';
		echo '<label for="wc_pickup_date">' . esc_html__('Pickup Date', 'your-text-domain') . '</label>';
		echo '<input type="date" name="wc_pickup_date" id="wc_pickup_date">';
		echo '</p>';
	
		echo '</div>';
	}

	public function wc_save_pickup_store_order_meta($order_id) {
		if (!empty($_POST['wc_pickup_store'])) {
			$selected_store = sanitize_text_field($_POST['wc_pickup_store']);

			// Extract store ID from "store_id - store_name - store_address"
			preg_match('/^(\d+)-/', $selected_store, $matches);
			$store_id = isset($matches[1]) ? intval($matches[1]) - 1 : null; // Reverting increment

			if ($store_id !== null) {
				// Fetch store details from stored options
				$stores = get_option('wc_pickup_stores', []);
				$selected_store_data = array_filter($stores, function ($store) use ($store_id) {
					return isset($store['id']) && $store['id'] == $store_id;
				});

				if (!empty($selected_store_data)) {
					$store = reset($selected_store_data);
					update_post_meta($order_id, 'wc_pickup_store_id', $store_id);
					update_post_meta($order_id, 'wc_pickup_store', sanitize_text_field($_POST['wc_pickup_store']));
					update_post_meta($order_id, 'wc_pickup_location', $store['latitude'] . ',' . $store['longitude'] ?? '');
				}
			}
		}
		if (!empty($_POST['wc_pickup_date'])) {
			update_post_meta($order_id, 'wc_pickup_date', sanitize_text_field($_POST['wc_pickup_date']));
		}	
		if (!empty($_POST['billing_email'])) {
			update_post_meta($order_id, 'billing_email', sanitize_text_field($_POST['billing_email']));
		}	
	}

	public function wc_display_pickup_store_admin_order($order) {
		$store = get_post_meta($order->get_id(), 'wc_pickup_store', true);
		$date = get_post_meta($order->get_id(), 'wc_pickup_date', true);
		$pickup_location = get_post_meta($order->get_id(), 'wc_pickup_location', true);
		$url = "https://www.google.com/maps?q=" . $pickup_location;
		
		if ($store) {
			echo '<p><strong>' . __('Pickup Store') . ':</strong> ' . esc_html($store) . '</p>';
		}
		if ($date) {
			echo '<p><strong>' . __('Pickup Date') . ':</strong> ' . esc_html($date) . '</p>';
		}
		if ($pickup_location) {
			echo '<p><strong>' . __('Pickup Location') . ':</strong> <a href="' . esc_url($url) . '" target="_blank">' . esc_html__('View Location') . '</a></p>';
		}

	}

	public function add_pickup_details_to_email($order, $sent_to_admin = false, $plain_text = false, $email = null) {
		$pickup_store = get_post_meta($order->get_id(), 'wc_pickup_store', true);
		$pickup_date = get_post_meta($order->get_id(), 'wc_pickup_date', true);
		$pickup_location = get_post_meta($order->get_id(), 'wc_pickup_location', true);
		$url = "https://www.google.com/maps?q=" . $pickup_location;
		
		if (!empty($pickup_store)) {
			echo '<h3>' . __('Pickup Details', 'woocommerce') . '</h3>';
			echo '<p><strong>Store Name:</strong> ' . esc_html($pickup_store) . '</p>';
			echo '<p><strong>Pickup Date:</strong> ' . esc_html($pickup_date) . '</p>';
			if($pickup_location) {
				echo '<p><strong>Pickup Location:</strong> <a href="' . esc_url($url) . '" target="_blank">' . esc_html("View Location") . '</a></p>'; 
			}
		}
	}

	public function schedule_wc_pickup_reminder($order_id) {
		if (!$order_id) return;
	
		global $wpdb;
		
		// Fetch pickup date
		$pickup_date = get_post_meta($order_id, 'wc_pickup_date', true);
		error_log("__ pickup : " . $pickup_date);
	
		if (!$pickup_date) return; // Exit if no pickup date
	
		// Calculate the reminder time (1 day before pickup at 9 AM)
		$reminder_time = strtotime($pickup_date . ' 10:10:00') - 86400;
		error_log("Reminder time: " . date('Y-m-d H:i:s', $reminder_time) . " for Order ID: " . $order_id);

		// Schedule the reminder if not already scheduled
		$res = wp_next_scheduled('wc_pickup_reminder_event', [$order_id]);
		error_log("Current cron : " . $res);
		if (!wp_next_scheduled('wc_pickup_reminder_event', [$order_id])) {
				
			error_log("Reminder manually scheduled for Order ID: " . $order_id);
			wp_schedule_single_event($reminder_time, 'wc_pickup_reminder_event', [$order_id]);
			error_log("Reminder manually scheduled for Order ID: " . $order_id);

		}
	}

	public function send_wc_pickup_reminder_email($order_id = null) {
		if (!$order_id) {
			error_log("Error: No Order ID provided for pickup reminder.");
			return;
		}
	
		error_log("Processing Order ID for Reminder: " . $order_id);
	
		// Fetch email from post meta
		$email = get_post_meta($order_id, 'billing_email', true);
	
		if (!$email) {
			error_log("Error: No email found for Order ID: " . $order_id);
			return;
		}
	
		// Fetch store details from post meta
		$pickup_store = get_post_meta($order_id, 'wc_pickup_store', true);
		$location = get_post_meta($order_id, 'wc_pickup_location', true);
	
		if (!$pickup_store) {
			error_log("Warning: No pickup store found for Order ID: " . $order_id);
			$pickup_store = "Unknown Store";
		}
	
		if (!$location) {
			error_log("Warning: No location found for Order ID: " . $order_id);
			$map_url = "https://www.google.com/maps"; 
		} else {
			$map_url = "https://www.google.com/maps?q=" . urlencode($location);
		}
		
		// Email Subject & Message
		$subject = "Reminder: Your Order Pickup is Tomorrow!";
		$message = "Hello,\n\n"
			.  "Your order #{$order_id} is scheduled for pickup tomorrow at {$pickup_store}.\n\n"
        	.  "Location: {$map_url}\n\n"
			.  "Thank you!";
	
			if (!function_exists('wp_mail')) {
				require_once ABSPATH . WPINC . '/pluggable.php';
			}
			
		// Send Email
		$sent = wp_mail($email, $subject, $message);
	
		if ($sent) {
			error_log("Reminder Email successfully sent to: " . $email . " " . $sent);
		} else {
			error_log("Error: Failed to send reminder email to: " . $email);
		}
	}
	
	
	
}
