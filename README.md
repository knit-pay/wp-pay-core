<p align="center">
	<a href="https://www.wp-pay.org/">
		<img src="https://www.wp-pay.org/assets/pronamic-pay.svgo-min.svg" alt="WordPress Pay » Core" width="72" height="72">
	</a>
</p>

<h1 align="center">WordPress Pay » Core</h3>

<p align="center">
	Core components for the WordPress payment processing library.
</p>

## Table of contents

- [Status](#status)
- [WordPress Filters](#wordpress-filters)

## Status

[![Latest Stable Version](https://poser.pugx.org/wp-pay/core/v/stable.svg)](https://packagist.org/packages/wp-pay/core)
[![Total Downloads](https://poser.pugx.org/wp-pay/core/downloads.svg)](https://packagist.org/packages/wp-pay/core)
[![Latest Unstable Version](https://poser.pugx.org/wp-pay/core/v/unstable.svg)](https://packagist.org/packages/wp-pay/core)
[![License](https://poser.pugx.org/wp-pay/core/license.svg)](https://packagist.org/packages/wp-pay/core)
[![Built with Grunt](http://cdn.gruntjs.com/builtwith.svg)](http://gruntjs.com/)

## WordPress Filters

### pronamic_payment_gateway_configuration_id

```php
add_filter(
	'pronamic_payment_gateway_configuration_id',
	/**
	 * Filter the payment gateway configuration ID to use specific 
	 * gateways for certain WooCommerce billing countries.
	 *
	 * @param int     $configuration_id Gateway configuration ID.
	 * @param Payment $payment          The payment resource data.
	 * @return int Gateway configuration ID.
	 */
	function( $configuration_id, $payment ) {
		if ( 'woocommerce' !== $payment->get_source() ) {
			return $configuration_id;
		}

		$billing_address = $payment->get_billing_address();

		if ( null === $billing_address ) {
			return $configuration_id;
		}

		$id = $configuration_id;

		switch ( $billing_address->get_country_code() ) {
			case 'US':
				$id = get_option( 'custom_us_gateway_configuration_id', $id );
				break;
			case 'AU':
				$id = get_option( 'custom_au_gateway_configuration_id', $id );
				break;
		}

		if ( 'pronamic_gateway' === get_post_type( $id ) && 'publish' === get_post_status( $id ) ) {
			$configuration_id = $id;
		}

		return $configuration_id;
	}
);
```
