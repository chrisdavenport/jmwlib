<?php
/**
 * @version		$Id:$
 * @package		Joomla
 * @subpackage	MediaWiki
 * @copyright	Copyright (C) 2010 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * MediaWiki login request.
 *
 * action=login (lg)
 * This module is used to login and get the authentication tokens.
 * In the event of a successful log-in, a cookie will be attached
 * to your session. In the event of a failed log-in, you will not
 * be able to attempt another log-in through this method for 5 seconds.
 * This is to prevent password guessing by automated password crackers.
 *
 * This module only accepts POST requests.
 * Parameters:
 *   lgname         - User Name
 *   lgpassword     - Password
 *   lgdomain       - Domain (optional)
 *   lgtoken        - Login token obtained in first request
 * Example:
 *   api.php?action=login&lgname=user&lgpassword=password
 */
class jmwModuleLogin extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'lgname',
		'lgpassword',
		'lgdomain',
		'lgtoken',
	);

	/**
	 * Make the API call to the wiki.
	 *
	 * @param	object	Mediawiki object.
	 * @return	object	This object for method chaining.
	 */
	public function call( jmwWiki $wiki )
	{
		// Make the call.
		parent::call( $wiki );

		// Get the returned data.
		$login = $this->getData();

		// Check the result attribute and return with an error if login failed.
		if ($login['result'] != 'Success') {
			throw new jmwlibLoginModuleException( 'Login failed ('.$login['result'].') '.$login['details'] );
		}

		return $this;
	}

}

class jmwlibLoginModuleException extends jmwlibModuleException {}
