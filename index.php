<?php
/**
 * @brief wordCount, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('DC_CONTEXT_ADMIN')) {
    exit;
}

// Getting current parameters
$wc_active      = (bool) dcCore::app()->blog->settings->wordcount->wc_active;
$wc_details     = (bool) dcCore::app()->blog->settings->wordcount->wc_details;
$wc_wpm         = (int) (dcCore::app()->blog->settings->wordcount->wc_wpm ?? 230);
$wc_autorefresh = (bool) dcCore::app()->blog->settings->wordcount->wc_autorefresh;
$wc_interval    = (int) (dcCore::app()->blog->settings->wordcount->wc_interval ?? 60);

// Saving new configuration
if (!empty($_POST['saveconfig'])) {
    try {
        dcCore::app()->blog->settings->addNamespace('wordcount');

        $wc_active   = (empty($_POST['active'])) ? false : true;
        $wc_details  = (empty($_POST['details'])) ? false : true;
        $wc_wpm      = (int) $_POST['wpm'];
        $wc_interval = (int) $_POST['interval'];
        dcCore::app()->blog->settings->wordcount->put('wc_active', $wc_active, 'boolean');
        dcCore::app()->blog->settings->wordcount->put('wc_details', $wc_details, 'boolean');
        dcCore::app()->blog->settings->wordcount->put('wc_wpm', ($wc_wpm ?: 230), 'integer');
        dcCore::app()->blog->settings->wordcount->put('wc_autorefresh', $wc_autorefresh, 'boolean');
        dcCore::app()->blog->settings->wordcount->put('wc_interval', ($wc_interval ?: 60), 'integer');
        dcCore::app()->blog->triggerBlog();
        $msg = __('Configuration successfully updated.');
    } catch (Exception $e) {
        dcCore::app()->error->add($e->getMessage());
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
    [
        html::escapeHTML(dcCore::app()->blog->name) => '',
        __('Word Count')                            => '',
    ]
);
?>

<?php if (!empty($msg)) {
    dcPage::success($msg);
}
?>

<div id="wc_options">
  <form method="post" action="<?php echo $p_url; ?>">
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
    <label for="wpm" class="classic"><?php echo __('Average words per minute (reading, usually 230):'); ?></label>
    <?php echo form::number('wpm', 1, 9999, (string) $wc_wpm); ?>
  </p>
  <p>
    <?php echo form::checkbox('autorefresh', 1, $wc_autorefresh); ?>
    <label class="classic" for="autorefresh"><?php echo __('Auto refresh counters'); ?></label>
  </p>
  <p>
    <label for="interval" class="classic"><?php echo __('Autorefresh interval in seconds (usually 60):'); ?></label>
    <?php echo form::number('interval', 15, 999, (string) $wc_interval); ?>
  </p>

  <p><input type="hidden" name="p" value="wordCount" />
  <?php echo dcCore::app()->formNonce(); ?>
  <input type="submit" name="saveconfig" value="<?php echo __('Save configuration'); ?>" />
  </p>
  </form>
</div>

</body>
</html>
