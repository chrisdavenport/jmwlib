<?php // no direct access
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title( JText::_( 'JMediaWiki Manager: Namespaces' ), 'generic.png' );
JToolBarHelper::preferences( 'com_mediawiki', '200' );
JToolBarHelper::help( 'screen.mediawiki.namespaces' );

?>

<form name="adminForm" action="index.php" method="post">
<?php echo $this->loadTemplate( 'setups' ); ?>

	<fieldset>
		<legend>MediaWiki Namespaces</legend>
		<table class="admintable">
<?php if (isset( $this->tests['error'] )) : ?>
				<tr>
				    <td class="key">Error</td>
				    <td style="color:red;"><?php echo $this->tests['error']; ?></td>
				</tr>
<?php else : ?>
			<thead>
				<tr>
					<th>Id</th>
					<th>Namespace</th>
					<th>Alias</th>
				</tr>
<?php
$i = 0;
foreach ($this->namespaces as $namespace) :
	$i++;
	if ($i < $this->pagination->limitstart ||
		$i >= $this->pagination->limitstart + $this->pagination->limit) {
		continue;
	}
	$namespace_url = $this->params->get( 'wiki_url' ).'/index.php?title=Special%3AAllPages&from=&namespace='.$namespace['id'];
?>
				<tr>
				    <td class="key"><?php echo $namespace['id']; ?></td>
				    <td><a href="<?php echo $namespace_url; ?>" target="_blank" ><?php echo $namespace['*']; ?></a></td>
				    <td>
<?php
foreach ($this->nsaliases as $alias) {
	if ($alias['id'] == $namespace['id']) {
		echo $alias['*'];
	}
}
?>
				    </td>
				</tr>
<?php endforeach; ?>
			</thead>
			<tfoot>
				<tr>
					<td colspan="9">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
<?php endif; ?>
		</table>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo JRequest::getCmd( 'option' ); ?>" />
	<input type="hidden" name="view" value="<?php echo JRequest::getCmd( 'view' ); ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
