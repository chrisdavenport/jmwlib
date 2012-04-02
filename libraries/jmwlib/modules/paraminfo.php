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
 * MediaWiki paraminfo request.
 *
 * action=paraminfo
 * Obtain information about certain API parameters
 *
 * Parameters:
 *   modules        - List of module names (value of the action= parameter)
 *   querymodules   - List of query module names (value of prop=, meta= or list= parameter)
 *   mainmodule     - Get information about the main (top-level) module as well
 *   pagesetmodule  - Get information about the pageset module (providing titles= and friends) as well
 *
 * Example:
 *   api.php?action=paraminfo&modules=parse&querymodules=allpages|siteinfo
 */
class jmwModuleParaminfo extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'modules',
		'querymodules',
		'mainmodule',
		'pagesetmodule',
	);

}