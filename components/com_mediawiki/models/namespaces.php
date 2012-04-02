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
class MediawikiModelNamespaces extends MediawikiModel
{
	/**
	 * Namespaces array.
	 */
	protected $_namespaces = array();

	/**
	 * Namespace map.
	 */
	protected $_nsmap = array();

	/**
	 * Method to get a list of namespaces from the wiki.
	 */
	public function getNamespaces()
	{
		// Load the content if it doesn't already exist.
		if (!empty( $this->_namespaces )) {
			return $this->_namespaces;
		}

		// Construct argument array.
		$args = array();
		$args['siprop'] = 'namespaces';

		$req = jmwWiki::getModule( 'query' )
			->meta( 'siteinfo', $args )
			->call( $this->wiki )
			;

//		if ($warnings = $req->warnings) {
//			$this->_pages = 'Warning: '.implode( ', ', $warnings )."<br/>\n";
//		}
//		else {
			$data = $req->getData();
			$this->_namespaces = $data['query']['namespaces'];
//		}

		$this->_nsmap = array();
		foreach ($this->_namespaces as $namespace) {
			if ($namespace['*'] == '') {
				$namespace['*'] = 'Main';
			}
			$this->_nsmap[$namespace['id']] = $namespace['*'];
		}

		return $this->_namespaces;
	}

}
