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
 * MediaWiki watch request.
 *
 * action=watch
 * Add or remove a page from/to the current user's watchlist
 *
 * This module requires read rights.
 * This module requires write rights.
 *
 * Parameters:
 *   title          - The page to (un)watch
 *   unwatch        - If set the page will be unwatched rather than watched
 *
 * Examples:
 *   api.php?action=watch&title=Main_Page
 *   api.php?action=watch&title=Main_Page&unwatch
 */
class jmwModuleWatch extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'title',
		'unwatch',
	);

}