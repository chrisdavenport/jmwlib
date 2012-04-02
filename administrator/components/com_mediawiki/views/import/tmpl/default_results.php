<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<?php if (isset( $this->pages )) : ?>
<form action="index.php" method="post">
	<fieldset>
		<legend>MediaWiki Import Parameters</legend>

		<table class="admintable">
			<tbody>
				<tr>
					<td class="key">Joomla category</td>
					<td><?php echo $this->lists['categories']; ?></td>
				</tr>
				<tr>
					<td class="key">Update existing pages with the same name?</td>
					<td><?php echo JHTML::_( 'select.booleanlist', 'update', '', '1' ); ?></td>
				</tr>
				<tr>
					<td class="key">Remove namespace from page title?</td>
					<td><?php echo JHTML::_( 'select.booleanlist', 'remove_ns', '', '1' ); ?></td>
				</tr>
				<tr>
					<td class="key">Remove tables of contents from pages?</td>
					<td><?php echo JHTML::_( 'select.booleanlist', 'remove_toc', '', '1' ); ?></td>
				</tr>
				<tr>
					<td class="key">Add help screen key reference?</td>
					<td><?php echo JHTML::_( 'select.booleanlist', 'keyref', '', '1' ); ?></td>
				</tr>
				<tr>
					<td class="key">Add wiki pages to watchlist?</td>
					<td><?php echo JHTML::_( 'select.booleanlist', 'watch', '', '1' ); ?></td>
				</tr>
				<tr>
					<td class="key">Maximum pages to retrieve (0 = all)</td>
					<td><?php echo JHTML::_( 'select.integerlist', 0, 100, 10, 'page_max', '', '10' ); ?></td>
				</tr>
				<tr>
					<td class="key"></td>
					<td><input type="submit" value="Import" /></td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="view" value="import" />
		<input type="hidden" name="from" value="<?php echo $this->state->from; ?>" />
		<input type="hidden" name="prefix" value="<?php echo $this->state->prefix; ?>" />
		<input type="hidden" name="namespace" value="<?php echo $this->state->namespace; ?>" />
		<input type="hidden" name="task" value="importPages" />
		<input type="hidden" name="option" value="<?php echo JRequest::getCmd( 'option' ); ?>" />
	</fieldset>
</form>
<?php endif ?>
