<?php
/**
 * @version		$Id:$
 * @package		JMediaWiki
 * @copyright	Copyright (C) 2010 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';

// Require specific controller if requested
if ($controller = JRequest::getCmd( 'controller', JRequest::getCmd( 'view' ) )) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists( $path )) {
		require_once $path;
	}
	else {
		$controller = '';
	}
}

// Create the controller
$classname	= 'MediawikiController'.ucfirst( $controller );
$controller = new $classname();

// Perform the Request task
$controller->execute( JRequest::getCmd( 'task' ) );

// Redirect if set by the controller
$controller->redirect();
