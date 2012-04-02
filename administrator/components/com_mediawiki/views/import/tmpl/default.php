<?php // no direct access
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title( JText::_( 'JMediaWiki Manager: Import' ), 'generic.png' );
JToolBarHelper::preferences( 'com_mediawiki', '200' );
JToolBarHelper::help( 'screen.mediawiki.import' );

echo $this->loadTemplate( 'setups' );

echo $this->loadTemplate( 'form' );

if (JRequest::getInt( 'show-preview' ) == 1) {
	echo $this->loadTemplate( 'preview' );
	echo $this->loadTemplate( 'results' );
}
