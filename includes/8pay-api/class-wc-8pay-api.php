<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/class-wc-8pay-fixed-recurring.php';
require_once dirname( __FILE__ ) . '/class-wc-8pay-webhook-notifications.php';
require_once dirname( __FILE__ ) . '/class-wc-8pay-woocommerce.php';

/**
 * WC_8Pay_API class.
 *
 */
class WC_8Pay_API {

	// API domains
	const DOMAIN_PRODUCTION  = 'https://api.8pay.network';
	const DOMAIN_DEVELOPMENT = 'https://api.8pay.tech';

	/**
	 * API key
	 *
	 * @var string
	 */
	public static $api_key;

	/**
	 * Set api_key.
	 *
	 * @param string  $api_key
	 *
	 * @return void
	 */
	public static function set_api_key( $api_key ) {
		self::$api_key = $api_key;
	}

	/**
	 * Get base url.
	 *
	 * @return string
	 */
	public static function get_base_url( $chain ) {
		$domain = $chain == WC_8Pay_Chain::PRIVATE ? self::DOMAIN_DEVELOPMENT : self::DOMAIN_PRODUCTION;
		return $domain . '/v1/' . $chain;
	}

	/**
	 * Get headers containing authorization token.
	 *
	 * @return array
	 */
	public static function get_headers() {
		$headers = [
			'Authorization' => 'Bearer ' . self::$api_key,
			'Content-Type'  => 'application/json'
		];

		return $headers;
	}
}
