<?php
/**
 * @version		$Id:$
 * @package		JMediaWiki
 * @copyright	Copyright (C) 2010 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * JMediawiki Base View
 *
 * @package		JMediaWiki
 */
class MediawikiView extends JView
{
	public function __construct()
	{
		$option = JRequest::getCmd( 'option' );
		$app = JFactory::getApplication();

		// Set up layout override paths in reverse order.
		// Precedence is: template specific -> template shared -> component specific -> component shared
		$config['template_path'] = array(
			JPATH_COMPONENT.DS.'layouts',														// Component shared
			JPATH_COMPONENT.DS.'views'.DS.$this->getName().DS.'tmpl',							// Component specific
			JPATH_THEMES.DS.$app->getTemplate().DS.'html'.DS.$option.DS.'layouts',				// Template shared
		//	JPATH_THEMES.DS.$app->getTemplate().DS.'html'.DS.$option.DS.$this->getName(),		// Last resort (added automatically)
			);

		parent::__construct( $config );
	}

}