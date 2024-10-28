<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_8Pay_Rates class.
 *
 */
class WC_8Pay_Rates {

	// A map between currency symbols and CoinMarketCap IDs
	const CMC_FIAT_IDS = [
		'USD' => 2781,
		'AUD' => 2782,
		'BRL' => 2783,
		'CAD' => 2784,
		'CHF' => 2785,
		'CLP' => 2786,
		'CNY' => 2787,
		'CZK' => 2788,
		'DKK' => 2789,
		'EUR' => 2790,
		'GBP' => 2791,
		'HKD' => 2792,
		'HUF' => 2793,
		'IDR' => 2794,
		'ILS' => 2795,
		'INR' => 2796,
		'JPY' => 2797,
		'KRW' => 2798,
		'MXN' => 2799,
		'MYR' => 2800,
		'NOK' => 2801,
		'NZD' => 2802,
		'PHP' => 2803,
		'PKR' => 2804,
		'PLN' => 2805,
		'RUB' => 2806,
		'SEK' => 2807,
		'SGD' => 2808,
		'THB' => 2809,
		'TRY' => 2810,
		'TWD' => 2811,
		'ZAR' => 2812,
		'AED' => 2813,
		'BGN' => 2814,
		'HRK' => 2815,
		'MUR' => 2816,
		'RON' => 2817,
		'ISK' => 2818,
		'NGN' => 2819,
		'COP' => 2820,
		'ARS' => 2821,
		'PEN' => 2822,
		'VND' => 2823,
		'UAH' => 2824,
		'BOB' => 2832,
		'ALL' => 3526,
		'AMD' => 3527,
		'AZN' => 3528,
		'BAM' => 3529,
		'BDT' => 3530,
		'BHD' => 3531,
		'BMD' => 3532,
		'BYN' => 3533,
		'CRC' => 3534,
		'CUP' => 3535,
		'DOP' => 3536,
		'DZD' => 3537,
		'EGP' => 3538,
		'GEL' => 3539,
		'GHS' => 3540,
		'GTQ' => 3541,
		'HNL' => 3542,
		'IQD' => 3543,
		'IRR' => 3544,
		'JMD' => 3545,
		'JOD' => 3546,
		'KES' => 3547,
		'KGS' => 3548,
		'KHR' => 3549,
		'KWD' => 3550,
		'KZT' => 3551,
		'LBP' => 3552,
		'LKR' => 3553,
		'MAD' => 3554,
		'MDL' => 3555,
		'MKD' => 3556,
		'MMK' => 3557,
		'MNT' => 3558,
		'NAD' => 3559,
		'NIO' => 3560,
		'NPR' => 3561,
		'OMR' => 3562,
		'PAB' => 3563,
		'QAR' => 3564,
		'RSD' => 3565,
		'SAR' => 3566,
		'SSP' => 3567,
		'TND' => 3568,
		'TTD' => 3569,
		'UGX' => 3570,
		'UYU' => 3571,
		'UZS' => 3572,
		'VES' => 3573,
	];

	const FIAT_CONVERSION_TOLERANCE = 0.1;

	/**
	 * Convert fiat to token
	 *
	 * @param string  $currency_symbol
	 * @param float  $currency_amount
	 * @param string  $token_symbol
	 *
	 * @return float
	 */
	public static function convert_fiat_amount( $currency_symbol, $currency_amount, $chain, $token_symbol ) {
		$token = WC_8Pay_Token::get( $chain, $token_symbol );
		if ( $currency_symbol == 'USD' && $token['is_stable_coin'] ) {
			return $currency_amount;
		} else if ( $currency_symbol == 'BTC' && $token_symbol == 'BTCB' ) {
			return $currency_amount;
		} else if ( array_key_exists( $currency_symbol, self::CMC_FIAT_IDS ) ) {
			$rate = self::get_rate( $token_symbol, $currency_symbol );
		} else {
			throw new Exception( 'Invalid currency' );
		}

		$full_amount = strval( number_format( $currency_amount / $rate, $token['decimals'], '.', '' ) );
		$parts = explode( '.', $full_amount );
		$decimals = $parts[1] ? count( str_split( $parts[1] ) ) : 0;
		if ( !$decimals ) {
			return $full_amount;
		}

		$i = 1;
		while ($i < $decimals) {
			$shorted_amount = substr( $full_amount, 0, count( str_split( $parts[0] ) ) + 1 + $i++ );
			$shorted_amount_value = $shorted_amount * $rate;
			$diff_percentage = ( $currency_amount - $shorted_amount_value ) / $currency_amount * 100;
			if ( $diff_percentage <= self::FIAT_CONVERSION_TOLERANCE ) {
				return $shorted_amount;
			}
		}

		return $full_amount;
	}

	/**
	 * Get token rate by currency
	 *
	 * @param string  $token_symbol
	 * @param string  $currency_symbol
	 *
	 * @return float
	 */
	public static function get_rate( $token_symbol, $currency_symbol ) {
		$fiat_id = self::CMC_FIAT_IDS[$currency_symbol];
		$url = 'https://web-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest?symbol=' . $token_symbol . '&convert_id=' . $fiat_id;

		$response = wp_remote_get( $url );
		$body     = wp_remote_retrieve_body( $response );

		$quotes = json_decode( $body );

		if ( $quotes->status->error_code ) {
			throw new Exception( 'Error fetching rate: ' . $quotes->status->error_message );
		}

		return $quotes->data->{$token_symbol}->quote->{$fiat_id}->price;
	}
}

new WC_8Pay_Rates();
