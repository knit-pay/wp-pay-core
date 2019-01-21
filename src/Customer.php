<?php
/**
 * Contact.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

namespace Pronamic\WordPress\Pay;

use InvalidArgumentException;
use Pronamic\WordPress\DateTime\DateTime;

/**
 * Contact.
 *
 * @author  Reüel van der Steege.
 * @since   2.1.0
 * @version 2.1.0
 */
class Customer {
	/**
	 * Contact name.
	 *
	 * @var ContactName|null
	 */
	private $name;

	/**
	 * Gender.
	 *
	 * @var string|null
	 */
	private $gender;

	/**
	 * Date of birth.
	 *
	 * @var DateTime|null
	 */
	private $birth_date;

	/**
	 * Email address.
	 *
	 * @var string|null
	 */
	private $email;

	/**
	 * Telephone number.
	 *
	 * @var string|null
	 */
	private $phone;

	/**
	 * IP address.
	 *
	 * @var string|null
	 */
	private $ip_address;

	/**
	 * User agent.
	 *
	 * @var string|null
	 */
	private $user_agent;

	/**
	 * Language.
	 *
	 * @var string|null
	 */
	private $language;

	/**
	 * Locale.
	 *
	 * @var string|null
	 */
	private $locale;

	/**
	 * WordPress user ID.
	 *
	 * @var integer|null
	 */
	private $user_id;

	/**
	 * Get contact name.
	 *
	 * @return ContactName|null
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set contact name.
	 *
	 * @param ContactName|null $name Contact name.
	 */
	public function set_name( ContactName $name = null ) {
		$this->name = $name;
	}

	/**
	 * Get gender.
	 *
	 * @return string|null
	 */
	public function get_gender() {
		return $this->gender;
	}

	/**
	 * Set gender.
	 *
	 * @param string $gender Gender.
	 */
	public function set_gender( $gender ) {
		$this->gender = $gender;
	}

	/**
	 * Get birth date.
	 *
	 * @return DateTime|null
	 */
	public function get_birth_date() {
		return $this->birth_date;
	}

	/**
	 * Set birth date.
	 *
	 * @param DateTime $birth_date Date of birth.
	 */
	public function set_birth_date( $birth_date ) {
		$this->birth_date = $birth_date;
	}

	/**
	 * Get email.
	 *
	 * @return string|null
	 */
	public function get_email() {
		return $this->email;
	}

	/**
	 * Set email address.
	 *
	 * @param string $email Email adress.
	 */
	public function set_email( $email ) {
		$this->email = $email;
	}

	/**
	 * Get phone.
	 *
	 * @return string|null
	 */
	public function get_phone() {
		return $this->phone;
	}

	/**
	 * Set phone.
	 *
	 * @param string $phone Telephone number.
	 */
	public function set_phone( $phone ) {
		$this->phone = $phone;
	}

	/**
	 * Get ip address.
	 *
	 * @return string|null
	 */
	public function get_ip_address() {
		return $this->ip_address;
	}

	/**
	 * Set ip address.
	 *
	 * @param string $ip_address IP address.
	 */
	public function set_ip_address( $ip_address ) {
		$this->ip_address = $ip_address;
	}

	/**
	 * Get user agent.
	 *
	 * @return string|null
	 */
	public function get_user_agent() {
		return $this->user_agent;
	}

	/**
	 * Set user agent.
	 *
	 * @param string $user_agent User agent.
	 */
	public function set_user_agent( $user_agent ) {
		$this->user_agent = $user_agent;
	}

	/**
	 * Get language.
	 *
	 * @return string|null
	 */
	public function get_language() {
		return $this->language;
	}

	/**
	 * Set language.
	 *
	 * @param string $language Language.
	 */
	public function set_language( $language ) {
		$this->language = $language;
	}

	/**
	 * Get locale.
	 *
	 * @return string|null
	 */
	public function get_locale() {
		return $this->locale;
	}

	/**
	 * Set locale.
	 *
	 * @param string $locale Locale.
	 */
	public function set_locale( $locale ) {
		$this->locale = $locale;
	}

	/**
	 * Get WordPress user ID.
	 *
	 * @return integer|null
	 */
	public function get_user_id() {
		return $this->user_id;
	}

	/**
	 * Set WordPress user ID.
	 *
	 * @param integer|null $user_id WordPress user ID.
	 */
	public function set_user_id( $user_id ) {
		$this->user_id = $user_id;
	}

	/**
	 * Get JSON.
	 *
	 * @return object|null
	 */
	public function get_json() {
		$data = array(
			'name'       => ( null === $this->get_name() ) ? null : $this->get_name()->get_json(),
			'gender'     => $this->get_gender(),
			'birth_date' => ( null === $this->get_birth_date() ) ? null : $this->get_birth_date()->format( DATE_RFC3339 ),
			'email'      => $this->get_email(),
			'phone'      => $this->get_phone(),
			'ip_address' => $this->get_ip_address(),
			'user_agent' => $this->get_user_agent(),
			'language'   => $this->get_language(),
			'locale'     => $this->get_locale(),
			'user_id'    => $this->get_user_id(),
		);

		$data = array_filter( $data );

		if ( empty( $data ) ) {
			return null;
		}

		return (object) $data;
	}

	/**
	 * Create address from object.
	 *
	 * @param mixed $json JSON.
	 * @return Customer
	 * @throws InvalidArgumentException Throws invalid argument exception when JSON is not an object.
	 */
	public static function from_json( $json ) {
		if ( ! is_object( $json ) ) {
			throw new InvalidArgumentException( 'JSON value must be an array.' );
		}

		$customer = new self();

		foreach ( $json as $key => $value ) {
			$method = sprintf( 'set_%s', $key );

			if ( is_callable( array( $customer, $method ) ) ) {
				if ( 'name' === $key ) {
					$value = ContactName::from_json( $value );
				}

				if ( 'birth_date' === $key ) {
					$value = new DateTime( $value );
				}

				call_user_func( array( $customer, $method ), $value );
			}
		}

		return $customer;
	}

	/**
	 * Create string representation of customer.
	 *
	 * @return string
	 */
	public function __toString() {
		$pieces = array(
			$this->get_name(),
			$this->get_email(),
			$this->get_phone(),
			$this->get_gender(),
			( null === $this->get_birth_date() ) ? null : $this->get_birth_date()->format( DATE_RFC3339 ),
			$this->get_user_agent(),
			$this->get_ip_address(),
			$this->get_language(),
			$this->get_locale(),
		);

		$pieces = array_map( 'strval', $pieces );

		$pieces = array_filter( $pieces );

		$string = implode( PHP_EOL, $pieces );

		return $string;
	}
}
