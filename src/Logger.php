<?php
/**
 * Logger.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

namespace Pronamic\WordPress\Pay;

/**
 * Logger.
 *
 * @author  Reüel van der Steege
 * @version 2.2.4
 * @since   2.2.4
 */
class Logger {
	/**
	 * Log exception.
	 *
	 * @param string $level      Severity level.
	 * @param string $error_code Error code.
	 * @param string $message    Error message.
	 * @param mixed  $data       Additional data.
	 */
	public static function log( $level, $error_code, $message, $data ) {
		$log = self::get_log();

		// Append error.
		$errors = $log->errors;

		$log->errors = array_merge(
			array(
				array(
					'date'       => current_time( 'mysql', true ),
					'level'      => $level,
					'error_code' => $error_code,
					'message'    => $message,
					'data'       => $data,
				),
			),
			$errors
		);

		// Only keep last 50 error messages.
		// @todo keep messages for last 30 days.
		$log->errors = array_slice( $log->errors, 0, 10 );

		// Save JSON encoded error log.
		update_option( 'pronamic_pay_log', wp_json_encode( $log ) );
	}

	/**
	 * Log exception.
	 *
	 * @param PayException $exception Pay exception.
	 */
	public static function log_exception( PayException $exception ) {
		self::log( LogLevel::CRITICAL, $exception->get_error_code(), $exception->get_message(), $exception->get_data() );
	}

	/**
	 * Get log.
	 *
	 * @return array|mixed|object|void
	 */
	public static function get_log() {
		// Retrieve current error log.
		$log = get_option( 'pronamic_pay_log' );

		$log = json_decode( $log );

		if ( null === $log ) {
			$log = (object) array(
				'errors' => array(),
			);
		}

		return $log;
	}
}
