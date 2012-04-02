<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<?php if (isset( $this->results )) : ?>
<h2>Results</h2>
<table>
	<tbody>
		<tr>
			<th>ID</th>
			<th>Namespace</th>
			<th>Page title</th>
		</tr>
<?php foreach ($this->results as $result) : ?>
		<tr>
			<td><?php echo $result['pageid']; ?></td>
			<td><?php echo $this->nsmap[$result['ns']]; ?></td>
			<td><?php echo $result['title']; ?></td>
		</tr>
<?php endforeach ?>
	</tbody>
</table>
<form action="index.php?option=com_mediawiki" method="post">
	<div>Joomla category: <?php echo $this->lists['categories']; ?></div>
	<div>Update existing pages with the same name? <?php echo JHTML::_( 'select.booleanlist', 'update', '', '1' ); ?></div>
	<div>Remove namespace from page title? <?php echo JHTML::_( 'select.booleanlist', 'remove_ns', '', '1' ); ?></div>
	<div>Remove tables of contents from pages? <?php echo JHTML::_( 'select.booleanlist', 'remove_toc', '', '1' ); ?></div>
	<div>Add help screen key reference? <?php echo JHTML::_( 'select.booleanlist', 'keyref', '', '1' ); ?></div>
	<div>Maximum pages to retrieve (0 = all): <?php echo JHTML::_( 'select.integerlist', 0, 100, 10, 'page_max', '', '10' ); ?></div>
	<div><input type="submit" value="Rip" /></div>
	<input type="hidden" name="from" value="<?php echo $this->state->from; ?>" />
	<input type="hidden" name="prefix" value="<?php echo $this->state->prefix; ?>" />
	<input type="hidden" name="namespace" value="<?php echo $this->state->namespace; ?>" />
	<input type="hidden" name="task" value="rip" />
	<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt( 'Itemid' ); ?>" />
</form>
<?php endif ?>
