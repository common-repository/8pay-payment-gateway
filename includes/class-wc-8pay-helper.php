<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_8Pay_Helper class.
 *
 */
class WC_8Pay_Helper {

	/**
	 * Get webhook url.
	 *
	 * @return string
	 */
	public static function get_webhook_url() {
		return add_query_arg( 'wc-api', 'wc_8pay', trailingslashit( get_home_url() ) );
	}
}
