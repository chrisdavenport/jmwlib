<?php
/**
 * @version		$Id:$
 * @package		JMediaWiki
 * @copyright	Copyright (C) 2010 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport( 'jmwlib.jmwwiki' );

/**
 * JMediawiki Component Base Model
 *
 * @package		JMediaWiki
 */
class MediawikiModel extends JModel
{
	/**
	 * Login token.
	 */
	protected $lgtoken = '';

	/**
	 * Namespaces array.
	 */
	protected $_namespaces = array();

	/**
	 * Namespace map.
	 */
	protected $_nsmap = array();

	/**
	 * Namespace aliases.
	 */
	protected $_nsaliases = array();

	/**
	 * Component/menu parameters.
	 */
	protected $params = null;

	/**
	 * MediaWiki object.
	 */
	protected $wiki = null;

	/**
	 * Constructor.
	 */
	public function __construct( $config = array() )
	{
		parent::__construct( $config );

		// Get a wiki instance.
		$this->params = JComponentHelper::getParams( JRequest::getCmd( 'option' ) );
		$this->wiki	= jmwWiki::getInstance( JURI::getInstance( $this->params->get( 'wiki_url' ) ) );
	}

	/**
	 * Return the wiki object.
	 */
	public function getWiki()
	{
		return $this->wiki;
	}

	/**
	 * Login to the wiki.
	 *
	 * @access public
	 */
	function login()
	{
		// Invalidate the current login token (if any).
		$this->lgtoken = '';

		// Create and configure a login module object.
		$req = jmwWiki::getModule( 'login' )
			->lgname( $this->params->get( 'wiki_user' ) )
			->lgpassword( $this->params->get( 'wiki_password' ) )
			->call( $this->getWiki() )
			;

		// Save login token for future API calls.
		$this->lgtoken = $req->lgtoken;

		return ($this->lgtoken != '') ? true : false;
	}

	/**
	 * Logout from the wiki.
	 *
	 * @access public
	 */
	function logout()
	{
		$req = jmwWiki::getModule( 'logout' )
			->call( $this->getWiki() );
		$this->lgtoken = '';
	}

	/**
	 * Method to get a list of namespaces from the wiki.
	 */
	public function getNamespaces()
	{
		// Load the content if it doesn't already exist.
		if (!empty( $this->_namespaces )) {
			return $this->_namespaces;
		}

		$req = jmwWiki::getModule( 'query' )
			->meta( 'siteinfo', array( 'siprop' => 'namespaces|namespacealiases' ) )
			->call( $this->getWiki() )
			;

		$this->_namespaces	= $req->getData();
		$this->_nsaliases	= $this->_namespaces['query']['namespacealiases'];
		$this->_namespaces	= $this->_namespaces['query']['namespaces'];

		$this->_nsmap = array();
		foreach ($this->_namespaces as $k => $namespace) {
			if ($namespace['*'] == '') {
				$this->_namespaces[$k]['*'] = 'Main';
			}
			$this->_nsmap[$namespace['id']] = $this->_namespaces[$k]['*'];
		}

		return $this->_namespaces;
	}

	/**
	 * Method to get a list of namespace aliases from the wiki.
	 */
	public function getNamespaceAliases()
	{
		// Load the content if it doesn't already exist.
		if (empty( $this->_namespaces )) {
			$this->getNamespaces();
		}

		return $this->_nsaliases;
	}

}
