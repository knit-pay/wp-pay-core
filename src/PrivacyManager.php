<?php
/**
 * Privacy manager
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

namespace Pronamic\WordPress\Pay;

/**
 * Class PrivacyManager
 *
 * @package Pronamic\WordPress\Pay
 */
class PrivacyManager {
	/**
	 * Exporters.
	 *
	 * @var array
	 */
	private $exporters = array();

	/**
	 * Erasers.
	 *
	 * @var array
	 */
	private $erasers = array();

	/**
	 * Privacy manager constructor.
	 */
	public function __construct() {
		// Filters.
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporters' ), 10 );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_erasers' ), 10 );
	}

	/**
	 * Register exporters.
	 *
	 * @param array $exporters Privacy exporters.
	 *
	 * @return array
	 */
	public function register_exporters( $exporters ) {
		do_action( 'pronamic_pay_privacy_register_exporters', $this );

		foreach ( $this->exporters as $id => $exporter ) {
			$exporters[ $id ] = $exporter;
		}

		return $exporters;
	}

	/**
	 * Register erasers.
	 *
	 * @param array $erasers Privacy erasers.
	 *
	 * @return array
	 */
	public function register_erasers( $erasers ) {
		do_action( 'pronamic_pay_privacy_register_erasers', $this );

		foreach ( $this->erasers as $id => $eraser ) {
			$erasers[ $id ] = $eraser;
		}

		return $erasers;
	}

	/**
	 * Add exporter.
	 *
	 * @param string $id       ID of the exporter.
	 * @param string $name     Exporter name.
	 * @param array  $callback Exporter callback.
	 */
	public function add_exporter( $id, $name, $callback ) {
		$id = 'pronamic-pay-' . $id;

		$this->exporters[ $id ] = array(
			'exporter_friendly_name' => $name,
			'callback'               => $callback,
		);
	}

	/**
	 * Add eraser.
	 *
	 * @param string $id       ID of the eraser.
	 * @param string $name     Eraser name.
	 * @param array  $callback Eraser callback.
	 */
	public function add_eraser( $id, $name, $callback ) {
		$id = 'pronamic-pay-' . $id;

		$this->erasers[ $id ] = array(
			'eraser_friendly_name' => $name,
			'callback'             => $callback,
		);
	}

	/**
	 * Export meta.
	 *
	 * @param string $meta_key     Meta key.
	 * @param array  $meta_options Registered meta options.
	 * @param array  $meta_values  Array with all post meta for item.
	 *
	 * @return array
	 */
	public function export_meta( $meta_key, $meta_options, $meta_values ) {
		// Label.
		$label = $meta_key;

		if ( isset( $meta_options['label'] ) ) {
			$label = $meta_options['label'];
		}

		// Meta value.
		$meta_value = $meta_values[ $meta_key ];

		if ( 1 === count( $meta_value ) ) {
			$meta_value = array_shift( $meta_value );
		} else {
			$meta_value = wp_json_encode( $meta_value );
		}

		// Return export data.
		return array(
			'name'  => $label,
			'value' => $meta_value,
		);
	}

	/**
	 * Erase meta.
	 *
	 * @param int    $post_id  ID of the post.
	 * @param string $meta_key Meta key to erase.
	 * @param string $action   Action 'erase' or 'anonymize'.
	 */
	public function erase_meta( $post_id, $meta_key, $action = 'erase' ) {
		switch ( $action ) {
			case 'erase':
				delete_post_meta( $post_id, $meta_key );

				break;
			case 'anonymize':
				$meta_value = get_post_meta( $post_id, $meta_key, true );

				// Mask email addresses.
				if ( false !== strpos( $meta_value, '@' ) ) {
					$meta_value = $this->mask_email( $meta_value );
				}

				update_post_meta( $post_id, $meta_key, $meta_value );

				break;
		}
	}

	/**
	 * Mask email address.
	 *
	 * @param string $email Email address.
	 *
	 * @return string
	 */
	public function mask_email( $email ) {
		// Is this an email address?
		if ( false === strpos( $email, '@' ) ) {
			return $email;
		}

		$parts = explode( '@', $email );

		// Local part.
		$local = $parts[0];

		if ( strlen( $local ) > 2 ) {
			$local = sprintf( '%1$s%2$s%3$s',
				substr( $local, 0, 1 ),
				str_repeat( '*', ( strlen( $local ) - 2 ) ),
				substr( $local, - 1 )
			);
		}

		// Domain part.
		$domain_parts = explode( '.', $parts[1] );

		$domain = array();

		foreach ( $domain_parts as $part ) {
			if ( strlen( $part ) <= 2 ) {
				$domain[] = $part;

				continue;
			}

			$domain[] = sprintf( '%1$s%2$s%3$s',
				substr( $part, 0, 1 ),
				str_repeat( '*', ( strlen( $part ) - 2 ) ),
				substr( $part, - 1 )
			);
		}

		// Combine local and domain part.
		$email = sprintf(
			'%1$s@%2$s',
			$local,
			implode( '.', $domain )
		);

		return $email;
	}
}