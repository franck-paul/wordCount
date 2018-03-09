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

if (!defined('DC_CONTEXT_ADMIN')) {return;}

$new_version = $core->plugins->moduleInfo('wordCount', 'version');
$old_version = $core->getVersion('wordCount');

if (version_compare($old_version, $new_version, '>=')) {
    return;
}

try
{
    if (version_compare(DC_VERSION, '2.4', '<')) {
        throw new Exception('Word Count requires Dotclear 2.4');
    }

    $core->blog->settings->addNamespace('wordcount');

    // Default state is active
    $core->blog->settings->wordcount->put('wc_active', true, 'boolean', 'Active', false, true);
    $core->blog->settings->wordcount->put('wc_details', false, 'boolean', 'Details', false, true);
    $core->blog->settings->wordcount->put('wc_wpm', 230, 'integer', 'Average words per minute', false, true);

    $core->setVersion('wordCount', $new_version);

    return true;
} catch (Exception $e) {
    $core->error->add($e->getMessage());
}
return false;
