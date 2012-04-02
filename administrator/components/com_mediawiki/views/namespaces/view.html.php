<?php
/**
 * @version		$Id:$
 * @package		Joomla-MediaWiki
 * @copyright	Copyright (C) 2010 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import( 'view', JPATH_COMPONENT, null );

/**
 * JMediawiki Component Namespaces View
 *
 * @static
 * @package		Joomla-MediaWiki
 */
class MediawikiViewNamespaces extends MediawikiView
{
	public function display( $tpl = null )
	{
		try {
			$namespaces = $this->get( 'namespaces' );
			$nsaliases	= $this->get( 'namespacealiases' );
			$pagination	= $this->get( 'pagination' );
		}
		catch (Exception $e) {
			$app = JFactory::getApplication();
			$option = JRequest::getCmd( 'option' );
			$app->redirect( 'index.php?option='.$option.'&view=dashboard', $e->getMessage(), 'error' );
		}

		// Get component parameters and assign them to the template.
		$option = JRequest::getCmd( 'option' );
		$params = JComponentHelper::getParams( 'com_mediawiki' );

		$this->assignRef( 'namespaces',	$namespaces );
		$this->assignRef( 'nsaliases',	$nsaliases );
		$this->assignRef( 'tests',		$tests );
		$this->assignRef( 'params',		$params );
		$this->assignRef( 'pagination',	$pagination );

		parent::display( $tpl );
	}
}
