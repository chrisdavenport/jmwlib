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
 * MediaWiki opensearch request.
 *
 * action=opensearch *
 * This module implements OpenSearch protocol
 *
 * This module requires read rights.
 *
 * Parameters:
 *   search         - Search string
 *   limit          - Maximum amount of results to return
 *                    No more than 100 (100 for bots) allowed.
 *                    Default: 10
 *   namespace      - Namespaces to search
 *                    Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
 *                    Default: 0
 *   suggest        - Do nothing if $wgEnableOpenSearchSuggest is false
 *   format         -
 *
 * Example:
 *   api.php?action=opensearch&search=Te
 */
class jmwModuleOpensearch extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'search',
		'limit',
		'namespace',
		'suggest',
		'format',
	);

}