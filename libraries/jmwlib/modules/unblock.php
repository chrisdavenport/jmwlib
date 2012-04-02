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
 * MediaWiki unblock request.
 *
 * action=unblock
 * Unblock a user.
 *
 * This module requires read rights.
 * This module requires write rights.
 * This module only accepts POST requests.
 *
 * Parameters:
 *   id             - ID of the block you want to unblock (obtained through list=blocks). Cannot be used together with user
 *   user           - Username, IP address or IP range you want to unblock. Cannot be used together with id
 *   token          - An unblock token previously obtained through the gettoken parameter or prop=info
 *   gettoken       - If set, an unblock token will be returned, and no other action will be taken
 *   reason         - Reason for unblock (optional)
 *
 * Examples:
 *   api.php?action=unblock&id=105
 *   api.php?action=unblock&user=Bob&reason=Sorry%20Bob
 */
class jmwModuleUnblock extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'id',
		'user',
		'token',
		'gettoken',
		'reason',
	);

}