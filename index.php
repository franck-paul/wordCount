<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of wordCount, a plugin for Dotclear 2.
# 
# Copyright (c) 2012 Franck Paul and contributors
# carnet.franck.paul@gmail.com
# 
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_CONTEXT_ADMIN')) { exit; }

// Getting current parameters
$wc_active = (boolean)$core->blog->settings->wordcount->wc_active;
$wc_details = (boolean)$core->blog->settings->wordcount->wc_details;

// Saving new configuration
if (!empty($_POST['saveconfig'])) {
	try
	{
		$core->blog->settings->addNamespace('wordcount');

		$wc_active = (empty($_POST['active']))?false:true;
		$wc_details = (empty($_POST['details']))?false:true;
		$core->blog->settings->wordcount->put('wc_active',$wc_active,'boolean');
		$core->blog->settings->wordcount->put('wc_details',$wc_details,'boolean');
		$core->blog->triggerBlog();
		$msg = __('Configuration successfully updated.');
	}
	catch (Exception $e)
	{
		$core->error->add($e->getMessage());
	}
}
?>
<html>
<head>
	<title><?php echo __('Word Count'); ?></title>
</head>

<body>
<h2><?php echo html::escapeHTML($core->blog->name); ?> &gt; <?php echo __('Word Count'); ?></h2>

<?php if (!empty($msg)) dcPage::message($msg); ?>

<div id="wc_options">
	<form method="post" action="plugin.php">
	<fieldset>
		<legend><?php echo __('Plugin activation'); ?></legend>
		<p class="field">
			<?php echo form::checkbox('active', 1, $wc_active); ?>
			<label class="classic" for="active"><?php echo __('Enable Word Count for this blog'); ?></label>
		</p>
	</fieldset>
	<fieldset>
		<legend><?php echo __('Options'); ?></legend>
		<p class="field">
			<?php echo form::checkbox('details', 1, $wc_details); ?>
			<label class="classic" for="details"><?php echo __('Show details (excerpt and content)'); ?></label>
		</p>
	</fieldset>

	<p><input type="hidden" name="p" value="wordCount" />
	<?php echo $core->formNonce(); ?>
	<input type="submit" name="saveconfig" value="<?php echo __('Save configuration'); ?>" />
	</p>
	</form>
</div>

</body>
</html>