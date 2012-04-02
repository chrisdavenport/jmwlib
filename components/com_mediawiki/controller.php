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
 * Mediawiki Component Controller
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
			JRequest::setVar( 'view', 'rip' );
		}

		$view = $this->getView( 'rip', 'html', '', array( 'base_path'=>$this->_basePath));

		// Get/Create the model
		$model = $this->getModel( 'rip' );
		$namespaces = $this->getModel( 'namespaces' );

		// Push the models into the view.
		$view->setModel( $model, true );
		$view->setModel( $namespaces );

		// Set the layout
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );
		$view->setLayout($viewLayout);

		// Display the view
		$view->display();

	}

	/**
	 * Show a preview of pages that will be pulled from the wiki.
	 *
	 * @access	public
	 */
	public function preview()
	{
		// Set a default view if none exists.
		if (!JRequest::getCmd( 'view' ) ) {
			JRequest::setVar( 'view', 'rip' );
		}

		$view = $this->getView( 'rip', 'html', '', array( 'base_path'=>$this->_basePath));

		// Get/Create the model
		$model = $this->getModel( 'rip' );
		$namespaces = $this->getModel( 'namespaces' );

		// Configure user state and model state using request data.
		$option = JRequest::getCmd( 'option' );
		$app = JFactory::getApplication();
		$model->setState( 'from',		$app->getUserStateFromRequest( $option.'.from', 'from' ) );
		$model->setState( 'prefix',		$app->getUserStateFromRequest( $option.'.prefix', 'prefix' ) );
		$model->setState( 'namespace',	$app->getUserStateFromRequest( $option.'.namespace', 'namespace' ) );

		// Load data from wiki.
		if (!$model->getPages()) {
			$this->setRedirect( 'index.php', $model->getError(), 'error' );
			$this->redirect();
		}

		// Push the models into the view.
		$view->setModel( $model, true );
		$view->setModel( $namespaces );

		// Set the layout
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );
		$view->setLayout($viewLayout);

		// Display the view
		$view->display();

	}

	/**
	 * Pull pages from the wiki and save them as Joomla content articles.
	 *
	 * @access	public
	 */
	public function rip()
	{
		// Set a default view if none exists.
		if (!JRequest::getCmd( 'view' ) ) {
			JRequest::setVar( 'view', 'rip' );
		}

		$view = $this->getView( 'rip', 'html', '', array( 'base_path'=>$this->_basePath));

		// Get/Create the models
		$model = $this->getModel( 'rip' );

		// Login to the wiki.
		if (!$model->login()) {
			$this->setRedirect( 'index.php', $model->getError(), 'error' );
			$this->redirect();
		}

		// Configure user state and model state using request data.
		$option = JRequest::getCmd( 'option' );
		$app = JFactory::getApplication();
		$model->setState( 'from',		$app->getUserStateFromRequest( $option.'.from', 'from' ) );
		$model->setState( 'prefix',		$app->getUserStateFromRequest( $option.'.prefix', 'prefix' ) );
		$model->setState( 'namespace',	$app->getUserStateFromRequest( $option.'.namespace', 'namespace' ) );
		$model->setState( 'category',	$app->getUserStateFromRequest( $option.'.category', 'category' ) );
		$model->setState( 'update',		$app->getUserStateFromRequest( $option.'.update', 'update' ) );
		$model->setState( 'remove_ns',	$app->getUserStateFromRequest( $option.'.remove_ns', 'remove_ns' ) );
		$model->setState( 'remove_toc',	$app->getUserStateFromRequest( $option.'.remove_toc', 'remove_toc' ) );
		$model->setState( 'keyref',		$app->getUserStateFromRequest( $option.'.keyref', 'keyref' ) );
		$model->setState( 'page_max',	$app->getUserStateFromRequest( $option.'.page_max', 'page_max' ) );

		// Load data from wiki and save it as Joomla articles.
		if (!$model->savePages()) {
			$this->setRedirect( 'index.php', $model->getError(), 'error' );
			$this->redirect();
		}

		// Logout again.
		$model->logout();

		// Push the model into the view (as default)
		$view->setModel($model, true);

		// Set the layout
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );
		$view->setLayout($viewLayout);

		// Display the view
		$view->display();

	}

}
