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
 * JMediawiki Component Namespaces Model
 *
 * @package		JMediaWiki
 */
class MediawikiModelNamespaces extends MediawikiModel
{
	/**
	 * Pagination object
	 *
	 * @var object
	 */
	protected $_pagination = null;

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

		$app = JFactory::getApplication();
		$option = JRequest::getCmd( 'option' );

		// Get the pagination request variables.
		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$limitstart	= $app->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly.
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState( 'limit', $limit );
		$this->setState( 'limitstart', $limitstart );
	}

	/**
	 * Method to get a pagination object for the namespaces.
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty( $this->_pagination ))
		{
			jimport( 'joomla.html.pagination' );
			$this->_pagination = new JPagination(
				$this->getTotal(),
				$this->getState( 'limitstart' ),
				$this->getState( 'limit' )
				);
		}

		return $this->_pagination;
	}

	/**
	 * Method to get the total number of namespaces.
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty( $this->_namespaces ))
		{
			$this->getNamespaces();
		}

		return count( $this->_namespaces );
	}

}