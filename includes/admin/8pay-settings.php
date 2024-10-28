<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$test_mode = isset( $_GET['8pay_testmode'] ) && $_GET['8pay_testmode'] == 1;

$token_options = [];
foreach ( WC_8Pay_Token::list() as $chain => $tokens ) {
	if ( $chain == WC_8Pay_Chain::PRIVATE && !$test_mode ) {
		continue;
	}

	$token_options[ WC_8Pay_Chain::name( $chain ) ] = [];
	foreach ($tokens as $token) {
		$key = $chain . '_' . $token['symbol'];
		$token_options[ $key ] = $token['symbol'];
	}
}

$wc_8pay_settings = [
	'enabled' => [
		'title'       => 'Enable/Disable',
		'type'        => 'checkbox',
		'label'       => 'Enable 8Pay',
		'default'     => 'no'
	],
	'description' => [
		'title'       => 'Description',
		'description' => 'Payment method description that the customer will see on your website.',
		'type'        => 'textarea',
		'default'     => 'You will be automatically redirect to 8Pay checkout page to proceed with the payment.',
		'desc_tip'    => true
	],
	'api_key' => [
		'title'       => 'API Key',
		'description' => 'Get your API keys from your 8Pay account.',
		'type'        => 'text',
		'desc_tip'    => true
	],
	'tokens' => [
		'title'       => 'Tokens',
		'description' => 'Hold the CTRL (or CMD) key and click the tokens to choose them.<br>Click any item again to deselect it, e.g. if you have made a mistake.<br>Remember to keep the CTRL (or CMD) key pressed.',
		'type'        => 'multiselect',
		'options'     => $token_options,
		'default'     => [
			'bsc_8PAY',
			'ethereum_8PAY',
			'polygon_8PAY'
		],
	],
	'receiver' => [
		'title'       => 'Receiver',
		'description' => 'Address that will receive the payments.',
		'type'        => 'text',
		'desc_tip'    => true
	],
	'logging'  => [
		'title'       => 'Logging',
		'description' => 'Save debug messages to the WooCommerce System Status Logs',
		'type'        => 'checkbox',
		'label'       => 'Enable log messages',
		'default'     => 'yes',
		'desc_tip'    => true
	],
];

if ( $test_mode ) {
	$wc_8pay_settings['test_mode'] = [
		'title'       => 'Test mode',
		'type'        => 'checkbox',
		'label'       => 'Enable test mode',
		'default'     => 'false',
	];
}

return apply_filters( 'wc_8pay_settings', $wc_8pay_settings );
