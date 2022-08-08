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
if (!defined('DC_RC_PATH')) {
    return;
}

dcCore::app()->addBehavior('initWidgets', ['widgetsWordCount', 'initWidgets']);

class widgetsWordCount
{
    public static function initWidgets($w)
    {
        // Widget for all series
        $w
            ->create('wordcount', __('Word Count'), ['tplWordCount', 'widgetWordCount'], null, __('Word Count'))
            ->addTitle(__('Statistics'))
            ->setting(
                'where',
                __('Display for:'),
                0,
                'combo',
                [
                    __('Posts and pages') => 0,
                    __('Posts only')      => 1,
                    __('Page only')       => 2,
                ]
            )
            ->setting('chars', __('Number of characters'), 0, 'check')
            ->setting('words', __('Number of words'), 1, 'check')
            ->setting('folios', __('Number of folios'), 0, 'check')
            ->setting('time', __('Reading time'), 0, 'check')
            ->setting('wpm', __('Average words per minute (reading):'), '')
            ->setting('list', __('Use <ul>/<li> markup'), 0, 'check')
            ->addContentOnly()
            ->addClass()
            ->addOffline();
    }
}
