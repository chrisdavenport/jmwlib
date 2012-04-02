<?php
/**
 * @version		$Id:$
 * @package		jmwlib
 * @copyright	Copyright (C) 2010 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class jmwPage
{
	/*
	 * Wiki object.
	 */
	public $wiki = null;

	/*
	 * Page text.
	 */
	public $text = '';

	/*
	 * Array of language links.
	 */
	public $langlinks = array();

	/*
	 * Array of categories.
	 */
	public $categories = array();

	/*
	 * Array of links.
	 */
	public $links = array();

	/*
	 * Array of templates.
	 */
	public $templates = array();

	/*
	 * Array of images.
	 */
	public $images = array();

	/*
	 * Array of external links.
	 */
	public $externallinks = array();

	/*
	 * Array of sections.
	 */
	public $sections = array();

	/*
	 * Revision ID.
	 */
	public $revid = 0;

	/**
	 * Constructor.
	 *
	 * @param	jmwWiki		Wiki object.
	 * @param	array		Array of page data.
	 */
	public function __construct( jmwWiki $wiki, $page_data )
	{
		$this->wiki = $wiki;

		if (isset( $page_data['text']['*'] )) {
			$this->text = $page_data['text']['*'];
		}

		if (isset( $page_data['langlinks'] )) {
			$this->langlinks = $page_data['langlinks'];
		}

		if (isset( $page_data['categories'] )) {
			$this->categories = $page_data['categories'];
		}

		if (isset( $page_data['links'] )) {
			$this->links = $page_data['links'];
		}

		if (isset( $page_data['templates'] )) {
			$this->templates = $page_data['templates'];
		}

		if (isset( $page_data['images'] )) {
			$this->images = $page_data['images'];
		}

		if (isset( $page_data['externallinks'] )) {
			$this->externallinks = $page_data['externallinks'];
		}

		if (isset( $page_data['sections'] )) {
			$this->sections = $page_data['sections'];
		}

		if (isset( $page_data['revid'] )) {
			$this->revid = $page_data['revid'];
		}

	}

	/**
	 * Fix links.
	 * Replace relative links to other wiki pages and images with absolute links to those pages and images.
	 *
	 * @param	boolean		If false then do nothing.
	 * @return	jmwPage		This object for method chaining.
	 */
	public function fixLinks( $fix = true )
	{
		if ($fix) {
			$uri = JFactory::getURI( JURI::root() );
			$pattern = '<a href="/';
			$replace = '<a href="' . $this->wiki->getURI()->toString( array( 'scheme', 'host', 'path' ) ) . '/';
			$this->text = str_replace( $pattern, $replace, $this->text );

			//		$pattern = '<a href="' . $this->wiki->getURI()->getPath() . '/';
	//		$replace = '<a href="' . $uri->toString( array( 'path' ) ) . 'index.php?option=com_content&task=findkey&keyref=';
	//		$this->text = str_replace( $pattern, $replace, $this->text );

	//		$pattern = '?option=com_content&task=findkey&keyref=' . $this->_nsmap[$ns] . ':';
	//		$replace = '?option=com_content&task=findkey&keyref=';
	//		$this->text = str_replace( $pattern, $replace, $this->text );
		}

		return $this;
	}

	/**
	 * Fix image URLs and links to image information pages.
	 * Replace relative links to images with absolute links that point to the wiki instead of this site.
	 *
	 * @param	boolean		If false then do nothing.
	 * @return	jmwPage		This object for method chaining.
	 */
	public function fixImages( $fix = true )
	{
		if ($fix) {
			// For image source URLs, change relative to absolute URLs.
			$pattern = 'src="' . $this->wiki->getURI()->getPath() . '/images/';
			$replace = 'src="' . $this->wiki->getURI()->toString( array( 'scheme', 'host', 'path' ) ) . '/images/';
			$this->text = str_replace( $pattern, $replace, $this->text );
		}

		return $this;
	}

	/**
	 * Remove table of contents from wiki page text.
	 *
	 * @param	boolean		If false then do nothing.
	 * @return	jmwPage		This object for method chaining.
	 */
	public function removeToc( $fix = true )
	{
		if ($fix) {
			$pattern = '!<table id="toc" class="toc" summary="Contents">(.+)</table>!msU';
			$this->text = preg_replace( $pattern, '', $this->text );
		}

		return $this;
	}

	/**
	 * Remove links to wiki image information pages.
	 *
	 * @param	boolean		If false then do nothing.
	 * @return	jmwPage		This object for method chaining.
	 */
	public function removeImageLinks( $fix = true )
	{
		if ($fix) {
			$pattern = '!<a href="' . $this->wiki->getURI()->toString( array( 'scheme', 'host', 'path' ) ) . '/Image:([^>]+)>(.+)</a>!';
			$this->text = preg_replace( $pattern, '$2', $this->text );
		}

		return $this;
	}

	/**
	 * Replace links to other wiki pages with links to this site.
	 * Note that these new links use the very inefficient key reference mechanism
	 * to find an article with a matching page title.
	 *
	 * @param	boolean		If false then do nothing.
	 * @return	jmwPage		This object for method chaining.
	 */
	public function linksToKeyReferences( $fix = true )
	{
		if ($fix) {
			$uri = JFactory::getURI( JURI::root() );
			$pattern = '<a href="' . $this->_wiki_uri->getPath() . '/';
			$replace = '<a href="' . $uri->toString( array( 'path' ) ) . 'index.php?option=com_content&task=findkey&keyref=';
			$this->text = str_replace( $pattern, $replace, $this->text );

			$pattern = '?option=com_content&task=findkey&keyref=' . $this->_nsmap[$ns] . ':';
			$replace = '?option=com_content&task=findkey&keyref=';
			$this->text = str_replace( $pattern, $replace, $this->text );
		}

		return $this;
	}

}
