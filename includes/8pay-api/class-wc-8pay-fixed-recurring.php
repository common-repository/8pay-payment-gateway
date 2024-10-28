<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_8Pay_API_FixedRecurring class.
 *
 * @extends WC_Gateway_8Pay
 */
class WC_8Pay_API_FixedRecurring extends WC_8Pay_API {

	/**
	 * Get a fixed recurring plan.
	 *
	 * @param string  $plan_id
	 *
	 * @throws WC_8Pay_Invalid_AccessToken_Exception if access token is missing or invalid
	 * @throws WC_8Pay_NotFound_Exception if plan does not exist
	 * @return object
	 */
	public static function get_plan ( $chain, $plan_id ) {
		$url = self::get_base_url( $chain ) . '/fixed-recurring/plans/' . $plan_id;
		$args = [
			'headers' => self::get_headers(),
		];
		$response = wp_remote_get( $url, $args );

		$status_code = wp_remote_retrieve_response_code( $response );

		if ( $status_code == 200 ) {
			$body = wp_remote_retrieve_body( $response );
			$plan = json_decode( $body );
			return $plan;
		}

		if ( $status_code == 401 ) {
			throw new WC_8Pay_Invalid_AccessToken_Exception( 'Missing or invalid access token' );
		}

		if ( $status_code == 404 ) {
			throw new WC_8Pay_NotFound_Exception( 'Plan not found' );
		}


		throw new Exception( 'Un unkown error occurred' );
	}

	/**
	 * Get a fixed recurring subscription.
	 *
	 * @param string  $subscription_id
	 *
	 * @throws WC_8Pay_Invalid_AccessToken_Exception if access token is missing or invalid
	 * @throws WC_8Pay_NotFound_Exception if subscription does not exist
	 * @return object
	 */
	public static function get_subscription ( $chain, $subscription_id ) {
		$url = self::get_base_url( $chain ) . '/fixed-recurring/subscriptions/' . $subscription_id;
		$args = [
			'headers' => self::get_headers(),
		];
		$response = wp_remote_get( $url, $args );

		$status_code = wp_remote_retrieve_response_code( $response );

		if ( $status_code == 200 ) {
			$body = wp_remote_retrieve_body( $response );
			$subscription = json_decode( $body );
			return $subscription;
		}

		if ( $status_code == 401 ) {
			throw new WC_8Pay_Invalid_AccessToken_Exception( 'Missing or invalid access token' );
		}

		if ( $status_code == 404 ) {
			throw new WC_8Pay_NotFound_Exception( 'Subscription not found' );
		}

		throw new Exception( 'Un unkown error occurred' );
	}
}
