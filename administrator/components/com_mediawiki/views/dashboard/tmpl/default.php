<?php // no direct access
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title( JText::_( 'JMediaWiki Manager: Dashboard' ), 'generic.png' );
JToolBarHelper::preferences( 'com_mediawiki', '200' );
JToolBarHelper::help( 'screen.mediawiki.dashboard' );

?>

<form name="adminForm" action="index.php" method="post">
<?php echo $this->loadTemplate( 'setups' ); ?>
</form>

<fieldset>
	<legend>MediaWiki Information</legend>
	<table class="admintable">
<?php if (isset( $this->tests['error'] )) : ?>
	  <tr>
	    <td class="key">Error</td>
	    <td style="color:red;"><?php echo $this->tests['error']; ?></td>
	  </tr>
<?php else : ?>
	  <tr>
	    <td class="key">Main page</td>
	    <td><?php echo $this->tests['general']['mainpage']; ?></td>
	    <td class="key">Pages</td>
	    <td><?php echo number_format( $this->tests['statistics']['pages'] ); ?></td>
	  </tr>
	  <tr>
	    <td class="key">Base</td>
	    <td><a href="<?php echo $this->tests['general']['base']; ?>" target="_blank"><?php echo $this->tests['general']['base']; ?></a></td>
	    <td class="key">Articles</td>
	    <td><?php echo number_format( $this->tests['statistics']['articles'] ); ?></td>
	  </tr>
	  <tr>
	    <td class="key">Sitename</td>
	    <td><?php echo $this->tests['general']['sitename']; ?></td>
	    <td class="key">Views</td>
	    <td><?php echo number_format( $this->tests['statistics']['views'] ); ?></td>
	  </tr>
	  <tr>
	    <td class="key">Generator</td>
	    <td><?php echo $this->tests['general']['generator']; ?></td>
	    <td class="key">Edits</td>
	    <td><?php echo number_format( $this->tests['statistics']['edits'] ); ?></td>
	  </tr>
	  <tr>
	    <td class="key">Case</td>
	    <td><?php echo $this->tests['general']['case']; ?></td>
	    <td class="key">Images</td>
	    <td><?php echo number_format( $this->tests['statistics']['images'] ); ?></td>
	  </tr>
	  <tr>
	    <td class="key">Rights</td>
	    <td><?php echo $this->tests['general']['rights']; ?></td>
	    <td class="key">Users</td>
	    <td><?php echo number_format( $this->tests['statistics']['users'] ); ?></td>
	  </tr>
	  <tr>
	    <td class="key">Language</td>
	    <td><?php echo $this->tests['general']['lang']; ?></td>
	    <td class="key">Admins</td>
	    <td><?php echo number_format( $this->tests['statistics']['admins'] ); ?></td>
	  </tr>
	  <tr>
	    <td class="key">Fallback 8-bit encoding</td>
	    <td><?php echo $this->tests['general']['fallback8bitEncoding']; ?></td>
	    <td class="key">Jobs</td>
	    <td><?php echo number_format( $this->tests['statistics']['jobs'] ); ?></td>
	  </tr>
	  <tr>
	    <td class="key">Write API</td>
	    <td><?php echo $this->tests['general']['writeapi']; ?></td>
	    <td class="key"></td>
	    <td></td>
	  </tr>
	  <tr>
	    <td class="key">Timezone</td>
	    <td><?php echo $this->tests['general']['timezone']; ?></td>
	    <td class="key"></td>
	    <td></td>
	  </tr>
	  <tr>
	    <td class="key">Time offset</td>
	    <td><?php echo $this->tests['general']['timeoffset']; ?></td>
	    <td class="key"></td>
	    <td></td>
	  </tr>
<?php endif; ?>
	</table>
</fieldset>
