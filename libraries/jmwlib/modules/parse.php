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
 * MediaWiki parse request.
 *
 * action=parse
 * This module parses wikitext and returns parser output
 *
 * This module requires read rights.
 *
 * Parameters:
 *   title          - Title of page the text belongs to
 *                    Default: API
 *   text           - Wikitext to parse
 *   summary        - Summary to parse
 *   page           - Parse the content of this page. Cannot be used together with text and title
 *   redirects      - If the page parameter is set to a redirect, resolve it
 *   oldid          - Parse the content of this revision. Overrides page
 *   prop           - Which pieces of information to get.
 *                    NOTE: Section tree is only generated if there are more than 4 sections, or if the __TOC__ keyword is present
 *                    Values (separate with '|'): text, langlinks, categories, links, templates, images, externallinks, sections, revid, displaytitle, headitems, headhtml
 *                    Default: text|langlinks|categories|links|templates|images|externallinks|sections|revid|displaytitle
 *   pst            - Do a pre-save transform on the input before parsing it.
 *                    Ignored if page or oldid is used.
 *   onlypst        - Do a PST on the input, but don't parse it.
 *                    Returns PSTed wikitext. Ignored if page or oldid is used.
 *
 * Example:
 *   api.php?action=parse&text={{Project:Sandbox}}
 */
class jmwModuleParse extends jmwModule
{
	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'title',
		'text',
		'summary',
		'page',
		'redirects',
		'oldid',
		'prop',
		'pst',
		'onlypst',
	);

	/**
	 * Make the API call to the wiki.
	 *
	 * @param	object	jmwWiki object.
	 * @return	object	This object for method chaining.
	 */
	public function call( jmwWiki $wiki )
	{
		// Always request data in PHP format.
		$this->args['format'] = 'php';

		// Make the call.
		$wiki->call( $this );
		$this->data['page'] = unserialize( $wiki->getData() );

		return $this;
	}

}