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
 * MediaWiki upload request.
 *
 * action=upload
 * Upload a file, or get the status of pending uploads. Several methods are available:
 *  * Upload file contents directly, using the "file" parameter
 *  * Have the MediaWiki server fetch a file from a URL, using the "url" parameter
 *  * Complete an earlier upload that failed due to warnings, using the "sessionkey" parameter
 * Note that the HTTP POST must be done as a file upload (i.e. using multipart/form-data) when
 * sending the "file". Note also that queries using session keys must be
 * done in the same login session as the query that originally returned the key (i.e. do not
 * log out and then log back in). Also you must get and send an edit token before doing any upload stuff.
 *
 * This module requires read rights.
 * This module requires write rights.
 * This module only accepts POST requests.
 *
 * Parameters:
 *   filename       - Target filename
 *   comment        - Upload comment. Also used as the initial page text for new files if "text" is not specified
 *                    Default:
 *   text           - Initial page text for new files
 *   token          - Edit token. You can get one of these through prop=info
 *   watch          - Watch the page
 *   ignorewarnings - Ignore any warnings
 *   file           - File contents
 *   url            - Url to fetch the file from
 *   sessionkey     - Session key returned by a previous upload that failed due to warnings
 *
 * Examples:
 *   Upload from a URL:
 *     api.php?action=upload&filename=Wiki.png&url=http%3A//upload.wikimedia.org/wikipedia/en/b/bc/Wiki.png
 *   Complete an upload that failed due to warnings:
 *     api.php?action=upload&filename=Wiki.png&sessionkey=sessionkey&ignorewarnings=1
 */
class jmwModuleUpload extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'filename',
		'comment',
		'text',
		'token',
		'watch',
		'ignorewarnings',
		'file',
		'url',
		'sessionkey',
	);

}