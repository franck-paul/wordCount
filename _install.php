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
    return;
}

$new_version = dcCore::app()->plugins->moduleInfo('wordCount', 'version');
$old_version = dcCore::app()->getVersion('wordCount');

if (version_compare((string) $old_version, $new_version, '>=')) {
    return;
}

try {
    dcCore::app()->blog->settings->addNamespace('wordcount');

    // Default state is active
    dcCore::app()->blog->settings->wordcount->put('wc_active', true, 'boolean', 'Active', false, true);
    dcCore::app()->blog->settings->wordcount->put('wc_details', false, 'boolean', 'Details', false, true);
    dcCore::app()->blog->settings->wordcount->put('wc_wpm', 230, 'integer', 'Average words per minute', false, true);
    dcCore::app()->blog->settings->wordcount->put('wc_autorefresh', true, 'boolean', 'Auto refresh counters', false, true);
    dcCore::app()->blog->settings->wordcount->put('wc_timeout', 60, 'integer', 'Interval between two refresh', false, true);

    dcCore::app()->setVersion('wordCount', $new_version);

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

return false;
