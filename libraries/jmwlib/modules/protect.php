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
 * MediaWiki protect request.
 *
 * action=protect
 * Change the protection level of a page.
 *
 * This module requires read rights.
 * This module requires write rights.
 * This module only accepts POST requests.
 *
 * Parameters:
 *   title          - Title of the page you want to (un)protect.
 *   token          - A protect token previously retrieved through prop=info
 *   protections    - Pipe-separated list of protection levels, formatted action=group (e.g. edit=sysop)
 *   expiry         - Expiry timestamps. If only one timestamp is set, it'll be used for all protections.
 *                    Use 'infinite', 'indefinite' or 'never', for a neverexpiring protection.
 *                    Default: infinite
 *   reason         - Reason for (un)protecting (optional)
 *                    Default:
 *   cascade        - Enable cascading protection (i.e. protect pages included in this page)
 *                    Ignored if not all protection levels are 'sysop' or 'protect'
 *   watch          - If set, add the page being (un)protected to your watchlist
 *
 * Examples:
 *   api.php?action=protect&title=Main%20Page&token=123ABC&protections=edit=sysop|move=sysop&cascade&expiry=20070901163000|never
 *   api.php?action=protect&title=Main%20Page&token=123ABC&protections=edit=all|move=all&reason=Lifting%20restrictions
 */
class jmwModuleProtect extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'title',
		'token',
		'protections',
		'expiry',
		'reason',
		'cascade',
		'watch',
	);

}