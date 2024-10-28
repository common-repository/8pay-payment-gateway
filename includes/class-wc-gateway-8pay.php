<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Gateway_8Pay class.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Gateway_8Pay extends WC_Payment_Gateway {

	const CHECKOUT_PRODUCTION = 'https://checkout.8pay.network';
	const CHECKOUT_DEVELOPMENT = 'https://checkout.8pay.tech';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id                 = '8pay';
		$this->icon               = '">Crypto <a href="http://8pay.network" target="_blank" style="margin-left: 12px"><img src="https://cdn.8pay.network/img/logo-full.svg"></a><span data="';
		$this->has_fields         = true;
		$this->method_title       = '8Pay';
		$this->method_description = 'Redirects customers to 8Pay checkout page to complete their payment.';
		$this->supports           = [
			'products',
			'subscriptions',
			'subscription_cancellation',
			'gateway_scheduled_payments',
		];

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Get setting values.
		$this->enabled     = $this->get_option( 'enabled' );
		$this->test_mode   = $this->get_option( 'enabled' );
		$this->description = $this->get_option( 'description' );
		$this->api_key     = $this->get_option( 'api_key' );
		$this->chain       = $this->get_option( 'chain' );
		$this->receiver    = $this->get_option( 'receiver' );
		$this->tokens      = $this->get_option( 'tokens' );

		WC_8Pay_API::set_api_key( $this->api_key );

		add_action( 'wp_enqueue_scripts', [ $this, 'payment_scripts' ] );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
	}

	/**
	 * Initialise Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = require WC_8PAY_PLUGIN_PATH . '/includes/admin/8pay-settings.php';
	}

	/**
	 * Payment form on checkout page
	 */
	public function payment_fields() {
		$cart = WC()->cart->get_cart();

		$products = array_filter( $cart, function( $e ) {
			$product = $e['data'];
			return $product->is_type( 'simple' );
		});

		$subscriptions = array_filter( $cart, function( $e ) {
			$product = $e['data'];
			return $product->is_type( 'subscription' );
		});

		if ( count( $products ) && count( $subscriptions ) ) {
			echo 'Your cart can\'t contain simple products and subscription products at the same time.';
			return;
		} else if ( count( $subscriptions ) ) {
			if ( count( $subscriptions ) > 1 ) {
				echo 'You can add a maximum of one subscription to the cart';
				return;
			} else {
				$subscription = array_values( $subscriptions )[0];
				if ( $subscription['quantity'] > 1 ) {
					echo 'Please reduce the subscription quantity to 1';
					return;
				}

				$attribute_8pay_plan_ids = $subscription['data']->get_attribute( '8pay_plan_ids' );
				$plan_ids = $attribute_8pay_plan_ids  ? explode( ',', $attribute_8pay_plan_ids ) : [];
				if ( count( $plan_ids ) == 0 ) {
					echo '<p>Crypto payment through 8Pay is not available for this product</p>';
					return;
				}

				$options = [];
				foreach ( $plan_ids as $plan_id ) {
					$found = false;
					foreach ( WC_8Pay_Chain::list() as $chain) {
						if ( $found ) {
							continue;
						}
						try {
							$plan = WC_8Pay_API_FixedRecurring::get_plan( $chain, $plan_id );
							array_push( $options, [
								'chain' => $chain,
								'token' => $plan->token,
								'plan_id' => $plan_id,
							] );
							$found = true;
						} catch( Exception $e ) {
							if (
								! ( $e instanceof WC_8Pay_NotFound_Exception ) &&
								( $this->test_mode == 'no' && $e instanceof WC_8Pay_Invalid_AccessToken_Exception )
							) {
								wc_add_notice( __('Payment error:', 'woothemes') . $e->getMessage(), 'error' );
								return;
							}
						}
					}
				}
			}
		} else {
			$options = array_map( function( $e ) {
				[ $chain, $token ] = explode( '_', $e );
				return [
					'chain' => $chain,
					'token' => $token,
				];
			}, $this->get_option( 'tokens' ) ? $this->get_option( 'tokens' ) : [] );
		}

		$chains = array_reduce( $options, function( $list, $option ) {
			if ( ! in_array( $option['chain'], $list ) ) {
				array_push( $list, $option['chain'] );
			}
			return $list;
		}, []);

		$first_option = count( $options ) ? array_values( $options )[0] : null;
		if ( is_null ( $first_option ) ) {
			echo 'There are no available tokens.';
			return;
		}

		$selected_chain = $first_option['chain'];
		$selected_token = $first_option['token'];

		echo '<input type="hidden" name="8pay_payment_gateway_chain" value="' . $selected_chain . '">';
		echo '<input type="hidden" name="8pay_payment_gateway_token" value="' . $selected_token . '">';
		if ( count( $subscriptions ) ) {
			$selected_plan_id = array_values($options)[0]['plan_id'];
			echo '<input type="hidden" name="8pay_payment_gateway_plan_id" value="' . $selected_plan_id . '">';
		}

		echo '<h6>Select a chain</h6>';
		echo '<div class="button-container-8pay">';
		foreach ( $chains as $chain ) {
			$selected = $chain == $selected_chain ? 'selected' : '';
			echo '<div class="btn-icon btn-chain ' . $selected . '" data-chain="' . $chain . '">';
			echo '  <img class="btn-icon-image" src="' . plugins_url( 'public/images/chains/' . $chain . '.svg', WC_8PAY_MAIN_FILE ) . '">';
			echo '  <span class="btn-icon-title">' . WC_8Pay_Chain::name( $chain ) . '</span>';
			echo '</div>';
		}
		echo '</div>';

		echo '<h6>Select a token</h6>';
		echo '<div class="button-container-8pay">';
		foreach ( $options as $option ) {
			$style = $option['chain'] != $selected_chain ? 'style="display: none;"' : '';
			$selected = $option['chain'] == $selected_chain && $option['token'] == $selected_token ? 'selected' : '';
			echo '<div class="btn-icon btn-token ' . $selected . '" data-chain="' . $option['chain'] . '" data-token="' . $option['token'] . '" ' . $style . '>';
			echo '  <img class="btn-icon-image" src="' . plugins_url( 'public/images/tokens/' . $option['token'] . '.svg', WC_8PAY_MAIN_FILE ) . '">';
			echo '  <span class="btn-icon-title">' . $option['token'] . '</span>';
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	 * Outputs scripts and styles used for 8Pay payment
	 */
	public function payment_scripts() {
		wp_register_script( 'woocommerce_8pay', plugins_url( 'public/js/8pay.js?v=1.1.5', WC_8PAY_MAIN_FILE ), ['jquery'], WC_8PAY_VERSION, true );
		wp_enqueue_script( 'woocommerce_8pay' );

		wp_register_style( '8pay_styles', plugins_url( 'public/css/8pay-styles.css?v=1.1.5', WC_8PAY_MAIN_FILE ), [], WC_8PAY_VERSION );
		wp_enqueue_style( '8pay_styles' );
	}

	/**
	 * Process the payment
	 *
	 * @param int  $order_id Reference.
	 *
	 * @throws Exception If payment will not be accepted.
	 * @return array|void
	 */
	public function process_payment( $order_id ) {
		global $woocommerce;

		$order = wc_get_order( $order_id );
		$order_id = $order->get_id();
		$currency = $order->get_currency();
		$total_amount = floatval( $order->get_total() );

		$chain = $_POST['8pay_payment_gateway_chain'];
		$token = $_POST['8pay_payment_gateway_token'];
		$plan_id = isset( $_POST['8pay_payment_gateway_plan_id'] ) ? $_POST['8pay_payment_gateway_plan_id'] : null;

		$order->add_meta_data( 'chain', $chain, true );
		$order->add_meta_data( 'token', $token, true );

		$extra = [
			'8PayWoocommerceGateway' => 1,
			'orderId' => $order_id,
		];

		if ( $plan_id ) {
			$type = WC_8Pay_BillingModel::FIXED_RECURRING;
			$data = [
				'planId' => $plan_id,
				'params' => [],
			];

			$order->add_meta_data( 'plan_id', $plan_id, true );

			$extra['webhook'] = WC_8Pay_Helper::get_webhook_url();

		} else {
			$amount = WC_8Pay_Rates::convert_fiat_amount( $currency, $total_amount, $chain, $token );

			$type = WC_8Pay_BillingModel::ONE_TIME;
			$data = [
				'params' => [
					'description' => 'Order #' . $order_id,
					'token' => $token,
					'receivers' => [ $this->receiver ],
					'amounts' => [ $amount ],
					'category' => 'Shop',
					'webhook' => WC_8Pay_Helper::get_webhook_url(),
				],
			];

			$order->add_meta_data( 'amount', $amount, true );
			$order->add_meta_data( 'receiver', $this->receiver, true );
		}

		$data['params']['extra'] = $extra;
		$data['params']['callbackSuccess'] = $this->get_return_url( $order );

		try {
			$short_url = WC_8Pay_API_WooCommerce::create_short_url( $chain, [ 'type' => $type, 'data' => $data ] );
			$checkout_domain = $chain == WC_8Pay_Chain::PRIVATE ? self::CHECKOUT_DEVELOPMENT : self::CHECKOUT_PRODUCTION;
			$checkout_url = $checkout_domain . '/' . $short_url->path;
			WC_8Pay_Logger::log( 'Created new short-url: ' . $checkout_url . PHP_EOL . print_r( $short_url, true ) );

			$order->save_meta_data();
			$order->update_status('on-hold', 'Awaiting 8Pay webhook');
			$woocommerce->cart->empty_cart();

			return [
				'result' => 'success',
				'redirect' => $checkout_url,
			];
		} catch( Exception $e ) {
			wc_add_notice( __('Payment error:', 'woothemes') . $e->getMessage(), 'error' );
			return;
		}
	}

	public function admin_options() {
		?>
		<style>
			#woocommerce_8pay_tokens {
				height: 250px;
			}
		</style>
		<h2>8Pay</h2>
		<table class="form-table">
		<?php $this->generate_settings_html(); ?>
		</table>
		<?php
	}

	public function validate_title_field( $key, $value ) {
		if ( ! isset( $value ) || empty( $value ) ) {
			return '8Pay';
		}

		return $value;
	}

	public function validate_api_key_field( $key, $value ) {
		$post_data = $this->get_post_data();

		$test_mode = isset( $post_data['woocommerce_8pay_test_mode'] ) && $post_data['woocommerce_8pay_test_mode'] == 1;

		$info = WC_8Pay_Auth::token_info( $value, $test_mode );
		if ( !isset( $info->token ) ) {
			WC_Admin_Settings::add_error( 'The provided api key is invalid' );
			$value = '';
		}

		return $value;
	}

	public function validate_receiver_field( $key, $value ) {
		if ( !preg_match( '/^(0x)?[0-9a-f]{40}$/i', $value ) ) {
			WC_Admin_Settings::add_error( 'The provided receiver address is invalid' );
			$value = '';
		}

		return $value;
	}
}
