<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<?php if (isset( $this->pages )) : ?>
<fieldset>
	<legend>MediaWiki Import Preview</legend>

	<table class="admintable">
		<tbody>
			<tr>
				<th>ID</th>
				<th>Namespace</th>
				<th>Page title</th>
			</tr>
<?php foreach ($this->pages as $page) : ?>
			<tr>
				<td class="key"><?php echo $page['pageid']; ?></td>
				<td><?php echo $this->nsmap[$page['ns']]; ?></td>
				<td><?php echo $page['title']; ?></td>
			</tr>
<?php endforeach ?>
		</tbody>
	</table>
</fieldset>
<?php endif ?>
