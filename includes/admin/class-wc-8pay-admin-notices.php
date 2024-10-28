<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_8Pay_Admin_Notices class.
 *
 */
class WC_8Pay_Admin_Notices {

	/**
	 * Notices (array)
	 *
	 * @var array
	 */
	public $notices = [];

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_notices', [ $this, 'admin_notices' ] );
	}

	/**
	 * Display any notices we've collected thus far.
	 */
	public function admin_notices() {
		$this->check_environment();

		foreach ( $this->notices as $notice_key => $notice ) {
			echo '<div class="' . esc_attr( $notice['class'] ) . '">';
			echo '<p>' . esc_html( $notice['message'] ) . '</p>';
			echo '</div>';
		}
	}

	/**
	 * The sanity check, in case the plugin is activated in a weird way,
	 * or the environment changes after activation.
	 */
	public function check_environment() {
		if ( version_compare( phpversion(), WC_8PAY_MIN_PHP_VERSION, '<' ) ) {
			$message = sprintf( '8Pay Payment Gateway - The minimum PHP version required for this plugin is %1$s. You are running %2$s.', WC_8PAY_MIN_PHP_VERSION, phpversion() );

			$this->add_admin_notice( 'php-version', 'notice notice-error', $message );

			return;
		}
	}

	/**
	 * Allow this class and other classes to add slug keyed notices (to avoid duplication).
	 */
	public function add_admin_notice( $notice_key, $class, $message ) {
		$this->notices[ $notice_key ] = [
			'class'   => $class,
			'message' => $message,
		];
	}
}

new WC_8Pay_Admin_Notices();
