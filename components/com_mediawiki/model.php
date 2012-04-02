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
 * Mediawiki Component Page Model
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

		// Set user credentials.
		$this->wiki->setUser( $this->params->get( 'wiki_user' ), $this->params->get( 'wiki_password' ) );

		// Create and configure a login module object.
		$req = jmwWiki::getModule( 'login' );

		// Make the API call.
		if (!$req->call( $this->wiki )) {
			$this->setError( $req->getError() );
			return false;
		}

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
			->call( $this->wiki );
		$this->lgtoken = '';
	}

}
