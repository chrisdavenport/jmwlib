<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<form name="adminForm" action="index.php" method="post">
	<fieldset>
		<legend>MediaWiki Select Pages</legend>

		<table class="admintable">
<?php if (isset( $this->tests['error'] )) : ?>
			<tr>
			    <td class="key">Error</td>
			    <td style="color:red;"><?php echo $this->tests['error']; ?></td>
			</tr>
<?php else : ?>
			<tr>
				<td class="key">Page name</td>
				<td><input type="text" name="from" value="<?php echo $this->state->from; ?>" /></td>
			</tr>
			<tr>
				<td class="key">Page prefix</td>
				<td><input type="text" name="prefix" value="<?php echo $this->state->prefix; ?>" /></td>
			</tr>
			<tr>
				<td class="key">Namespace</td>
				<td><?php echo $this->lists['namespaces']; ?></td>
			</tr>
			<tr>
				<td class="key"></td>
				<td><input type="submit" value="Preview" /></td>
			</tr>
<?php endif; ?>
		</table>
		<input type="hidden" name="option" value="<?php echo JRequest::getCmd( 'option' ); ?>" />
		<input type="hidden" name="view" value="import" />
		<input type="hidden" name="task" value="importPreview" />
	</fieldset>
</form>