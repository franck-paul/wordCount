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

use Dotclear\App;
use Dotclear\Database\MetaRecord;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Html;
use Dotclear\Plugin\widgets\WidgetsElement;

class FrontendWidgets
{
    /**
     * Render widget
     *
     * @param      WidgetsElement  $widget      The widget
     */
    public static function widgetWordCount(WidgetsElement $widget): string
    {
        if ($widget->offline) {
            // Widget offline
            return '';
        }

        $where = is_numeric($where = $widget->get('where')) ? (int) $where : My::WIDGET_POSTS_AND_PAGES;
        switch (App::url()->getType()) {
            case 'post':
                if ($where !== My::WIDGET_POSTS_AND_PAGES && $where !== My::WIDGET_POSTS_ONLY) {
                    // Don't display for post
                    return '';
                }

                break;
            case 'pages':
                if ($where !== My::WIDGET_POSTS_AND_PAGES && $where !== My::WIDGET_PAGES_ONLY) {
                    // Don't display for page
                    return '';
                }

                break;
            default:
                // Other contexts, not managed here
                return '';
        }

        // Get widget title
        $res = ($widget->title ? $widget->renderTitle(Html::escapeHTML($widget->title)) . "\n" : '');

        // Get counters
        $settings = My::settings();

        if (App::frontend()->context()->posts instanceof MetaRecord) {
            $wpm = is_numeric($wpm = $widget->get('wpm')) ? (int) $wpm : (is_numeric($wpm = $settings->wpm) ? (int) $wpm : My::DEFAULT_WPM);

            $excerpt = is_string($excerpt = App::frontend()->context()->posts->getExcerpt()) ? $excerpt : '';
            $content = is_string($content = App::frontend()->context()->posts->getContent()) ? $content : '';

            $counters = Helper::getCounters(
                $excerpt . ' ' . $content,
                $wpm,
                true,
                (bool) $widget->get('chars'),
                (bool) $widget->get('words'),
                (bool) $widget->get('folios'),
                (bool) $widget->get('time'),
                (bool) $widget->get('list')
            );

            // Assemble
            if (!$widget->get('list')) {
                $counters = (new Para())
                    ->items([
                        (new Text(null, $counters)),
                    ])
                ->render();
            }

            $res .= $counters;
        }

        // Return final markup
        return $widget->renderDiv((bool) $widget->content_only, implode(' ', ['wordcount', $widget->class]), '', $res);
    }
}
