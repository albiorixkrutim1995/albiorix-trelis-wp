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
class Trelis_Crypto_Payments_Admin {

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
	 * The id of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $id    The id of this plugin.
	 */
	private $id;

	/**
	 * The logo icon of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $trelisIcon    The logo of this plugin.
	 */
	private $trelisIcon;


	/**
	 * The supported types 
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $pluginSupports   
	 */
	private $pluginSupports;


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
		 * defined in Trelis_Crypto_Payments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Trelis_Crypto_Payments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/trelis-crypto-payments-admin.css', array(), $this->version, 'all' );

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
		 * defined in Trelis_Crypto_Payments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Trelis_Crypto_Payments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/trelis-crypto-payments-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Include the Trelis Gateway option to the woocommerce Payments
	 */
	public function trelis_add_gateway_class( $gateways )
    {
        $gateways[] = 'WC_Trelis_Gateway';
        return $gateways;
    }

	/**
	 * Include USD Coin(USDC) & Ehterium (ETH) currency to the currency dropdown
	 */
	public function trelis_add_crypto( $currencies ) 
	{	
		$currencies['ETH'] = __( 'ETH', 'woocommerce' );
		$currencies['USDC'] = __( 'USDC', 'woocommerce' );
		return $currencies;
	}

	/**
	 * Include USD Coin(USDC) & Ehterium (ETH) currency symbole
	 */
	public function trelis_add_currency_symbols($currency_symbol, $currency)
	{
		switch( $currency ) {
			case 'ETH': $currency_symbol = 'ETH'; break;
			case 'USDC': $currency_symbol = 'USDC'; break;
		}
		return $currency_symbol;
	}

	/**
	 * Include treslis payment gateway class
	 */
	public function trelis_init_gateway_class() {
		if($this->is_woocommer_plugin_active())
		{
			require_once plugin_dir_path( __FILE__ ) . 'class-wc-trelis-gateway.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-wc-trelis-rest-api.php';
		}
	}

	/**
	 * Checks if the WooCommerce plugin is active.
	 *
	 * @return bool Whether the plugin is active or not.
	 */
	public function is_woocommer_plugin_active() {
		return class_exists( 'WC_Payment_Gateway' );
	}
}
