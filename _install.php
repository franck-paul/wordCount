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

if (!dcCore::app()->newVersion(basename(__DIR__), dcCore::app()->plugins->moduleInfo(basename(__DIR__), 'version'))) {
    return;
}

try {
    // Default state is active
    dcCore::app()->blog->settings->wordcount->put('wc_active', true, 'boolean', 'Active', false, true);
    dcCore::app()->blog->settings->wordcount->put('wc_details', false, 'boolean', 'Details', false, true);
    dcCore::app()->blog->settings->wordcount->put('wc_wpm', 230, 'integer', 'Average words per minute', false, true);
    dcCore::app()->blog->settings->wordcount->put('wc_autorefresh', true, 'boolean', 'Auto refresh counters', false, true);
    dcCore::app()->blog->settings->wordcount->put('wc_timeout', 60, 'integer', 'Interval between two refresh', false, true);

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

return false;
