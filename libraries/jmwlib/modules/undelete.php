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
 * MediaWiki undelete request.
 *
 * action=undelete
 * Restore certain revisions of a deleted page. A list of deleted revisions (including timestamps) can be
 * retrieved through list=deletedrevs
 *
 * This module requires read rights.
 * This module requires write rights.
 * This module only accepts POST requests.
 *
 * Parameters:
 *   title          - Title of the page you want to restore.
 *   token          - An undelete token previously retrieved through list=deletedrevs
 *   reason         - Reason for restoring (optional)
 *                    Default:
 *   timestamps     - Timestamps of the revisions to restore. If not set, all revisions will be restored.
 *
 * Examples:
 *   api.php?action=undelete&title=Main%20Page&token=123ABC&reason=Restoring%20main%20page
 *   api.php?action=undelete&title=Main%20Page&token=123ABC&timestamps=20070703220045|20070702194856
 */
class jmwModuleUndelete extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'title',
		'token',
		'reason',
		'timestamps',
	);

}