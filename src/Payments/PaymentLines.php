<?php
/**
 * Payment lines
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Payments
 */

namespace Pronamic\WordPress\Pay\Payments;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Pronamic\WordPress\Money\Money;
use stdClass;

/**
 * Payment lines
 *
 * @author  Remco Tolsma
 * @version 2.0.6
 */
class PaymentLines implements Countable, IteratorAggregate {
	/**
	 * The lines.
	 *
	 * @var array
	 */
	private $lines;

	/**
	 * Constructs and initialize a payment lines object.
	 */
	public function __construct() {
		$this->lines = array();
	}

	/**
	 * Get iterator.
	 *
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator() {
		return new ArrayIterator( $this->lines );
	}

	/**
	 * Add line.
	 *
	 * @param PaymentLine $line The line to add.
	 */
	public function add_line( PaymentLine $line ) {
		$this->lines[] = $line;
	}

	/**
	 * Count lines.
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->lines );
	}

	/**
	 * Calculate the total amount of all lines.
	 *
	 * @return Money
	 */
	public function get_amount() {
		$amount = 0;

		$use_bcmath = extension_loaded( 'bcmath' );

		foreach ( $this->lines as $line ) {
			if ( $use_bcmath ) {
				// Use non-locale aware float value.
				// @link http://php.net/sprintf.
				$line_amount = sprintf( '%F', $line->get_amount() );

				$amount = bcadd( $amount, $line_amount, 8 );
			} else {
				$amount += $line->get_amount();
			}
		}

		return new Money( $amount );
	}

	/**
	 * Get JSON.
	 *
	 * @return array
	 */
	public function get_json() {
		$objects = array_map(
			function( $line ) {
					return $line->get_json();
			},
			$this->lines
		);

		return $objects;
	}

	/**
	 * Create items from object.
	 *
	 * @param mixed $json JSON.
	 * @return Items
	 * @throws InvalidArgumentException Throws invalid argument exception when JSON is not an array.
	 */
	public static function from_json( $json ) {
		if ( ! is_array( $json ) ) {
			throw new InvalidArgumentException( 'JSON value must be an array.' );
		}

		$object = new self();

		$lines = array_map(
			function( $object ) {
					return PaymentLine::from_json( $object );
			},
			$json
		);

		foreach ( $lines as $line ) {
			$object->add_line( $line );
		}

		return $object;
	}

	/**
	 * Create string representation the payment lines.
	 *
	 * @return string
	 */
	public function __toString() {
		$pieces = array_map( 'strval', $this->lines );

		$string = implode( PHP_EOL, $pieces );

		return $string;
	}
}
