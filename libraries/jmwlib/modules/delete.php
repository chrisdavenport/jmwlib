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
 * MediaWiki delete request.
 *
 * action=delete
 * Delete a page.
 *
 * This module requires read rights.
 * This module requires write rights.
 * This module only accepts POST requests.
 *
 * Parameters:
 *   title          - Title of the page you want to delete. Cannot be used together with pageid
 *   pageid         - Page ID of the page you want to delete. Cannot be used together with title
 *   token          - A delete token previously retrieved through prop=info
 *   reason         - Reason for the deletion. If not set, an automatically generated reason will be used.
 *   watch          - Add the page to your watchlist
 *   unwatch        - Remove the page from your watchlist
 *   oldimage       - The name of the old image to delete as provided by iiprop=archivename
 *
 * Examples:
 *   api.php?action=delete&title=Main%20Page&token=123ABC
 *   api.php?action=delete&title=Main%20Page&token=123ABC&reason=Preparing%20for%20move
 */
class jmwModuleDelete extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'title',
		'pageid',
		'token',
		'reason',
		'watch',
		'unwatch',
		'oldimage',
	);

}