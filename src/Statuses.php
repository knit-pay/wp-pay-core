<?php

namespace Pronamic\WordPress\Pay\Core;

/**
 * Title: WordPress pay statuses constants
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.0
 * @since 1.0.0
 */
class Statuses {
	/**
	 * Status indicator for success
	 *
	 * @var string
	 */
	const SUCCESS = 'Success';

	/**
	 * Status indicator for cancelled
	 *
	 * @var string
	 */
	const CANCELLED = 'Cancelled';

	/**
	 * Status indicator for expired
	 *
	 * @var string
	 */
	const EXPIRED = 'Expired';

	/**
	 * Status indicator for failure
	 *
	 * @var string
	 */
	const FAILURE = 'Failure';

	/**
	 * Status indicator for open
	 *
	 * @var string
	 */
	const OPEN = 'Open';

	/**
	 * Status indicator for active
	 *
	 * @var string
	 */
	const ACTIVE = 'Active';

	/**
	 * Status indicator for completed
	 *
	 * @var string
	 */
	const COMPLETED = 'Completed';
}
