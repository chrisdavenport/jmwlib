<?php
/**
 * @version		$Id:$
 * @package		JMediaWiki
 * @copyright	Copyright (C) 2010 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import( 'model', JPATH_COMPONENT, null );

/**
 * Mediawiki Component Page Model
 *
 * @package		JMediaWiki
 */
class MediawikiModelPage extends MediawikiModel
{
	/**
	 * Page data.
	 */
	protected $pages = '';

	/**
	 * Start page.
	 */
	protected $from = '';

	/**
	 * Page title prefix.
	 */
	protected $prefix = '';

	/**
	 * Namespace.
	 * Note: 0 = Main: so specify an empty string to mean "all".
	 */
	protected $namespace = '0';

	/**
	 * Namespaces.
	 */
	protected $_namespaces = array();

	/**
	 * Namespace map.
	 */
	protected $nsmap = array();

	/**
	 * Method to get pages from the wiki.
	 *
	 * @access public
	 */
	public function getPages()
	{
		// Load the content if it doesn't already exist.
		if (!empty( $this->_pages )) {
			return $this->_pages;
		}


		$this->login();

		// Construct argument array.
		$from	= $this->getState( 'from' );
		$prefix	= $this->getState( 'prefix' );
		$ns		= $this->getState( 'namespace' );

		$args = array();
		if ($from != '')	$args['apfrom']		= $from;
		if ($prefix != '')	$args['apprefix']	= $prefix;
		if ($ns != '')		$args['apnamespace']= $ns;

		$req = jmwWiki::getModule( 'block' )
//			->titles( 'Main%20Page|Developers' )
//			->pageids( '53|348' )
//			->revids( '20938|8092' )
//			->prop( 'info', array( 'inprop' => 'protection|talkid|watched|subjectid|url|readable|preload' ) )
//			->generator( 'templates', 0 )
//			->lists( 'allpages', $args )
			->gettoken()
			->call( $this->wiki )
			;

		if ($warnings = $req->warnings) {
			$this->_pages = 'Warning: '.implode( ', ', $warnings )."<br/>\n";
		}
		else {
			$this->_pages = $req->pages;
		}

		return $this->_pages;
	}

	/**
	 * Get results
	 */
	public function getResults()
	{
		return $this->_pages;
	}

}
