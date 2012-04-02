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
 * JMediawiki Component Dashboard Model
 *
 * @package		JMediaWiki
 */
class MediawikiModelDashboard extends MediawikiModel
{
	/**
	 * Perform tests on the wiki and return array of results.
	 *
	 * @return	array	Array of test results.
	 */
	public function getTests()
	{
		$tests = array();

		$req = jmwWiki::getModule( 'query' )
			->meta( 'siteinfo', array( 'siprop' => 'general|statistics|namespacealiases' ) )
			->call( $this->getWiki() )
			;

		return $req->getData();
	}
}
