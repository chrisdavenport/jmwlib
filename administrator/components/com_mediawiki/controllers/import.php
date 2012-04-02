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
 * JMediawiki Component Import Controller
 *
 * @package		JMediaWiki
 */
class MediawikiControllerImport extends MediawikiController
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
			JRequest::setVar( 'view', 'import' );
		}

		$view = $this->getView( 'import', 'html', '', array( 'base_path'=>$this->_basePath));

		// Get/Create the model
		$model = $this->getModel( 'import' );

		// Configure user state and model state using request data.
		$option = JRequest::getCmd( 'option' );
		$app = JFactory::getApplication();
		$model->setState( 'from',		$app->getUserStateFromRequest( $option.'.from', 'from' ) );
		$model->setState( 'prefix',		$app->getUserStateFromRequest( $option.'.prefix', 'prefix' ) );
		$model->setState( 'namespace',	$app->getUserStateFromRequest( $option.'.namespace', 'namespace' ) );

		// Push the models into the view.
		$view->setModel( $model, true );

		// Set the layout
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );
		$view->setLayout($viewLayout);

		// Display the view
		$view->display();
	}

	/**
	 * Show a preview of pages that will be imported from the wiki.
	 *
	 * @access	public
	 */
	public function importPreview()
	{
		// Set a default view.
		JRequest::setVar( 'view', 'import' );

		$view = $this->getView( 'import', 'html', '', array( 'base_path'=>$this->_basePath));

		// Get/Create the model
		$model = $this->getModel( 'import' );

		// Configure user state and model state using request data.
		$option = JRequest::getCmd( 'option' );
		$app = JFactory::getApplication();
		$model->setState( 'from',		$app->getUserStateFromRequest( $option.'.from', 'from' ) );
		$model->setState( 'prefix',		$app->getUserStateFromRequest( $option.'.prefix', 'prefix' ) );
		$model->setState( 'namespace',	$app->getUserStateFromRequest( $option.'.namespace', 'namespace' ) );

		// Set flag to tell view to show preview.
		JRequest::setVar( 'show-preview', 1 );

		// Push the models into the view.
		$view->setModel( $model, true );

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
	public function importPages()
	{
		// Set a default view if none exists.
		if (!JRequest::getCmd( 'view' ) ) {
			JRequest::setVar( 'view', 'import' );
		}

		$option = JRequest::getCmd( 'option' );
		$view = $this->getView( 'import', 'html', '', array( 'base_path'=>$this->_basePath));

		// Get/Create the models
		$model = $this->getModel( 'import' );

		// Login to the wiki.
		try {
			$model->login();
		}
		catch (Exception $e) {
			$this->setRedirect( 'index.php?option='.$option.'&view=dashboard', $e->getMessage(), 'error' );
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
		$model->setState( 'watch',		$app->getUserStateFromRequest( $option.'.watch', 'watch' ) );

		// Load data from wiki and save it as Joomla articles.
		try {
			$import_count = $model->savePages();
			$this->setRedirect( 'index.php?option='.$option.'&view=dashboard', 'Successfully imported '.$import_count.' wiki pages' );
		}
		catch (Exception $e) {
			$this->setRedirect( 'index.php?option='.$option.'&view=dashboard', $e->getMessage(), 'error' );
		}

		// Logout again.
		$model->logout();

		$this->redirect();
	}

}
