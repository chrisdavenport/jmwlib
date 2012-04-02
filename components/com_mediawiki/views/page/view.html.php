<?php
/**
 * @version		$Id:$
 * @package		Joomla
 * @subpackage	MediaWiki
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
 * @package		Joomla
 * @subpackage	Mediawiki
 * @since 1.0
 */
class MediawikiViewPage extends JView
{
	function display( $tpl = null)
	{
		$pages = $this->get( 'pages' );
		$this->assignRef( 'pages', $pages );

		parent::display( $tpl );
	}
}
