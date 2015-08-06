<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of wordCount, a plugin for Dotclear 2.
#
# Copyright (c) Franck Paul and contributors
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
$wc_wpm = (integer)$core->blog->settings->wordcount->wc_wpm;

// Saving new configuration
if (!empty($_POST['saveconfig'])) {
	try
	{
		$core->blog->settings->addNamespace('wordcount');

		$wc_active = (empty($_POST['active']))?false:true;
		$wc_details = (empty($_POST['details']))?false:true;
		$core->blog->settings->wordcount->put('wc_active',$wc_active,'boolean');
		$core->blog->settings->wordcount->put('wc_details',$wc_details,'boolean');
		if (!empty($_POST['wpm'])) {
			$wc_wpm = (integer)$_POST['wpm'];
		} else {
			$wc_wpm = 0;
		}
		$core->blog->settings->wordcount->put('wc_wpm',($wc_wpm ? $wc_wpm : 230),'integer');
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
<?php
	echo dcPage::breadcrumb(
		array(
			html::escapeHTML($core->blog->name) => '',
			__('Word Count') => ''
		));
?>

<?php if (!empty($msg)) dcPage::success($msg); ?>

<div id="wc_options">
	<form method="post" action="plugin.php">
	<p>
		<?php echo form::checkbox('active', 1, $wc_active); ?>
		<label class="classic" for="active"><?php echo __('Enable Word Count for this blog'); ?></label>
	</p>
	<h3><?php echo __('Options'); ?></h3>
	<p>
		<?php echo form::checkbox('details', 1, $wc_details); ?>
		<label class="classic" for="details"><?php echo __('Show details (excerpt and content)'); ?></label>
	</p>
	<p>
		<label for="wpm" class="classic"><?php echo __('Average words per minute (reading):'); ?></label>
		<?php echo form::field('wpm',3,4,(integer) $wc_wpm); ?>
	</p>
	<p class="form-note"><?php echo __('Leave empty for default (230 words per minute)'); ?></p>

	<p><input type="hidden" name="p" value="wordCount" />
	<?php echo $core->formNonce(); ?>
	<input type="submit" name="saveconfig" value="<?php echo __('Save configuration'); ?>" />
	</p>
	</form>
</div>

</body>
</html>
