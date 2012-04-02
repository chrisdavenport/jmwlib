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
 * JMediawiki Component Import Model
 *
 * @package		JMediaWiki
 */
class MediawikiModelImport extends MediawikiModel
{
	/**
	 * Page data.
	 */
	protected $_pages = '';

	/**
	 * Start page.
	 */
	protected $from = '';

	/**
	 * Page title prefix.
	 */
	protected $prefix = '';

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

		// Construct argument array.
		$from	= $this->getState( 'from' );
		$prefix	= $this->getState( 'prefix' );
		$ns		= $this->getState( 'namespace' );

		$args = array();
		if ($from != '')	$args['apfrom']		= $from;
		if ($prefix != '')	$args['apprefix']	= $prefix;
		if ($ns != '')		$args['apnamespace']= $ns;

		$req = jmwWiki::getModule( 'query' )
			->lists( 'allpages', $args )
			->call( $this->wiki )
			;

		$this->_pages = $req->getData();
		$this->_pages = $this->_pages['allpages'];

		return $this->_pages;
	}

	/**
	 * Get results
	 */
	public function getResults()
	{
		return $this->_pages;
	}

	/**
	 * Method to get pages from the wiki and save them as Joomla articles.
	 *
	 * @access public
	 */
	public function savePages()
	{
		// Get model state data.
		$from		= $this->getState( 'from' );			// Start from this page
		$prefix		= $this->getState( 'prefix' );			// Retrieve only pages with this prefix
		$ns			= $this->getState( 'namespace' );		// Retrieve only pages from this namespace
		$category	= $this->getState( 'category' );		// Joomla category, not MediaWiki
		$update		= $this->getState( 'update' );			// Update existing pages with the same title?
		$remove_ns	= $this->getState( 'remove_ns' );		// Remove namespace name from page titles?
		$remove_toc	= $this->getState( 'remove_toc' );		// Remove table of contents from page contents?
		$keyref		= $this->getState( 'keyref' );			// Add help screen key reference to Joomla article?
		$page_max	= $this->getState( 'page_max' );		// Maximum number of wiki pages to retrieve
		$watch		= $this->getState( 'watch' );			// Add wiki page to watchlist

		// Construct argument array.
		$args = array();
		if ($from != '')	$args['apfrom']		= $from;
		if ($prefix != '')	$args['apprefix']	= $prefix;
		if ($ns != '')		$args['apnamespace']= $ns;

		// Get list of page names from wiki.
		$req = jmwWiki::getModule( 'query' )
			->lists( 'allpages', $args )
			->call( $this->wiki, $page_max )
			;

		// Get page data returned.
		$this->_pages = $req->getData();
		$this->_pages = $this->_pages['allpages'];

		// Get global database object.
		$db = $this->getDBO();

		// Determine the section id from the category id.
		$query = 'SELECT section'
			. ' FROM #__categories'
			. ' WHERE id=' . (int) $category
			;
		$db->setQuery( $query );
		$section = $db->loadObject();
		$sectionid = $section->section;

		// Setup some basic information.
		$article	= JTable::getInstance( 'content' );
		$user		= JFactory::getUser();
		$userid		= $user->get( 'id' );
		$import_count = 0;

		// Make sure we have an array of namespaces.
		$this->getNamespaces();

		// Loop through the pages from the wiki.
		foreach ($this->_pages as $page) {

//			if ($import_count > 1) {		// DIAGNOSTIC
//				continue;					// DIAGNOSTIC
//			}

			// Retrieve individual page from wiki.
			$p_args = array();
			$p_req = jmwWiki::getModule( 'parse' )
				->page( $page['title'] )
				->call( $this->getWiki() )
				;
			$page_data = $p_req->page;
			$parse = $page_data['parse'];
			$page_text = $parse['text']['*'];

			// Remove namespace from page title if requested.
			if ($remove_ns) {
				$page['title'] = str_replace( $this->_nsmap[$ns].':', '', $page['title'] );
			}

			// Construct alias from page title.
			$data['alias'] = $page['title'];
			$data['alias'] = JFilterOutput::stringURLSafe( $data['alias'] );

			if (trim( str_replace( '-', '', $data['alias'] )) == '') {
				$datenow = JFactory::getDate();
				$data['alias'] = $datenow->toFormat( "%Y-%m-%d-%H-%M-%S" );
			}

			// Remove table of contents.
			if ($remove_toc) {
				$pattern = '!<table id="toc" class="toc" summary="Contents">(.+)</table>!msU';
				$page_text = preg_replace( $pattern, '', $page_text );
			}

			// Remove links to wiki image information pages.
			$pattern = '!<a href="' . $this->wiki->getURI()->getPath() . '/Image:([^>]+)>(.+)</a>!';
			$page_text = preg_replace( $pattern, '$2', $page_text );

			// Get component parameters.
			$option = JRequest::getCmd( 'option' );
			$params = JComponentHelper::getParams( 'com_mediawiki' );

			// Replace links to other wiki pages with links to this site.
			// Note that these new links use the very inefficient key reference mechanism
			// to find an article with a matching page title.
			$uri = JFactory::getURI( JURI::root() );
			$wiki_uri = JURI::getInstance( $params->get( 'wiki_url' ) );
			$pattern = '<a href="' . $wiki_uri->getPath() . '/';
			$replace = '<a href="' . $uri->toString( array( 'path' ) ) . 'index.php?option=com_content&task=findkey&keyref=';
			$page_text = str_replace( $pattern, $replace, $page_text );

			$pattern = '?option=com_content&task=findkey&keyref=' . $this->_nsmap[$ns] . ':';
			$replace = '?option=com_content&task=findkey&keyref=';
			$page_text = str_replace( $pattern, $replace, $page_text );

			// Replace relative links to images with absolute links that point to the wiki instead of this site.
			$pattern = 'src="' . $wiki_uri->getPath() . '/images/';
			$replace = 'src="' . $wiki_uri->toString( array( 'scheme', 'host', 'path' ) ) . '/images/';
			$page_text = str_replace( $pattern, $replace, $page_text );

			// If there is an existing article in the same category with the same title, then update it.
			if ($update) {
				$query = 'SELECT id'
					. ' FROM #__content'
					. ' WHERE title=' . $db->quote( $page['title'] )
					. ' AND catid=' . (int) $category
					;
				$db->setQuery( $query );
				$result = $db->loadObject();
			}
			$data['id'] = (isset( $result )) ? $result->id : 0;

			// Set up rest of page data.
			$data['title']		= $page['title'];
			$data['introtext']	= $page_text;
			$data['sectionid']	= (int) $sectionid;
			$data['catid']		= (int) $category;
			$data['created']	= gmdate( 'Y-m-d H:i:s' );
			$data['created_by']	= (int) $userid;
			$data['state']		= 1;
			$data['version']	= (int) $parse['revid'];

			// If requested, add help screen key reference.
			if ($keyref) {
				$data['attribs'] = 'keyref=' . str_replace( ' ', '_', $page['title'] );
			}

			// Bind the form fields to the web link table
			if (!$article->bind( $data )) {
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}

			// Store the article in the database.
			if (!$article->store()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// Add page we just imported onto our watchlist.
			if ($watch) {
/*
				$req = jmwWiki::getModule( 'query' )
					->titles( $page['title'] )
					->prop( 'info', array( 'intoken' => 'edit' ) )
					->call( $this->wiki )
					;
				$token = $req->getData();
				$token = $token['query']['pages'];
				$token = reset( $token );
				$token = $token['edittoken'];

				$req = jmwWiki::getModule( 'edit' )
					->title( $page['title'] )
					->token( $token )
					->appendtext( 'test' )
					->watch()
					->call( $this->wiki )
					;
*/
				$version = $this->getWiki()->getVersion();

				// Add page to watchlist (requires MediaWiki >= 1.14).
				if ($version[0] >= 1 && $version[1] >= 14) {
					$req = jmwWiki::getModule( 'watch' )
						->title( $page['title'] )
						->call( $this->getWiki() )
						;
				}
			}

			// All done, increment counter.
			$import_count++;

		}

		return $import_count;
	}

	/**
	 * Method to get a list of Joomla section/categories.
	 */
	public function getCategories()
	{
		$query = 'SELECT c.id, c.title as category, s.title as section'
			. ' FROM #__categories as c'
			. ' LEFT JOIN #__sections as s'
			. ' ON c.section=s.id'
			. ' ORDER BY c.ordering'
			;
		$db = $this->getDBO();
		$db->setQuery( $query );
		$cat_list = $db->loadObjectList();

		return $cat_list;
	}
}
