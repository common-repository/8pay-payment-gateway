<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_8Pay_Chain class.
 *
 */
class WC_8Pay_Chain {

	const BSC      = 'bsc';
	const ETHEREUM = 'ethereum';
	const POLYGON  = 'polygon';
	const SANDBOX  = 'sandbox';
	const PRIVATE  = 'private';

	private static $names = [
		self::BSC      => 'BNB Chain',
		self::ETHEREUM => 'Ethereum',
		self::POLYGON  => 'Polygon',
		self::SANDBOX  => 'BNB Chain (Testnet)',
		self::PRIVATE  => 'Private',
	];

	static function list() {
		$rc = new ReflectionClass(__CLASS__);
		return $rc->getConstants();
	}

	static function name($code) {
		return self::$names[$code];
	}
}
