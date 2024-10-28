<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_8Pay_WebhookNotificationType class.
 *
 */
class WC_8Pay_WebhookNotificationType {

	/**
	 * The list of 8Pay webhook types.
	 */
	const ONE_TIME           = 'one-time';
	const WALLET_TO_WALLET   = 'wallet-to-wallet';
	const FIXED_RECURRING    = 'fixed-recurring';
	const AUTOBILLER         = 'autobiller';
}
