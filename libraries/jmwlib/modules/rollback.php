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
 * MediaWiki rollback request.
 *
 * action=rollback
 * Undo the last edit to the page. If the last user who edited the page made multiple edits in a row,
 * they will all be rolled back.
 *
 * This module requires read rights.
 * This module requires write rights.
 * This module only accepts POST requests.
 *
 * Parameters:
 *   title          - Title of the page you want to rollback.
 *   user           - Name of the user whose edits are to be rolled back. If set incorrectly, you'll get a badtoken error.
 *   token          - A rollback token previously retrieved through prop=revisions
 *   summary        - Custom edit summary. If not set, default summary will be used.
 *   markbot        - Mark the reverted edits and the revert as bot edits
 *
 * Examples:
 *   api.php?action=rollback&title=Main%20Page&user=Catrope&token=123ABC
 *   api.php?action=rollback&title=Main%20Page&user=217.121.114.116&token=123ABC&summary=Reverting%20vandalism&markbot=1
 */
class jmwModuleRollback extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'title',
		'user',
		'token',
		'summary',
		'markbot',
	);

}