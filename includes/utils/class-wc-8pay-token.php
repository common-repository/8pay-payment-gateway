<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_8Pay_Token class.
 *
 */
class WC_8Pay_Token {

	const TOKENS = [
		WC_8Pay_Chain::BSC => [
			[
				'name' => '8PAY Network v2',
				'symbol' => '8PAY v2',
				'decimals' => 18,
				'address' => '0x6EaDc05928ACd93eFB3FA0DFbC644D96C6Aa1Df8',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Binance Coin',
				'symbol' => 'BNB',
				'decimals' => 18,
				'address' => '0xEeeeeEeeeEeEeeEeEeEeeEEEeeeeEeeeeeeeEEeE',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Wrapped BNB',
				'symbol' => 'WBNB',
				'decimals' => 18,
				'address' => '0xbb4CdB9CBd36B01bD1cBaEBF2De08d9173bc095c',
				'is_stable_coin' => false,
			],
			[
				'name' => 'BUSD Token',
				'symbol' => 'BUSD',
				'decimals' => 18,
				'address' => '0xe9e7CEA3DedcA5984780Bafc599bD69ADd087D56',
				'is_stable_coin' => true,
			],
			[
				'name' => 'Tether USD',
				'symbol' => 'USDT',
				'decimals' => 18,
				'address' => '0x55d398326f99059fF775485246999027B3197955',
				'is_stable_coin' => true,
			],
			[
				'name' => 'USD Coin',
				'symbol' => 'USDC',
				'decimals' => 18,
				'address' => '0x8AC76a51cc950d9822D68b83fE1Ad97B32Cd580d',
				'is_stable_coin' => true,
			],
			[
				'name' => 'Ethereum Token',
				'symbol' => 'ETH',
				'decimals' => 18,
				'address' => '0x2170Ed0880ac9A755fd29B2688956BD959F933F8',
				'is_stable_coin' => false,
			],
			[
				'name' => 'BTCB Token',
				'symbol' => 'BTCB',
				'decimals' => 18,
				'address' => '0x7130d2A12B9BCbFAe4f2634d864A1Ee1Ce3Ead9c',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Matic Token',
				'symbol' => 'MATIC',
				'decimals' => 18,
				'address' => '0xCC42724C6683B7E57334c4E856f4c9965ED682bD',
				'is_stable_coin' => false,
			],
			[
				'name' => 'PancakeSwap Token',
				'symbol' => 'CAKE',
				'decimals' => 18,
				'address' => '0x0E09FaBB73Bd3Ade0a17ECC321fD13a19e81cE82',
				'is_stable_coin' => false,
			],
			[
				'name' => 'BSCPAD.com',
				'symbol' => 'BSCPAD',
				'decimals' => 18,
				'address' => '0x5A3010d4d8D3B5fB49f8B6E57FB9E48063f16700',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Dai Token',
				'symbol' => 'DAI',
				'decimals' => 18,
				'address' => '0x1AF3F329e8BE154074D8769D1FFa4eE058B1DBc3',
				'is_stable_coin' => false,
			],
			[
				'name' => 'CumRocket',
				'symbol' => 'CUMMIES',
				'decimals' => 18,
				'address' => '0x27Ae27110350B98d564b9A3eeD31bAeBc82d878d',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Nafty',
				'symbol' => 'NAFTY',
				'decimals' => 9,
				'address' => '0x2EED4682197834708c0ea8D11D683440Bbe104d1',
				'is_stable_coin' => false,
			],
			[
				'name' => 'HappyFans',
				'symbol' => 'HAPPY',
				'decimals' => 18,
				'address' => '0xF5d8A096CcCb31b9D7bcE5afE812BE23e3D4690d',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Dogecoin',
				'symbol' => 'DOGE',
				'decimals' => 8,
				'address' => '0xbA2aE424d960c26247Dd6c32edC70B295c744C43',
				'is_stable_coin' => false,
			]
		],
		WC_8Pay_Chain::ETHEREUM => [
			[
				'name' => '8PAY Network v2',
				'symbol' => '8PAY v2',
				'decimals' => 18,
				'address' => '0x06DDb3a8BC0aBc14f85e974CF1A93a6f8d4909d9',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Ether',
				'symbol' => 'ETH',
				'decimals' => 18,
				'address' => '0xEeeeeEeeeEeEeeEeEeEeeEEEeeeeEeeeeeeeEEeE',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Wrapped Ether',
				'symbol' => 'WETH',
				'decimals' => 18,
				'address' => '0xC02aaA39b223FE8D0A0e5C4F27eAD9083C756Cc2',
				'is_stable_coin' => false,
			],
			[
				'name' => 'BNB',
				'symbol' => 'BNB',
				'decimals' => 18,
				'address' => '0xB8c77482e45F1F44dE1745F52C74426C631bDD52',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Binance USD',
				'symbol' => 'BUSD',
				'decimals' => 18,
				'address' => '0x4Fabb145d64652a948d72533023f6E7A623C7C53',
				'is_stable_coin' => true,
			],
			[
				'name' => 'Tether USD',
				'symbol' => 'USDT',
				'decimals' => 6,
				'address' => '0xdAC17F958D2ee523a2206206994597C13D831ec7',
				'is_stable_coin' => true,
			],
			[
				'name' => 'USD Coin',
				'symbol' => 'USDC',
				'decimals' => 6,
				'address' => '0xA0b86991c6218b36c1d19D4a2e9Eb0cE3606eB48',
				'is_stable_coin' => true,
			],
			[
				'name' => 'Wrapped BTC',
				'symbol' => 'WBTC',
				'decimals' => 8,
				'address' => '0x2260FAC5E5542a773Aa44fBCfeDf7C193bc2C599',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Dai Stablecoin',
				'symbol' => 'DAI',
				'decimals' => 18,
				'address' => '0x6B175474E89094C44Da98b954EedeAC495271d0F',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Matic Token',
				'symbol' => 'MATIC',
				'decimals' => 18,
				'address' => '0x7D1AfA7B718fb893dB30A3aBc0Cfc608AaCfeBB0',
				'is_stable_coin' => false,
			]
		],
		WC_8Pay_Chain::POLYGON => [
			[
				'name' => '8PAY Network v2',
				'symbol' => '8PAY v2',
				'decimals' => 18,
				'address' => '0x06DDb3a8BC0aBc14f85e974CF1A93a6f8d4909d9',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Polygon',
				'symbol' => 'MATIC',
				'decimals' => 18,
				'address' => '0xEeeeeEeeeEeEeeEeEeEeeEEEeeeeEeeeeeeeEEeE',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Wrapped Matic',
				'symbol' => 'WMATIC',
				'decimals' => 18,
				'address' => '0x0d500b1d8e8ef31e21c99d1db9a6444d3adf1270',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Tether USD',
				'symbol' => 'USDT',
				'decimals' => 6,
				'address' => '0xc2132D05D31c914a87C6611C10748AEb04B58e8F',
				'is_stable_coin' => true,
			],
			[
				'name' => 'USD Coin',
				'symbol' => 'USDC',
				'decimals' => 6,
				'address' => '0x2791Bca1f2de4661ED88A30C99A7a9449Aa84174',
				'is_stable_coin' => true,
			],
			[
				'name' => 'Binance Token',
				'symbol' => 'BNB',
				'decimals' => 18,
				'address' => '0x5c4b7CCBF908E64F32e12c6650ec0C96d717f03F',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Wrapped Ether',
				'symbol' => 'WETH',
				'decimals' => 18,
				'address' => '0x7ceB23fD6bC0adD59E62ac25578270cFf1b9f619',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Wrapped BTC',
				'symbol' => 'WBTC',
				'decimals' => 8,
				'address' => '0x1BFD67037B42Cf73acF2047067bd4F2C47D9BfD6',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Dai Stablecoin',
				'symbol' => 'DAI',
				'decimals' => 18,
				'address' => '0x8f3Cf7ad23Cd3CaDbD9735AFf958023239c6A063',
				'is_stable_coin' => false,
			]
		],
		WC_8Pay_Chain::SANDBOX => [
			[
				'name' => '8PAY Network',
				'symbol' => '8PAY',
				'decimals' => 18,
				'address' => '0xEDF5b665A22E135678793c79a4E4ABd54C1863Bd',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Binance Coin',
				'symbol' => 'BNB',
				'decimals' => 18,
				'address' => '0xEeeeeEeeeEeEeeEeEeEeeEEEeeeeEeeeeeeeEEeE',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Wrapped BNB',
				'symbol' => 'WBNB',
				'decimals' => 18,
				'address' => '0xae13d989dac2f0debff460ac112a837c89baa7cd',
				'is_stable_coin' => false,
			],
			[
				'name' => 'BUSD Token',
				'symbol' => 'BUSD',
				'decimals' => 18,
				'address' => '0xed24fc36d5ee211ea25a80239fb8c4cfd80f12ee',
				'is_stable_coin' => true,
			],
			[
				'name' => 'Tether USD',
				'symbol' => 'USDT',
				'decimals' => 18,
				'address' => '0x337610d27c682e347c9cd60bd4b3b107c9d34ddd',
				'is_stable_coin' => true,
			],
			[
				'name' => 'Ethereum Token',
				'symbol' => 'ETH',
				'decimals' => 18,
				'address' => '0xd66c6b4f0be8ce5b39d52e0fd1344c389929b378',
				'is_stable_coin' => false,
			],
			[
				'name' => 'BTCB Token',
				'symbol' => 'BTCB',
				'decimals' => 18,
				'address' => '0x6ce8da28e2f864420840cf74474eff5fd80e65b8',
				'is_stable_coin' => false,
			]
		],
		WC_8Pay_Chain::PRIVATE => [
			[
				'name' => '8PAY Network',
				'symbol' => '8PAY',
				'decimals' => 18,
				'address' => '0xaF7e5080C403b26f66F55eba3e15a6669cc20e3e',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Binance Coin',
				'symbol' => 'BNB',
				'decimals' => 18,
				'address' => '0xEeeeeEeeeEeEeeEeEeEeeEEEeeeeEeeeeeeeEEeE',
				'is_stable_coin' => false,
			],
			[
				'name' => 'Wrapped BNB',
				'symbol' => 'WBNB',
				'decimals' => 18,
				'address' => '0xae13d989dac2f0debff460ac112a837c89baa7cd',
				'is_stable_coin' => false,
			],
			[
				'name' => 'BUSD Token',
				'symbol' => 'BUSD',
				'decimals' => 18,
				'address' => '0xed24fc36d5ee211ea25a80239fb8c4cfd80f12ee',
				'is_stable_coin' => true,
			],
			[
				'name' => 'Tether USD',
				'symbol' => 'USDT',
				'decimals' => 18,
				'address' => '0x337610d27c682e347c9cd60bd4b3b107c9d34ddd',
				'is_stable_coin' => true,
			],
			[
				'name' => 'Ethereum Token',
				'symbol' => 'ETH',
				'decimals' => 18,
				'address' => '0xd66c6b4f0be8ce5b39d52e0fd1344c389929b378',
				'is_stable_coin' => false,
			],
			[
				'name' => 'BTCB Token',
				'symbol' => 'BTCB',
				'decimals' => 18,
				'address' => '0x6ce8da28e2f864420840cf74474eff5fd80e65b8',
				'is_stable_coin' => false,
			]
		],
	];

	static function list() {
		return self::TOKENS;
	}

	static function get( $chain, $token ) {
		$filtered_tokens = array_filter( self::TOKENS[$chain], function( $e ) use ( $token ) {
			return $e['symbol'] == $token || $e['address'] == $token;
		});
		return array_shift($filtered_tokens);
	}
}
