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
 * MediaWiki expandtemplates request.
 *
 * action=expandtemplates
 * This module expand all templates in wikitext
 *
 * This module requires read rights.
 *
 * Parameters:
 *   title          - Title of page
 *                    Default: API
 *   text           - Wikitext to convert
 *   generatexml    - Generate XML parse tree
 *
 * Example:
 *   api.php?action=expandtemplates&text={{Project:Sandbox}}
 */
class jmwModuleExpandtemplates extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'title',
		'text',
		'generatexml',
	);

}