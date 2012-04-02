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
 * MediaWiki edit request.
 *
 * action=edit
 * Create and edit pages.
 *
 * This module requires read rights.
 * This module requires write rights.
 * This module only accepts POST requests.
 *
 * Parameters:
 *   title          - Page title
 *   section        - Section number. 0 for the top section, 'new' for a new section
 *   text           - Page content
 *   token          - Edit token. You can get one of these through prop=info
 *   summary        - Edit summary. Also section title when section=new
 *   minor          - Minor edit
 *   notminor       - Non-minor edit
 *   bot            - Mark this edit as bot
 *   basetimestamp  - Timestamp of the base revision (gotten through prop=revisions&rvprop=timestamp).
 *                    Used to detect edit conflicts; leave unset to ignore conflicts.
 *   starttimestamp - Timestamp when you obtained the edit token.
 *                    Used to detect edit conflicts; leave unset to ignore conflicts.
 *   recreate       - Override any errors about the article having been deleted in the meantime
 *   createonly     - Don't edit the page if it exists already
 *   nocreate       - Throw an error if the page doesn't exist
 *   captchaword    - Answer to the CAPTCHA
 *   captchaid      - CAPTCHA ID from previous request
 *   watch          - DEPRECATED! Add the page to your watchlist
 *   unwatch        - DEPRECATED! Remove the page from your watchlist
 *   watchlist      - Unconditionally add or remove the page from your watchlist, use preferences or do not change watch
 *                    One value: watch, unwatch, preferences, nochange
 *                    Default: preferences
 *   md5            - The MD5 hash of the text parameter, or the prependtext and appendtext parameters concatenated.
 *                    If set, the edit won't be done unless the hash is correct
 *   prependtext    - Add this text to the beginning of the page. Overrides text.
 *   appendtext     - Add this text to the end of the page. Overrides text
 *   undo           - Undo this revision. Overrides text, prependtext and appendtext
 *   undoafter      - Undo all revisions from undo to this one. If not set, just undo one revision
 *
 * Examples:
 *   Edit a page (anonymous user):
 *     api.php?action=edit&title=Test&summary=test%20summary&text=article%20content&basetimestamp=20070824123454&token=%2B\
 *   Prepend __NOTOC__ to a page (anonymous user):
 *     api.php?action=edit&title=Test&summary=NOTOC&minor&prependtext=__NOTOC__%0A&basetimestamp=20070824123454&token=%2B\
 *   Undo r13579 through r13585 with autosummary(anonymous user):
 *     api.php?action=edit&title=Test&undo=13585&undoafter=13579&basetimestamp=20070824123454&token=%2B\
 */
class jmwModuleEdit extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'title',
		'section',
		'text',
		'token',
		'summary',
		'minor',
		'notminor',
		'bot',
		'basetimestamp',
		'starttimestamp',
		'recreate',
		'createonly',
		'nocreate',
		'captchaword',
		'captchaid',
		'watch',			// DEPRECATED
		'unwatch',			// DEPRECATED
		'watchlist',
		'md5',
		'prependtext',
		'appendtext',
		'undo',
		'undoafter',
	);

}