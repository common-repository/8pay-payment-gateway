<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_8Pay_API_WooCommerce class.
 *
 * @extends WC_Gateway_8Pay
 */
class WC_8Pay_API_WooCommerce extends WC_8Pay_API {

	/**
	 * Create a short url.
	 *
	 * @param object  $params
	 *
	 * @throws WC_8Pay_Invalid_AccessToken_Exception if access token is missing or invalid
	 * @throws WC_8Pay_BadRequest_Exception if params are invalid.
	 * @return object
	 */
	public static function create_short_url ( $chain, $data ) {
		$url = self::get_base_url( $chain ) . '/woocommerce/short-urls';
		$args = [
			'headers' => self::get_headers(),
			'body'    => json_encode( $data )
		];
		$response = wp_remote_post( $url, $args );

		$status_code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$short_url = json_decode( $body );

		if ( $status_code == 200 ) {
			return $short_url;
		}

		if ( $status_code == 400 ) {
			$error_msg = isset( $short_url->error->message ) ? $short_url->error->message : 'Bad Request';
			throw new WC_8Pay_BadRequest_Exception( $error_msg );
		}

		if ( $status_code == 401 ) {
			throw new WC_8Pay_Invalid_AccessToken_Exception( 'Missing or invalid access token' );
		}

		throw new Exception( 'Un unkown error occurred' );
	}
}
