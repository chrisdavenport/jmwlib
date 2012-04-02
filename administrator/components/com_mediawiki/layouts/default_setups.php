<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$password = $this->params->get( 'wiki_password' );
$password_set = '<font style="color:green;">Has been set</font>';
$password_not_set = '<font style="color:red;">Has not been set</font>';

$wiki_url = $this->params->get( 'wiki_url' );
?>
<fieldset>
	<legend>MediaWiki Setups</legend>
	<table class="admintable">
	  <tr>
	    <td class="key">MediaWiki URL</td>
	    <td><?php echo $wiki_url; ?></td>
	    <td class="key">Username</td>
	    <td><?php echo $this->params->get( 'wiki_user' ); ?></td>
	    <td class="key">Password</td>
	    <td><?php echo ($password == '') ? $password_not_set : $password_set; ?></td>
	    <td class="key">API URL</td>
	    <td><a href="<?php echo $wiki_url; ?>/api.php" target="_blank"><?php echo $wiki_url; ?>/api.php</a></td>
	  </tr>
	</table>
</fieldset>
