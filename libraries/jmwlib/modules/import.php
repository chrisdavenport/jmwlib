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
 * MediaWiki import request.
 *
 * action=import
 * Import a page from another wiki, or an XML file
 *
 * This module requires read rights.
 * This module requires write rights.
 * This module only accepts POST requests.
 *
 * Parameters:
 *   token          - Import token obtained through prop=info
 *   summary        - Import summary
 *   xml            - Uploaded XML file
 *   interwikisource - For interwiki imports: wiki to import from
 *                    One value: meta, nost, de, es, fr, it, pl
 *   interwikipage  - For interwiki imports: page to import
 *   fullhistory    - For interwiki imports: import the full history, not just the current version
 *   templates      - For interwiki imports: import all included templates as well
 *   namespace      - For interwiki imports: import to this namespace
 *                    One value: 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
 *
 * Examples:
 *   Import [[meta:Help:Parserfunctions]] to namespace 100 with full history:
 *     api.php?action=import&interwikisource=meta&interwikipage=Help:ParserFunctions&namespace=100&fullhistory&token=123ABC
 */
class jmwModuleImport extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'token',
		'summary',
		'xml',
		'interwikisource',
		'interwikipage',
		'fullhistory',
		'templates',
		'namespace',
	);

}