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
 * MediaWiki feedwatchlist request.
 *
 * action=feedwatchlist
 * This module returns a watchlist feed
 *
 * This module requires read rights.
 *
 * Parameters:
 *   feedformat     - The format of the feed
 *                    One value: rss, atom
 *                    Default: rss
 *   hours          - List pages modified within this many hours from now
 *                    The value must be between 1 and 72
 *                    Default: 24
 *   allrev         - Include multiple revisions of the same page within given timeframe.
 *   wlowner        - The user whose watchlist you want (must be accompanied by wltoken if it's not you)
 *   wltoken        - Security token that requested user set in their preferences
 *
 * Example:
 *   api.php?action=feedwatchlist
 */
class jmwModuleFeedwatchlist extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'feedformat',
		'hours',
		'allrev',
		'wlowner',
		'wltoken',
	);

}