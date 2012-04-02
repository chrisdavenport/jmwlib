<?php
/**
 * @version		$Id:$
 * @package		JMediaWiki
 * @copyright	Copyright (C) 2010 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * JMediawiki Component Base Controller
 *
 * @package		JMediaWiki
 */
class MediawikiController extends JController
{
	/**
	 * Method to show a wiki request form.
	 *
	 * @access	public
	 */
	public function display()
	{
		// Set a default view if none exists.
		if (!JRequest::getCmd( 'view' ) ) {
			JRequest::setVar( 'view', 'dashboard' );
		}

		parent::display();
	}

}
