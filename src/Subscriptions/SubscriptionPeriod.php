<?php
/**
 * Subscription Period
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Subscriptions
 */

namespace Pronamic\WordPress\Pay\Subscriptions;

use DateTimeInterface;
use Pronamic\WordPress\Money\Money;
use Pronamic\WordPress\DateTime\DateTime;

/**
 * Subscription Period
 *
 * @author  Remco Tolsma
 * @version unreleased
 * @since   unreleased
 */
class SubscriptionPeriod {
	/**
	 * The subscription this period is part of.
	 *
	 * @var Subscription
	 */
	private $subscription;

	/**
	 * The start date of this period.
	 *
	 * @var DateTimeInterface
	 */
	private $start_date;

	/**
	 * The end date of this period.
	 *
	 * @var DateTimeInterface
	 */
	private $end_date;

	/**
	 * The amount to pay for this period.
	 *
	 * @var Amount
	 */
	private $amount;

	/**
	 * Construct and initialize subscription period object.
	 *
	 * @param Subscription      $subscription Subscription.
	 * @param DateTimeInterface $start_date   Start date.
	 * @param DateTimeInterface $end_date     End date.
	 */
	public function __construct( Subscription $subscription, DateTimeInterface $start_date, DateTimeInterface $end_date ) {
		$this->subscription = $subscription;
		$this->start_date   = $start_date;
		$this->end_date     = $end_date;

		$this->amount = clone $subscription->get_total_amount();
	}

	/**
	 * Get subscription.
	 *
	 * @return Subscription
	 */
	public function get_subscription() {
		return $this->subscription;
	}

	/**
	 * Get start date.
	 *
	 * @return DateTimeInterface
	 */
	public function get_start_date() {
		return $this->start_date;
	}

	/**
	 * Get end date.
	 *
	 * @return DateTimeInterface
	 */
	public function get_end_date() {
		return $this->end_date;
	}

	/**
	 * Get amount.
	 *
	 * @return Amount
	 */
	public function get_amount() {
		return $this->amount;
	}
}
