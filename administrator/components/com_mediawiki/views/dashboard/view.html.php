<?php
/**
 * @version		$Id:$
 * @package		JMediaWiki
 * @copyright	Copyright (C) 2010 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import( 'view', JPATH_COMPONENT, null );

/**
 * JMediawiki Component Dashboard View
 *
 * @package		JMediaWiki
 */
class MediawikiViewDashboard extends MediawikiView
{
	public function display( $tpl = null )
	{
		try {
			$tests = $this->get( 'tests' );
			$tests = $tests['query'];
		}
		catch (jmwlibHttpException $e) {
			$tests = array( 'error' => $e->getMessage() );
		}
		catch (jmwlibModuleException $e) {
			$tests = array( 'error' => $e->getMessage() );
		}

		// Get component parameters and assign them to the template.
		$option = JRequest::getCmd( 'option' );
		$params = JComponentHelper::getParams( 'com_mediawiki' );

		$this->assignRef( 'tests',	$tests );
		$this->assignRef( 'params',	$params );

		parent::display( $tpl );
	}

}