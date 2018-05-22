<?php

namespace Pronamic\WordPress\Pay\Core;

use Pronamic\WordPress\Pay\Plugin;
use WP_Query;

/**
 * Title: WordPress pay payment methods
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.0
 * @since 1.0.1
 */
class PaymentMethods {
	/**
	 * Alipay
	 *
	 * @var string
	 * @since 2.0.0
	 */
	const ALIPAY = 'alipay';

	/**
	 * Bancontact
	 *
	 * @var string
	 * @since 1.3.7
	 */
	const BANCONTACT = 'bancontact';

	/**
	 * Bank transfer
	 *
	 * @var string
	 */
	const BANK_TRANSFER = 'bank_transfer';

	/**
	 * Constant for the Belfius Direct Net method.
	 *
	 * @since 1.3.11
	 * @var string
	 */
	const BELFIUS = 'belfius';

	/**
	 * Bitcoin
	 *
	 * @since 1.3.9
	 * @var string
	 */
	const BITCOIN = 'bitcoin';

	/**
	 * Bunq
	 *
	 * @see https://www.sisow.nl/news/00009
	 * @see https://plugins.trac.wordpress.org/browser/sisow-for-woocommerce/tags/4.7.2/includes/classes/Sisow/Gateway/Bunq.php
	 * @since 1.3.13
	 * @var string
	 */
	const BUNQ = 'bunq';

	/**
	 * Credit Card
	 *
	 * @var string
	 */
	const CREDIT_CARD = 'credit_card';

	/**
	 * Direct Debit
	 *
	 * @var string
	 */
	const DIRECT_DEBIT = 'direct_debit';

	/**
	 * Constant for the Direct Debit mandate via Bancontact payment method.
	 *
	 * @var string
	 * @since 1.3.13
	 */
	const DIRECT_DEBIT_BANCONTACT = 'direct_debit_bancontact';

	/**
	 * Constant for the Direct Debit mandate via Credit Card payment method.
	 *
	 * @var string
	 * @since 1.3.9
	 */
	const DIRECT_DEBIT_CREDIT_CARD = 'direct_debit_credit_card';

	/**
	 * Constant for the Direct Debit mandate via iDEAL payment method.
	 *
	 * @var string
	 * @since 1.3.9
	 */
	const DIRECT_DEBIT_IDEAL = 'direct_debit_ideal';

	/**
	 * Constant for the Direct Debit mandate via SOFORT payment method.
	 *
	 * @var string
	 * @since 1.3.15
	 */
	const DIRECT_DEBIT_SOFORT = 'direct_debit_sofort';

	/**
	 * Constant for the iDEAL payment method.
	 *
	 * @var string
	 */
	const IDEAL = 'ideal';

	/**
	 * Constant for the iDEAL payment method.
	 *
	 * @var string
	 */
	const IDEALQR = 'idealqr';

	/**
	 * Constant for the Giropay payment method.
	 *
	 * @var string
	 */
	const GIROPAY = 'giropay';

	/**
	 * Constant for the Gulden payment method.
	 *
	 * @var string
	 */
	const GULDEN = 'gulden';

	/**
	 * Constant for the KBC/CBC Payment Button method.
	 *
	 * @since 1.3.11
	 * @var string
	 */
	const KBC = 'kbc';

	/**
	 * Constant for the Maestro payment method.
	 *
	 * @var string
	 * @since 1.3.10
	 */
	const MAESTRO = 'maestro';

	/**
	 * MiniTix
	 *
	 * @var string
	 * @deprecated deprecated since version 1.3.1
	 */
	const MINITIX = 'minitix';

	/**
	 * Bancontact/Mister Cash
	 *
	 * @deprecated "Bancontact/Mister Cash" was renamed to just "Bancontact".
	 * @var string
	 */
	const MISTER_CASH = 'mister_cash';

	/**
	 * Constant for the Payconiq method.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	const PAYCONIQ = 'payconiq';

	/**
	 * PayPal
	 *
	 * @var string
	 * @since 1.3.7
	 */
	const PAYPAL = 'paypal';

	/**
	 * SOFORT Banking
	 *
	 * @var string
	 * @since 1.0.1
	 */
	const SOFORT = 'sofort';

	/**
	 * Get payment methods
	 *
	 * @since 1.3.0
	 * @var string
	 * @return array
	 */
	public static function get_payment_methods() {
		$payment_methods = array(
			PaymentMethods::ALIPAY                  => __( 'Alipay', 'pronamic_ideal' ),
			PaymentMethods::BANCONTACT              => __( 'Bancontact', 'pronamic_ideal' ),
			PaymentMethods::BANK_TRANSFER           => __( 'Bank Transfer', 'pronamic_ideal' ),
			PaymentMethods::BELFIUS                 => __( 'Belfius Direct Net', 'pronamic_ideal' ),
			PaymentMethods::BITCOIN                 => __( 'Bitcoin', 'pronamic_ideal' ),
			PaymentMethods::BUNQ                    => __( 'Bunq', 'pronamic_ideal' ),
			PaymentMethods::CREDIT_CARD             => __( 'Credit Card', 'pronamic_ideal' ),
			PaymentMethods::DIRECT_DEBIT            => __( 'Direct Debit', 'pronamic_ideal' ),
			PaymentMethods::DIRECT_DEBIT_BANCONTACT => __( 'Direct Debit mandate via Bancontact', 'pronamic_ideal' ),
			PaymentMethods::DIRECT_DEBIT_IDEAL      => __( 'Direct Debit mandate via iDEAL', 'pronamic_ideal' ),
			PaymentMethods::DIRECT_DEBIT_SOFORT     => __( 'Direct Debit mandate via SOFORT', 'pronamic_ideal' ),
			PaymentMethods::GIROPAY                 => __( 'Giropay', 'pronamic_ideal' ),
			PaymentMethods::GULDEN                  => __( 'Gulden', 'pronamic_ideal' ),
			PaymentMethods::IDEAL                   => __( 'iDEAL', 'pronamic_ideal' ),
			PaymentMethods::IDEALQR                 => __( 'iDEAL QR', 'pronamic_ideal' ),
			PaymentMethods::KBC                     => __( 'KBC/CBC Payment Button', 'pronamic_ideal' ),
			PaymentMethods::PAYCONIQ                => __( 'Payconiq', 'pronamic_ideal' ),
			PaymentMethods::PAYPAL                  => __( 'PayPal', 'pronamic_ideal' ),
			PaymentMethods::SOFORT                  => __( 'SOFORT Banking', 'pronamic_ideal' ),
		);

		return $payment_methods;
	}

	/**
	 * Get payment method name
	 *
	 * @since 1.3.0
	 *
	 * @param null   $method
	 * @param string $default
	 *
	 * @return string
	 */
	public static function get_name( $method = null, $default = null ) {
		$payment_methods = self::get_payment_methods();

		if ( null !== $method && array_key_exists( $method, $payment_methods ) ) {
			return $payment_methods[ $method ];
		}

		if ( null === $default ) {
			return $method;
		}

		return $default;
	}

	/**
	 * Get direct debit methods.
	 *
	 * @since 1.3.14
	 * @return array
	 */
	public static function get_direct_debit_methods() {
		$payment_methods = array(
			PaymentMethods::DIRECT_DEBIT_BANCONTACT => PaymentMethods::BANCONTACT,
			PaymentMethods::DIRECT_DEBIT_IDEAL      => PaymentMethods::IDEAL,
			PaymentMethods::DIRECT_DEBIT_SOFORT     => PaymentMethods::SOFORT,
		);

		return $payment_methods;
	}

	/**
	 * Is direct debit method.
	 *
	 * @since 1.3.14
	 *
	 * @param $payment_method
	 *
	 * @return bool
	 */
	public static function is_direct_debit_method( $payment_method ) {
		return array_key_exists( $payment_method, self::get_direct_debit_methods() );
	}

	/**
	 * Get recurring methods.
	 *
	 * @since 1.3.14
	 * @return array
	 */
	public static function get_recurring_methods() {
		// Get the direct debit methods
		$payment_methods = self::get_direct_debit_methods();

		// Add additional methods suitable for recurring payments
		$payment_methods[ PaymentMethods::CREDIT_CARD ] = PaymentMethods::CREDIT_CARD;

		return $payment_methods;
	}

	/**
	 * Is recurring method.
	 *
	 * @since 1.3.14
	 *
	 * @param $payment_method
	 *
	 * @return bool
	 */
	public static function is_recurring_method( $payment_method ) {
		return array_key_exists( $payment_method, self::get_recurring_methods() );
	}

	/**
	 * Get first method for payment method.
	 *
	 * @param $payment_method
	 *
	 * @return string
	 */
	public static function get_first_payment_method( $payment_method ) {
		if ( self::is_direct_debit_method( $payment_method ) ) {
			$direct_debit_methods = self::get_direct_debit_methods();

			return $direct_debit_methods[ $payment_method ];
		}

		return $payment_method;
	}

	/**
	 * Maybe update active payment methods.
	 *
	 * @return void
	 */
	private static function maybe_update_active_payment_methods() {
		$payment_methods = get_option( 'pronamic_pay_active_payment_methods' );

		// Update active payment methods option if necessary.
		if ( ! is_array( $payment_methods ) ) {
			self::update_active_payment_methods();
		}
	}

	/**
	 * Update active payment methods option.
	 *
	 * @since 2.0.0
	 */
	public static function update_active_payment_methods() {
		$active_payment_methods = array();

		$query = new WP_Query( array(
			'post_type' => 'pronamic_gateway',
			'nopaging'  => true,
			'fields'    => 'ids',
		) );

		foreach ( $query->posts as $config_id ) {
			$gateway = Plugin::get_gateway( $config_id );

			if ( ! $gateway ) {
				continue;
			}

			if ( ! method_exists( $gateway, 'get_supported_payment_methods' ) ) {
				continue;
			}

			$payment_methods = $gateway->get_transient_available_payment_methods();

			foreach ( $payment_methods as $payment_method ) {
				if ( ! isset( $active_payment_methods[ $payment_method ] ) ) {
					$active_payment_methods[ $payment_method ] = array();
				}

				$active_payment_methods[ $payment_method ][] = $config_id;
			}
		}

		update_option( 'pronamic_pay_active_payment_methods', $active_payment_methods );
	}

	/**
	 * Get active payment methods.
	 *
	 * @return array
	 */
	public static function get_active_payment_methods() {
		self::maybe_update_active_payment_methods();

		$payment_methods = array();

		$active_methods = get_option( 'pronamic_pay_active_payment_methods' );

		if ( is_array( $active_methods ) ) {
			$payment_methods = array_keys( $active_methods );
		}

		return $payment_methods;
	}

	/**
	 * Get config IDs for payment method.
	 *
	 * @param string $payment_method Payment method.
	 *
	 * @return array
	 */
	public static function get_config_ids( $payment_method = null ) {
		self::maybe_update_active_payment_methods();

		$config_ids = array();

		$active_methods = get_option( 'pronamic_pay_active_payment_methods' );

		// Make sure active payments methods is an array.
		if ( ! is_array( $active_methods ) ) {
			return $config_ids;
		}

		// Get config IDs for payment method.
		if ( isset( $active_methods[ $payment_method ] ) ) {
			$config_ids = $active_methods[ $payment_method ];
		}

		// Get all config IDs if payment method is empty.
		if ( empty( $payment_method ) ) {
			foreach ( $active_methods as $method_config_ids ) {
				$config_ids = array_merge( $config_ids, $method_config_ids );
			}

			$config_ids = array_unique( $config_ids );
		}

		return $config_ids;
	}

	/**
	 * Check if payment method is active.
	 *
	 * @param string $payment_method Payment method.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public static function is_active( $payment_method = null ) {
		return in_array( $payment_method, self::get_active_payment_methods(), true );
	}
}