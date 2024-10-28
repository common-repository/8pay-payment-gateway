<?php
/**
 * Plugin Name:       8Pay Payment Gateway
 * Description:       Take crypto payments on your store using 8Pay.
 * Version:           1.1.5
 * Requires at least: 5.7
 * Requires PHP:      5.6
 * Author:            8Pay
 * Author URI:        https://8pay.network/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Required minimums and constants
 */
define( 'WC_8PAY_VERSION', '1.1.5' );
define( 'WC_8PAY_MIN_PHP_VERSION', '5.6.0' );
define( 'WC_8PAY_MIN_WC_VERSION', '3.0' );
define( 'WC_8PAY_MAIN_FILE', __FILE__ );
define( 'WC_8PAY_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

/**
 * WooCommerce fallback notice.
 */
function woocommerce_8pay_missing_wc_notice() {
	echo '<div class="error"><p><strong>' . sprintf( '8Pay requires WooCommerce to be installed and active. You can download %s here.', '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

/**
 * WooCommerce not supported fallback notice.
 */
function woocommerce_8pay_wc_not_supported() {
	echo '<div class="error"><p><strong>' . sprintf( '8Pay requires WooCommerce %s or greater to be installed and active.', WC_8PAY_MIN_WC_VERSION ) . '</strong></p></div>';
}

function woocommerce_gateway_8pay() {

	static $plugin;

	if ( ! isset( $plugin ) ) {

		class WC_8Pay {

			/**
			 * The *Singleton* instance of this class
			 *
			 * @var Singleton
			 */
			private static $instance;

			/**
			 * Returns the *Singleton* instance of this class.
			 *
			 * @return Singleton The *Singleton* instance.
			 */
			public static function get_instance() {
				if ( ! self::$instance ) {
					self::$instance = new self();
				}
				return self::$instance;
			}

			/**
			 * Private clone method to prevent cloning of the instance of the
			 * *Singleton* instance.
			 *
			 * @return void
			 */
			public function __clone() {}

			/**
			 * Private unserialize method to prevent unserializing of the *Singleton*
			 * instance.
			 *
			 * @return void
			 */
			public function __wakeup() {}

			/**
			 * Protected constructor to prevent creating a new instance of the
			 * *Singleton* via the `new` operator from outside of this class.
			 */
			public function __construct() {
				$this->init();
			}

			/**
			 * Init the plugin after plugins_loaded so environment variables are set.
			 */
			public function init() {
				require_once dirname( __FILE__ ) . '/includes/8pay-api/class-wc-8pay-api.php';
				require_once dirname( __FILE__ ) . '/includes/8pay-api/class-wc-8pay-auth.php';
				require_once dirname( __FILE__ ) . '/includes/exceptions/class-wc-8pay-bad-request-exception.php';
				require_once dirname( __FILE__ ) . '/includes/exceptions/class-wc-8pay-invalid-access-token-exception.php';
				require_once dirname( __FILE__ ) . '/includes/exceptions/class-wc-8pay-not-found-exception.php';
				require_once dirname( __FILE__ ) . '/includes/utils/class-wc-8pay-billing-model.php';
				require_once dirname( __FILE__ ) . '/includes/utils/class-wc-8pay-chain.php';
				require_once dirname( __FILE__ ) . '/includes/utils/class-wc-8pay-token.php';
				require_once dirname( __FILE__ ) . '/includes/utils/class-wc-8pay-webhook-notification-type.php';
				require_once dirname( __FILE__ ) . '/includes/class-wc-8pay-helper.php';
				require_once dirname( __FILE__ ) . '/includes/class-wc-8pay-logger.php';
				require_once dirname( __FILE__ ) . '/includes/class-wc-8pay-rates.php';
				require_once dirname( __FILE__ ) . '/includes/class-wc-8pay-webhook-handler.php';
				require_once dirname( __FILE__ ) . '/includes/class-wc-gateway-8pay.php';

				if ( is_admin() ) {
					require_once dirname( __FILE__ ) . '/includes/admin/class-wc-8pay-admin-notices.php';
				}

				add_filter( 'woocommerce_payment_gateways', [ $this, 'add_gateways' ] );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'plugin_action_links' ] );
				add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );
			}

			/**
			 * Add the gateways to WooCommerce.
			 */
			public function add_gateways( $methods ) {
				$methods[] = 'WC_Gateway_8Pay';

				return $methods;
			}

			/**
			 * Add plugin action links.
			 */
			public function plugin_action_links( $links ) {
				$plugin_links = [
					'<a href="admin.php?page=wc-settings&tab=checkout&section=8pay">Settings</a>',
				];
				return array_merge( $plugin_links, $links );
			}

			/**
			 * Add plugin row meta.
			 */
			public function plugin_row_meta( $links, $file ) {
				if ( plugin_basename( __FILE__ ) === $file ) {
					$row_meta = [
						'docs' => '<a href="https://docs.8pay.network/plugins/woocommerce/" target="_blank" title="View Documentation">Docs</a>',
					];
					return array_merge( $links, $row_meta );
				}
				return (array) $links;
			}
		}

		$plugin = WC_8Pay::get_instance();

	}

	return $plugin;
}

add_action( 'plugins_loaded', 'woocommerce_gateway_8pay_init' );

function woocommerce_gateway_8pay_init() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_8pay_missing_wc_notice' );
		return;
	}

	if ( version_compare( WC_VERSION, WC_8PAY_MIN_WC_VERSION, '<' ) ) {
		add_action( 'admin_notices', 'woocommerce_8pay_wc_not_supported' );
		return;
	}

	woocommerce_gateway_8pay();
}
