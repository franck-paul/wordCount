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

if (!defined('DC_RC_PATH')) {return;}

$core->addBehavior('initWidgets', array('widgetsWordCount', 'initWidgets'));

class widgetsWordCount
{
    public static function initWidgets($w)
    {
        // Widget for all series
        $w->create('wordcount', __('Word Count'), array('tplWordCount', 'widgetWordCount'), null, __('Word Count'));
        $w->wordcount->setting('title', __('Title:'), __('Statistics'));

        $w->wordcount->setting('where', __('Display for:'), 0, 'combo',
            array(
                __('Posts and pages') => 0,
                __('Posts only')      => 1,
                __('Page only')       => 2
            )
        );
        $w->wordcount->setting('chars', __('Number of characters'), 0, 'check');
        $w->wordcount->setting('words', __('Number of words'), 1, 'check');
        $w->wordcount->setting('folios', __('Number of folios'), 0, 'check');
        $w->wordcount->setting('time', __('Reading time'), 0, 'check');
        $w->wordcount->setting('wpm', __('Average words per minute (reading):'), '');

        $w->wordcount->setting('list', __('Use <ul>/<li> markup'), 0, 'check');

        $w->wordcount->setting('content_only', __('Content only'), 0, 'check');
        $w->wordcount->setting('class', __('CSS class:'), '');
        $w->wordcount->setting('off', __('Offline'), 0, 'check');
    }
}
