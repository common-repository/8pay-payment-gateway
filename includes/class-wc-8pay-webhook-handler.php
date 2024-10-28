<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_8Pay_Webhook_Handler class.
 *
 */
class WC_8Pay_Webhook_Handler {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_api_wc_8pay', [ $this, 'handle_webhook' ] );
	}

	/**
	 * Check incoming requests for 8Pay Webhook data and process them.
	 */
	public function handle_webhook() {
		if ( ! isset( $_SERVER['REQUEST_METHOD'] )
			|| ( 'POST' !== $_SERVER['REQUEST_METHOD'] )
			|| ! isset( $_GET['wc-api'] )
			|| ( 'wc_8pay' !== $_GET['wc-api'] )
		) {
			return;
		}

		try {
			$payload = file_get_contents( 'php://input' );
			$webhook = json_decode( $payload );
			WC_8Pay_Logger::log( 'Received webhook' . PHP_EOL . print_r( $webhook, true ) );

			// Validate it to make sure it is legit.
			$this->verify_integrity( $webhook );

			$this->process_webhook( $webhook );

			status_header( 200 );
			exit;
		} catch ( Exception $e ) {
			WC_8Pay_Logger::log( 'Error processing webhook ' . $webhook->id . ': ' . $e->getMessage() );
			status_header( 500 );
			exit;
		}
	}

	/**
	 * Process webhook.
	 *
	 * @param object $webhook
	 */
	public function process_webhook( $webhook ) {
		switch ( $webhook->type ) {
			case WC_8Pay_WebhookNotificationType::ONE_TIME:
				$this->process_webhook_one_time( $webhook );
				break;

			case WC_8Pay_WebhookNotificationType::WALLET_TO_WALLET:
				$this->process_webhook_wallet_to_wallet( $webhook );
				break;

			case WC_8Pay_WebhookNotificationType::FIXED_RECURRING:
			case WC_8Pay_WebhookNotificationType::AUTOBILLER:
				$this->process_webhook_fixed_recurring( $webhook );
				break;

			default:
				throw new Exception( 'Unhandled webhook type' );
		}
	}

	/**
	 * Process one-time webhook.
	 *
	 * @param object $webhook
	 */
	public function process_webhook_one_time( $webhook ) {
		$order = $this->retrieve_wc_order( $webhook );
		$order_id = $order->get_id();

		$expected_token = $order->get_meta( 'token', true );
		$expected_amount = floatval( $order->get_meta( 'amount', true ) );
		$expected_receiver = $order->get_meta( 'receiver', true );

		$received_token = $webhook->data->token;
		$received_amount = floatval( $webhook->data->receivers[0]->amount );
		$received_receiver = $webhook->data->receivers[0]->account;

		if ( $expected_token != $received_token ) {
			throw new Exception( 'Error: Order #' . $order_id . ' - expected token to be "' . $expected_token . '", received "' . $received_token . '" instead' );
		}

		if ( $expected_amount != $received_amount ) {
			throw new Exception( 'Error: Order #' . $order_id . ' - expected amount to be "' . $expected_amount . '", received "' . $received_amount . '" instead' );
		}

		if ( strtolower( $expected_receiver ) != strtolower( $received_receiver ) ) {
			throw new Exception( 'Error: Order #' . $order_id . ' - expected receiver to be "' . $expected_receiver . '", received "' . $received_receiver . '" instead' );
		}

		$order->payment_complete();
		WC_8Pay_Logger::log( 'Order #' . $order_id . ' completed' );
	}

	/**
	 * Process wallet-to-wallet webhook.
	 *
	 * @param object $webhook
	 */
	public function process_webhook_wallet_to_wallet( $webhook ) {
		$order = $this->retrieve_wc_order( $webhook );
		$order_id = $order->get_id();

		$expected_token = $order->get_meta( 'token', true );
		$expected_amount = floatval( $order->get_meta( 'amount', true ) );
		$expected_receiver = $order->get_meta( 'receiver', true );

		$received_token = $webhook->data->token;
		$received_amount = floatval( $webhook->data->receivers[0]->amount );
		$received_receiver = $webhook->data->receivers[0]->user;

		if ( $expected_token != $received_token ) {
			throw new Exception( 'Error: Order #' . $order_id . ' - expected token to be "' . $expected_token . '", received "' . $received_token . '" instead' );
		}

		if ( $expected_amount != $received_amount ) {
			throw new Exception( 'Error: Order #' . $order_id . ' - expected amount to be "' . $expected_amount . '", received "' . $received_amount . '" instead' );
		}

		if ( $expected_receiver != $received_receiver ) {
			throw new Exception( 'Error: Order #' . $order_id . ' - expected receiver to be "' . $expected_receiver . '", received "' . $received_receiver . '" instead' );
		}

		$order->payment_complete();
		WC_8Pay_Logger::log( 'Order #' . $order_id . ' completed' );
	}

	/**
	 * Process fixed-recurring webhook.
	 *
	 * @param object $webhook
	 */
	public function process_webhook_fixed_recurring( $webhook ) {
		$order = $this->retrieve_wc_order( $webhook );
		$order_id = $order->get_id();

		$expected_plan_id = $order->get_meta( 'plan_id', true );
		$received_plan_id = $webhook->data->planId;

		if ( $expected_plan_id != $received_plan_id ) {
			throw new Exception( 'Error: Order #' . $order_id . ' - expected plan_id to be "' . $expected_plan_id . '", received "' . $received_plan_id . '" instead' );
		}

		$subscriptions = wcs_get_subscriptions_for_order( $order_id );
		$subscription = array_values( $subscriptions )[0];
		$subscription_id = $subscription->get_id();
		$subscription_status = $subscription->get_status();

		$fixed_recurring_subscription = WC_8Pay_API_FixedRecurring::get_subscription( $webhook->chain, $webhook->data->subscriptionId );

		switch ( $webhook->event ) {
			case 'Subscription':
				if ( $fixed_recurring_subscription->subscribedAt != $fixed_recurring_subscription->cycleStart ) {
					return;
				}

				$order->update_status( 'completed' );
				$subscription->update_status( 'active' );

				$dates = [
					'start' => date( 'Y-m-d H:i:s', $fixed_recurring_subscription->subscribedAt ),
					'next_payment' => date( 'Y-m-d H:i:s', $fixed_recurring_subscription->cycleEnd ),
					'last_payment' => date( 'Y-m-d H:i:s', $fixed_recurring_subscription->subscribedAt )
				];
				$subscription->update_dates( $dates, 'utc' );

				WC_8Pay_Logger::log( 'Order #' . $order_id . ' completed' . PHP_EOL . 'Subscription ' . $subscription_id . ' active' . PHP_EOL . 'Start: ' . $dates['start'] . PHP_EOL . 'Next payment: ' . $dates['next_payment'] );
				break;

			case 'Billing':
				if ( $webhook->data->cycleStart != $fixed_recurring_subscription->cycleStart ) {
					return;
				}

				if ( $subscription_status == 'expired' ) {
					$items = $order->get_items();
					$item = array_values( $items )[0];
					$product = $item->get_product( $item );

					$new_subscription = wcs_create_subscription([
						'order_id' => $order->get_id(),
						'status' => 'pending',
						'billing_period' => WC_Subscriptions_Product::get_period( $product ),
						'billing_interval' => WC_Subscriptions_Product::get_interval( $product )
					]);

					$new_subscription->add_product( $product, 1 );

					$dates = [
						'last_payment' => date( 'Y-m-d H:i:s', $webhook->timestamp ),
						'next_payment' => date( 'Y-m-d H:i:s', $fixed_recurring_subscription->cycleEnd ),
					];
					$new_subscription->update_dates( $dates );
					$new_subscription->calculate_totals();

					$order->update_status( 'completed' );
					$new_subscription->update_status( 'active' );
				} else {
					$dates = [
						'last_payment' => date( 'Y-m-d H:i:s', $webhook->timestamp ),
						'next_payment' => date( 'Y-m-d H:i:s', $fixed_recurring_subscription->cycleEnd ),
					];
					$subscription->update_dates( $dates, 'utc' );
				}

				WC_8Pay_Logger::log( 'Order #' . $order_id . ' completed' . PHP_EOL . 'Subscription ' . $subscription_id . ' active' . PHP_EOL . 'Last payment: ' . $dates['last_payment'] . PHP_EOL . 'Next payment: ' . $dates['next_payment'] );
				break;

			case 'BillingFailed':
			case 'BillingAttemptFailed':
				if ( $fixed_recurring_subscription->status == 'EXPIRED' ) {
					$order->update_status( 'failed' );
					$subscription->update_status( 'expired' );
					WC_8Pay_Logger::log( 'Order #' . $order_id . ' failed' . PHP_EOL . 'Subscription ' . $subscription_id . ' expired' );
				}
				break;

			case 'SubscriptionCancelled':
			case 'SubscriptionTerminated':
				if ( in_array( $fixed_recurring_subscription->status, [ 'CANCELLED', 'TERMINATED' ] ) ) {
					$order->update_status( 'cancelled' );
					$subscription->update_status( 'cancelled' );
					WC_8Pay_Logger::log( 'Order #' . $order_id . ' cancelled' . PHP_EOL . 'Subscription ' . $subscription_id . ' cancelled' );
				}
				break;

			default:
				throw new Exception( 'Unhandled event' );
		}
	}

	/**
	 * Verify the incoming webhook to make sure it is legit.
	 *
	 * @param string $payload
	 *
	 * @return bool
	 */
	public function verify_integrity( $webhook_notification ) {
		try {
			$api_notification = WC_8Pay_API_WebhookNotifications::get_notification( $webhook_notification->chain, $webhook_notification->id );

			if ( $webhook_notification != $api_notification ) {
				throw new Exception( 'Mismatch between notifications' );
			}
		} catch ( Exception $e ) {
			throw new Exception( 'Integrity check failed: ' . $e->getMessage() );
		}
	}

	/**
	 * Retrieve WooCommerce order
	 *
	 * @param object $webhook
	 *
	 * @return bool
	 */
	public function retrieve_wc_order( $webhook ) {
		$order_id = isset( $webhook->woocommerce->orderId ) ? $webhook->woocommerce->orderId : null;
		if ( is_null( $order_id ) ) {
			throw new Exception( 'Missing orderId' );
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			throw new Exception( 'Order "' . $order_id . '" not found' );
		}

		return $order;
	}
}

new WC_8Pay_Webhook_Handler();
