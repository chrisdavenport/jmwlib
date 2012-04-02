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
 * MediaWiki block request.
 *
 * action=block
 * Block a user.
 *
 * This module requires read rights.
 * This module requires write rights.
 * This module only accepts POST requests.
 *
 * Parameters:
 *   user           - Username, IP address or IP range you want to block
 *   token          - A block token previously obtained through the gettoken parameter or prop=info
 *   gettoken       - If set, a block token will be returned, and no other action will be taken
 *   expiry         - Relative expiry time, e.g. '5 months' or '2 weeks'. If set to 'infinite', 'indefinite' or 'never', the block will never expire.
 *                    Default: never
 *   reason         - Reason for block (optional)
 *   anononly       - Block anonymous users only (i.e. disable anonymous edits for this IP)
 *   nocreate       - Prevent account creation
 *   autoblock      - Automatically block the last used IP address, and any subsequent IP addresses they try to login from
 *   noemail        - Prevent user from sending e-mail through the wiki. (Requires the "blockemail" right.)
 *   hidename       - Hide the username from the block log. (Requires the "hideuser" right.)
 *   allowusertalk  - Allow the user to edit their own talk page (depends on $wgBlockAllowsUTEdit)
 *   reblock        - If the user is already blocked, overwrite the existing block
 *
 * Examples:
 *   api.php?action=block&user=123.5.5.12&expiry=3%20days&reason=First%20strike
 *   api.php?action=block&user=Vandal&expiry=never&reason=Vandalism&nocreate&autoblock&noemail
 */
class jmwModuleBlock extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'user',
		'token',
		'gettoken',
		'expiry',
		'reason',
		'anononly',
		'nocreate',
		'autoblock',
		'noemail',
		'hidename',
		'allowusertalk',
		'reblock',
	);

}