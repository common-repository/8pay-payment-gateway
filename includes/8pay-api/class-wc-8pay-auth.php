<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_8Pay_Auth class.
 *
 */
class WC_8Pay_Auth {

	// Auth domains
	const DOMAIN_PRODUCTION  = 'https://auth.8pay.network';
	const DOMAIN_DEVELOPMENT = 'https://auth.8pay.tech';

	/**
	 * Get token info.
	 *
	 * @param string  $token
	 * @param bool  $test_mode
	 *
	 * @return object
	 */
	public static function token_info ( $token, $test_mode = false ) {
		$base_url = $test_mode === false ? self::DOMAIN_PRODUCTION : self::DOMAIN_DEVELOPMENT;
		$url = $base_url . '/v1/tokeninfo?token=' . $token;
		$args = [
			'headers' => [ 'Content-Type' => 'application/json' ],
		];
		$response = wp_remote_get( $url, $args );
		$body = wp_remote_retrieve_body( $response );
		$info = json_decode( $body );
		return $info;
	}
}
