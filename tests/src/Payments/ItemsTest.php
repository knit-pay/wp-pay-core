<?php
/**
 * Item test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Payments
 */

namespace Pronamic\WordPress\Pay\Payments;

use WP_UnitTestCase;

/**
 * Items test
 *
 * @author Remco Tolsma
 * @version 1.0
 */
class ItemsTest extends WP_UnitTestCase {
	private $items;

	public function setUp() {
		$this->items = new Items();

		$item_a = new Item();

		$item_a->set_id( '1234' );
		$item_a->set_description( 'Lorem ipsum dolor sit amet.' );
		$item_a->set_quantity( 50 );
		$item_a->set_price( 39.99 );

		$this->items->add_item( $item_a );

		$item_b = new Item();

		$item_b->set_id( '5678' );
		$item_b->set_description( 'Lorem ipsum dolor sit amet.' );
		$item_b->set_quantity( 10 );
		$item_b->set_price( 25 );

		$this->items->add_item( $item_b );

		$item_c = new Item();

		$this->items->add_item( $item_c );
	}

	/**
	 * Test count.
	 */
	public function test_count() {
		$this->assertCount( 3, $this->items );
	}

	/**
	 * Test to string.
	 */
	public function test_to_string() {
		$string = (string) $this->items;

		$expected = '';

		$expected .= '1234 Lorem ipsum dolor sit amet. 50 39.99 1999.50' . PHP_EOL;
		$expected .= '5678 Lorem ipsum dolor sit amet. 10 25.00 250.00' . PHP_EOL;
		$expected .= '  1 0.00 0.00';

		$this->assertEquals( $expected, $string );
	}

	/**
	 * Test JSON.
	 */
	public function test_json() {
		$json_data   = $this->items->get_json();
		$json_string = wp_json_encode( $json_data, JSON_PRETTY_PRINT );

		$expected = file_get_contents( __DIR__ . '/../../json/items.json' );

		$this->assertEquals( $expected, $json_string );
	}

	/**
	 * Test from object.
	 */
	public function test_from_object() {
		$json_string = file_get_contents( __DIR__ . '/../../json/items.json' );

		$object = json_decode( $json_string );

		$items = Items::from_object( $object );

		$this->assertCount( 3, $items );
		$this->assertEquals( $json_string, wp_json_encode( $items->get_json(), JSON_PRETTY_PRINT ) );
	}
}
