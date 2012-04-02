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
 * MediaWiki emailuser request.
 *
 * action=emailuser
 * Email a user.
 *
 * This module requires read rights.
 * This module requires write rights.
 * This module only accepts POST requests.
 *
 * Parameters:
 *   target         - User to send email to
 *   subject        - Subject header
 *   text           - Mail body
 *   token          - A token previously acquired via prop=info
 *   ccme           - Send a copy of this mail to me
 *
 * Example:
 *   api.php?action=emailuser&target=WikiSysop&text=Content
 */
class jmwModuleEmailuser extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'target',
		'subject',
		'text',
		'token',
		'ccme',
	);

}