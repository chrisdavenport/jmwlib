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
 * MediaWiki move request.
 *
 * action=move
 * Move a page.
 *
 * This module requires read rights.
 * This module requires write rights.
 * This module only accepts POST requests.
 *
 * Parameters:
 *   from           - Title of the page you want to move. Cannot be used together with fromid.
 *   fromid         - Page ID of the page you want to move. Cannot be used together with from.
 *   to             - Title you want to rename the page to.
 *   token          - A move token previously retrieved through prop=info
 *   reason         - Reason for the move (optional).
 *   movetalk       - Move the talk page, if it exists.
 *   movesubpages   - Move subpages, if applicable
 *   noredirect     - Don't create a redirect
 *   watch          - Add the page and the redirect to your watchlist
 *   unwatch        - Remove the page and the redirect from your watchlist
 *   ignorewarnings - Ignore any warnings
 *
 * Example:
 *   api.php?action=move&from=Exampel&to=Example&token=123ABC&reason=Misspelled%20title&movetalk&noredirect
 */
class jmwModuleMove extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'from',
		'fromid',
		'to',
		'token',
		'reason',
		'movetalk',
		'movesubpages',
		'noredirect',
		'watch',
		'unwatch',
		'ignorewarnings',
	);

}