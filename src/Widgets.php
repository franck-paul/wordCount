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

declare(strict_types=1);

namespace Dotclear\Plugin\wordCount;

use Dotclear\Plugin\widgets\WidgetsStack;

class Widgets
{
    public static function initWidgets(WidgetsStack $w): string
    {
        // Widget for all series
        $w
            ->create('wordcount', __('Word Count'), FrontendWidgets::widgetWordCount(...), null, __('Word Count'), My::id())
            ->addTitle(__('Statistics'))
            ->setting(
                'where',
                __('Display for:'),
                My::WIDGET_POSTS_AND_PAGES,
                'combo',
                [
                    __('Posts and pages') => My::WIDGET_POSTS_AND_PAGES,
                    __('Posts only')      => My::WIDGET_POSTS_ONLY,
                    __('Page only')       => My::WIDGET_PAGES_ONLY,
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

        return '';
    }
}
