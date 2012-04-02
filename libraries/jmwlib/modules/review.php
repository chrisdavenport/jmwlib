<?php
/**
 * @version		$Id:$
 * @package		jmwlib
 * @copyright	Copyright (C) 2010 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * MediaWiki review request.
 *
 * action=review *
 * Review a revision via FlaggedRevs.
 *
 * This module requires read rights.
 * This module requires write rights.
 * This module only accepts POST requests.
 *
 * Parameters:
 *   revid          - The revision ID for which to set the flags
 *   token          - An edit token retrieved through prop=info
 *   comment        - Comment for the review (optional)
 *   flag_status    - Set the flag ''status'' to the specified value
 *                    One value: 0, 1
 *                    Default: 1
 *
 * Example:
 *   api.php?action=review&revid=12345&token=123AB&flag_accuracy=1&comment=Ok
 */
class jmwModuleReview extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'revid',
		'token',
		'comment',
		'flag_status',
	);

}