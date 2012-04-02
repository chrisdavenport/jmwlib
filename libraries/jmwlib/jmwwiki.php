<?php
/**
 * @version		$Id:$
 * @package		jmwlib
 * @copyright	Copyright (C) 2010 - 2011 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the module class too.
jimport( 'jmwlib.mwmodule' );

class jmwWiki
{
	/**
	 * Wiki JURI object.
	 * This is the base URI of the wiki (omitting any index.php).
	 */
	protected $_wiki_uri = null;

	/**
	 * Wiki API JURI object.
	 * This is the base URI of the wiki API.
	 */
	protected $_api_uri = null;

	/**
	 * Response data.
	 */
	protected $_response = null;

	/**
	 * Constructor.
	 *
	 * @param	array	Array of configuration parameters.
	 */
	public function __construct( $config = array() )
	{
		if (isset( $config['wiki_uri'] )) {
			$this->_wiki_uri = $config['wiki_uri'];
		}

		$this->_api_uri  = clone $this->_wiki_uri;
		$path = $this->_api_uri->getPath();
		$this->_api_uri->setPath( $path . '/api.php' );
	}

	/**
	 * Make a call to the remote MediaWiki API.
	 *
	 * @param	jmwModule	MediaWiki request object.
	 * @param	integer		Cache lifetime (in seconds).
	 *
	 * @return	jmwWiki	The MediaWiki object.
	 */
	public function call( jmwModule $request, $cache_lifetime = 0 )
	{
		$this->_api_uri->setQuery( (string) $request );

		if ($cache_lifetime) {

			// Get JCache object.
			$cache = JFactory::getCache();

			// Set cache lifetime (in seconds).
			$cache->setLifeTime( (int) $cache_lifetime );

			// Make a cachable call to the wiki API.
			$this->_response = $cache->call( array( 'jmwWiki', '_call' ), $this->_api_uri, $request->getArgs() );

		} else {

			// Make a non-cachable call to the wiki API.
			$this->_response = $this->_call( $this->_api_uri, $request->getArgs() );

		}

		return $this;
	}

	/**
	 * Get data from last request.
	 * This returns the raw, undecoded data.
	 *
	 * @return	mixed	Data returned by wiki API call.
	 */
	public function getData()
	{
		return $this->_response->data;
	}

	/**
	 * Returns a reference to a jmwWiki object, only creating one if it doesn't already exist.
	 *
	 * @param	JURI	$uri 		A JURI object giving the base URI of the MediaWiki instance.
	 * @param	array	$config 	An optional associative array of configuration settings.
	 *
	 * @return	jmwWiki				The MediaWiki object.
	 */
	public static function getInstance( JURI $uri, $config = array() )
	{
		static $instances;

		if (!isset( $instances )) {
			$instances = array();
		}

		$wiki_uri = $uri->toString( array( 'scheme', 'host', 'path' ) );
		$config['wiki_uri'] = $uri;

		if (!isset( $instances[$wiki_uri] )) {
			$instances[$wiki_uri] = new jmwWiki( $config );
		}

		return $instances[$wiki_uri];
	}

	/**
	 * Factory method which returns a jmwWiki API module object.
	 *
	 * @param	string	$type		The name of the module.
	 * @param	string	$format		The format that MediaWiki should return data in.
	 *
	 * @return	jmwWiki				The MediaWiki object.
	 */
	public static function getModule( $type = 'help', $format = 'php' )
	{
		$file = dirname( __FILE__ ).DS.'modules'.DS.$type.'.php';
		$class = 'jmwModule'.ucfirst( $type );
		if (file_exists( $file )) {
			require_once $file;
			$instance = new $class( $format );
		}
		$instance->action = $type;

		return $instance;
	}

	/**
	 * Return wiki JURI object.
	 *
	 * @return	JURI	Object representing the URI of the wiki.
	 */
	public function getURI()
	{
		return $this->_wiki_uri;
	}

	/**
	 * Get a page from the wiki and perform various manipulations on it.
	 *
	 * @param	string	Wiki page name.
	 * @param	array	Optional array of options (see below).
	 *
	 * @return	string	Wiki page text.
	 *
	 * Options:-
	 * 	remove_toc			Remove table of contents from page.
	 * 	remove_img_links	Remove links to wiki image information pages.
	 */
	public function getPageProcessed( $page_name, $options = array() )
	{
		// Get the page requested.
		$args = array();
		$req = jmwWiki::getModule( 'parse' )
			->page( $page_name )
			->call( $this )
			;

		// Instantiate a page object.
		jimport( 'jmwlib.jmwpage' );
		$page_data = $req->page;
		$page = new jmwPage( $this, $page_data['parse'] );

		// Fix links, URLs and other options.
		$page->fixLinks()
			 ->fixImages()
			 ->removeToc( isset( $options['remove_toc'] ) && $options['remove_toc'] )
			 ->removeImageLinks( isset( $options['remove_img_links'] ) && $options['remove_img_links'] )
			 ;

echo "LINKS";
print_r( $page->links );
echo "IMAGES";
print_r( $page->images );
echo "EXTERNALLINKS";
print_r( $page->externallinks );

/*
		// Replace links to other wiki pages with links to this site.
		// Note that these new links use the very inefficient key reference mechanism
		// to find an article with a matching page title.
		$uri = JFactory::getURI( JURI::root() );
		$pattern = '<a href="' . $this->_wiki_uri->getPath() . '/';
		$replace = '<a href="' . $uri->toString( array( 'path' ) ) . 'index.php?option=com_content&task=findkey&keyref=';
		$page_text = str_replace( $pattern, $replace, $page_text );

		$pattern = '?option=com_content&task=findkey&keyref=' . $this->_nsmap[$ns] . ':';
		$replace = '?option=com_content&task=findkey&keyref=';
		$page_text = str_replace( $pattern, $replace, $page_text );
*/
		return $page->text;
	}

	/**
	 * Get version number of MediaWiki.
	 * Useful for checking if a particular API call is available.
	 *
	 * @return	array	Array of version numbers (major, minor, maintenance).
	 */
	public function getVersion()
	{
		$req = jmwWiki::getModule( 'query' )
			->meta( 'siteinfo', array( 'siprop' => 'general' ) )
			->call( $this )
			;

		$data = $req->getData();
		$version = $data['query']['general']['generator'];
		$version = str_replace( 'MediaWiki ', '', $version );
		$version = explode( '.', $version );

		return $version;
	}

	/**
	 * Make a call to the remote MediaWiki API.
	 *
	 * @param	object	JURI object containing URI to retrieve.
	 * @param	array	Optional array of POST arguments.  If not empty a POST is made instead of a GET.
	 *
	 * @return	object	Object containing data returned by API call.
	 */
	protected function _call( JURI $uri, $post = array() )
	{
		// Initialise CURL object.
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $uri->toString() );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

		// If any POST arguments have been specified then change to a POST method.
		if (!empty( $post )) {
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $post );
		}

		// Create an object to hold response data.
		$response = new stdClass;

		// Perform the HTTP request using CURL.
		$response->data		= curl_exec( $curl );
		$response->status	= curl_errno( $curl );
		$response->error	= curl_error( $curl );
		$response->arguments = $post;

		// Check for errors.
		if (curl_errno( $curl ) != 0) {
			throw new jmwlibHttpException( curl_error( $curl ), curl_errno( $curl ) );
		}

		curl_close( $curl );

		return $response;
	}

}

class jmwlibHttpException extends Exception {}
