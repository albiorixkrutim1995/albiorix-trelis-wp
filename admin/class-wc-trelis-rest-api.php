<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.Trelis.com
 * @since      1.0.0
 *
 * @package    Trelis_Crypto_Payments
 * @subpackage Trelis_Crypto_Payments/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Trelis_Crypto_Payments
 * @subpackage Trelis_Crypto_Payments/admin
 * @author     Trelis <jalpesh.fullstack10@gmail.com>
 */
class WC_Trelis_Rest_Api extends WC_Payment_Gateway {

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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_trelis_payment' ) );
	}
	
	public function register_trelis_payment()
	{
		register_rest_route(
			'trelis/v3',
			'/payment',
			array(
				'methods' => 'POST',
				'callback' => array( $this, 'trelis_payment_confirmation_callback' ),
				'permission_callback' => '__return_true'
			),
		);
	}

	/*
	* Payment callback Webhook, Used to process the payment callback from the payment gateway
	*/
	public function trelis_payment_confirmation_callback()
	{
		$trelis = WC()->payment_gateways->payment_gateways()['trelis'];
		$json = file_get_contents('php://input');

		$expected_signature = hash_hmac('sha256', $json,  $trelis->get_option('webhook_secret'));
		if ( $expected_signature != $_SERVER["HTTP_SIGNATURE"])
			return __('Failed','trelis-crypto-payments');

		$data = json_decode($json);

		$orders = get_posts( array(
			'post_type' => 'shop_order',
			'posts_per_page' => -1,
			'post_status' => 'any',
			'meta_key'   => '_transaction_id',
			'meta_value' => json_decode(json_encode($data->mechantProductKey)),
		));

		if (empty($orders))
			return __('Failed','trelis-crypto-payments');

		$order_id = $orders[0]->ID;
		$order = wc_get_order($order_id);

		if ($order->get_status() == 'processing' || $order->get_status() == 'complete')
			return __('Already processed','trelis-crypto-payments');

		if ($data->event === "submission.failed" || $data->event === "charge.failed") {
			$order->add_order_note(__('Trelis Payment Failed! Expected amount ','trelis-crypto-payments') . $data->requiredPaymentAmount . __(', attempted ','trelis-crypto-payments') . $data->paidAmount, true);
			$order->save();
			return __('Failed','trelis-crypto-payments');
		}

		if ($data->event !== "charge.success") {
			return __('Pending','trelis-crypto-payments');
		}

		$order->add_order_note(__('Payment complete!','trelis-crypto-payments'), true);
		$order->payment_complete();
		$order->reduce_order_stock();
		return __('Processed!','trelis-crypto-payments');
	}
}


