<?php
/**
 * @version		$Id:$
 * @package		JMediaWiki
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL 2 or later.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Content plugin to pull in a page from a MediaWiki instance.
 *
 * @package		JMediaWiki
 */
class plgContentMediawiki extends JPlugin
{
	/**
	 * Prepare content method
	 *
	 * Method is called by the view
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 */
	public function onPrepareContent( $article, $params, $limitstart )
	{
		// Simple performance check to determine whether plugin should process further.
		if (JString::strpos( $article->text, 'mediawiki' ) === false) {
			return true;
		}

		// Get plugin info
		$plugin = JPluginHelper::getPlugin( 'content', 'mediawiki' );

		// Expression to search for.
	 	$regex = '/{mediawiki\s*.*?}/i';

		// Check whether plugin has been unpublished.
	 	$pluginParams = new JParameter( $plugin->params );
	 	if (!$pluginParams->get( 'enabled', 1 )) {
			$article->text = preg_replace( $regex, '', $article->text );
			return true;
		}

	 	// Find all instances of plugin and put in $matches.
		preg_match_all( $regex, $article->text, $matches );

		// Number of instances of the MediaWiki plugin.
	 	$count = count( $matches[0] );

	 	// Plugin only processes if there are any instances of the plugin in the text.
	 	if ($count) {

	 		jimport( 'jmwlib.jmwwiki' );

			// Get a wiki instance.
			$params = JComponentHelper::getParams( 'com_mediawiki' );
			$wiki	= jmwWiki::getInstance( JURI::getInstance( $params->get( 'wiki_url' ) ) );

			// Determine options for page processing.
			$options = array();
			$options['remove_toc']			= $pluginParams->get( 'remove_toc', true );
			$options['remove_img_links']	= $pluginParams->get( 'remove_img_links', true );

			// Loop through all plugin instances.
			for ($i=0; $i < $count; $i++) {
		 		$load = str_replace( 'mediawiki', '', $matches[0][$i] );
		 		$load = str_replace( '{', '', $load );
		 		$load = str_replace( '}', '', $load );
		 		$load = trim( $load );

		 		// Try to retrieve wiki page by name.
		 		try {
					$page_text = $wiki->getPageProcessed( $load, $options );
		 		}
		 		catch (Exception $e) {
					$page_text = 'Error: Cannot retrieve wiki page '.$load;
					$app = JFactory::getApplication();
					$app->enqueueMessage( $page_text, 'error' );
		 		}

		 		// Replace tag with page text from the wiki.
				$article->text 	= str_replace( $matches[0][$i], $page_text, $article->text );
		 	}

		  	// Remove tags that didn't get matched for some reason.
			$article->text = preg_replace( $regex, '', $article->text );
	 	}

		return true;
	}

}