<?php
/**
 * @version		$Id:$
 * @package		JMediaWiki
 * @copyright	Copyright (C) 2010 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Mediawiki component
 *
 * @static
 * @package		JMediaWiki
 */
class MediawikiViewRip extends JView
{
	public function display( $tpl = null)
	{
		$results	= $this->get( 'results' );
		$namespaces = $this->get( 'namespaces', 'namespaces' );
		$categories = $this->get( 'categories' );
		$state		= $this->get( 'state' );

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

		$this->assignRef( 'results',	$results );
		$this->assignRef( 'namespaces',	$namespaces );
		$this->assignRef( 'nsmap',		$nsmap );
		$this->assignRef( 'lists',		$lists );
		$this->assignRef( 'categories',	$categories );
		$this->assignRef( 'state',		$state );

		parent::display( $tpl );
	}

}