<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_8Pay_API_WebhookNotifications class.
 *
 * @extends WC_Gateway_8Pay
 */
class WC_8Pay_API_WebhookNotifications extends WC_8Pay_API {

	/**
	 * Get a webhook notification.
	 *
	 * @param string  $notification_id
	 *
	 * @throws WC_8Pay_Invalid_AccessToken_Exception if access token is missing or invalid
	 * @throws WC_8Pay_NotFound_Exception if notification does not exist
	 * @return object
	 */
	public static function get_notification ( $chain, $notification_id ) {
		$url = self::get_base_url( $chain ) . '/webhook-notifications/' . $notification_id;
		$args = [
			'headers' => self::get_headers(),
		];
		$response = wp_remote_get( $url, $args );

		$status_code = wp_remote_retrieve_response_code( $response );

		if ( $status_code == 200 ) {
			$body = wp_remote_retrieve_body( $response );
			$notification = json_decode( $body );
			return $notification;
		}

		if ( $status_code == 401 ) {
			throw new WC_8Pay_Invalid_AccessToken_Exception( 'Missing or invalid access token' );
		}

		if ( $status_code == 404 ) {
			throw new WC_8Pay_NotFound_Exception( 'Plan not found' );
		}

		throw new Exception( 'Un unkown error occurred' );
	}
}
