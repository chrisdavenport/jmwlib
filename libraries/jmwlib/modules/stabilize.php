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
 * MediaWiki stabilize request.
 *
 * action=stabilize
 * Configure review-protection settings.
 *
 * This module requires read rights.
 * This module requires write rights.
 * This module only accepts POST requests.
 *
 * Parameters:
 *   protectlevel   - The review-protection level
 *                    One value: autoconfirmed, review, none
 *                    Default: none
 *   expiry         - DEPRECATED! Review-protection expiry
 *                    Default: infinite
 *   reason         - Reason
 *                    Default:
 *   watch          - Watch this page
 *   token          - An edit token retrieved through prop=info
 *   title          - Title of page to be review-protected
 *
 * Example:
 *   api.php?action=stabilize&title=Test&protectlevel=none&reason=Test&token=123ABC
 */
class jmwModuleStabilize extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'protectlevel',
		'expiry',
		'reason',
		'watch',
		'token',
		'title',
	);

}