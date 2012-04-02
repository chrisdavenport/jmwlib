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
 * MediaWiki patrol request.
 *
 * action=patrol
 * Patrol a page or revision.
 *
 * This module requires read rights.
 * This module requires write rights.
 *
 * Parameters:
 *   token          - Patrol token obtained from list=recentchanges
 *   rcid           - Recentchanges ID to patrol
 *
 * Example:
 *   api.php?action=patrol&token=123abc&rcid=230672766
 */
class jmwModulePatrol extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'token',
		'rcid',
	);

}