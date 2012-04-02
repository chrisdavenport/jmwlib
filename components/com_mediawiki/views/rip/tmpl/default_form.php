<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<h2>Request a rip</h2>
<form action="index.php?option=com_mediawiki" method="post">
	<div>Page name: <input type="text" name="from" value="<?php echo $this->state->from; ?>" /></div>
	<div>Page prefix: <input type="text" name="prefix" value="<?php echo $this->state->prefix; ?>" /></div>
	<div>Namespace: <?php echo $this->lists['namespaces']; ?></div>
	<div><input type="submit" value="Preview" /></div>
	<input type="hidden" name="task" value="preview" />
	<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt( 'Itemid' ); ?>" />
</form>
