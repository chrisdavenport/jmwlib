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
 * MediaWiki userrights request.
 *
 * action=userrights
 * Add/remove a user to/from groups
 *
 * This module requires read rights.
 * This module requires write rights.
 * This module only accepts POST requests.
 *
 * Parameters:
 *   user           - User name
 *   add            - Add the user to these groups
 *                    Values (separate with '|'): bot, sysop, bureaucrat, checkuser, reviewer, steward, accountcreator, import, transwiki, ipblock-exempt, oversight, founder, rollbacker, confirmed, autoreviewer, researcher, abusefilter
 *   remove         - Remove the user from these groups
 *                    Values (separate with '|'): bot, sysop, bureaucrat, checkuser, reviewer, steward, accountcreator, import, transwiki, ipblock-exempt, oversight, founder, rollbacker, confirmed, autoreviewer, researcher, abusefilter
 *   token          - A userrights token previously retrieved through list=users
 *   reason         - Reason for the change
 *                    Default:
 *
 *  Example:
 *    api.php?action=userrights&user=FooBot&add=bot&remove=sysop|bureaucrat&token=123ABC
 */
class jmwModuleUserrights extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'user',
		'add',
		'remove',
		'token',
		'reason',
	);

}