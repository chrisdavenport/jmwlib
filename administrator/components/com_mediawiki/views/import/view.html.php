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
 * JMediawiki Component Import View
 *
 * @package		JMediaWiki
 */
class MediawikiViewImport extends MediawikiView
{
	public function display( $tpl = null )
	{
		$option = JRequest::getCmd( 'option' );

		try {
			$pages		= $this->get( 'pages' );
			$namespaces	= $this->get( 'namespaces' );
			$pagination	= $this->get( 'pagination' );
			$categories = $this->get( 'categories' );
			$state		= $this->get( 'state' );
		}
		catch (Exception $e) {
			$app = JFactory::getApplication();
			$app->redirect( 'index.php?option='.$option.'&view=dashboard', $e->getMessage(), 'error' );
		}

		// Get component parameters and assign them to the template.
		$params = JComponentHelper::getParams( $option );

		// Default namespace is Main.
		if ($state->namespace == '') {
			$state->namespace = '0';
		}

		// Construct drop-down list of namespaces.
		$options = array();
		$nsmap = array();
		foreach ($namespaces as $namespace) {
			if ($namespace['*'] == '') {
				$namespace['*'] = 'Main';
			}
			$options[] = JHTML::_( 'select.option', $namespace['id'], $namespace['*'] );
			$nsmap[$namespace['id']] = $namespace['*'];
		}
		$lists['namespaces'] = JHTML::_( 'select.genericlist', $options, 'namespace', null, 'value', 'text', $state->namespace );

		// Construct drop-down list of sections and categories.
		$options = array();
		foreach ($categories as $category) {
			$options[] = JHTML::_( 'select.option', $category->id, $category->section.' / '.$category->category );
		}
		$lists['categories'] = JHTML::_( 'select.genericlist', $options, 'category', null, 'value', 'text', $state->category );

		$this->assignRef( 'namespaces',	$namespaces );
		$this->assignRef( 'nsmap',		$nsmap );
		$this->assignRef( 'tests',		$tests );
		$this->assignRef( 'pages',		$pages );
		$this->assignRef( 'lists',		$lists );
		$this->assignRef( 'params',		$params );
		$this->assignRef( 'pagination',	$pagination );
		$this->assignRef( 'state',		$state );

		parent::display( $tpl );
	}

}