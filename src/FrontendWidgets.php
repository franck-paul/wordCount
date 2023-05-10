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

use dcCore;
use Dotclear\Helper\Html\Html;
use Dotclear\Plugin\widgets\WidgetsElement;

class FrontendWidgets
{
    /**
     * Render widget
     *
     * @param      \Dotclear\Plugin\widgets\WidgetsElement  $widget      The widget
     *
     * @return     string
     */
    public static function widgetWordCount(WidgetsElement $widget): string
    {
        if ($widget->offline) {
            // Widget offline
            return '';
        }

        switch (dcCore::app()->url->type) {
            case 'post':
                if ($widget->where != 0 && $widget->where != 1) {
                    // Don't display for post
                    return '';
                }

                break;
            case 'pages':
                if ($widget->where != 0 && $widget->where != 2) {
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
        $settings = dcCore::app()->blog->settings->get(My::id());
        $counters = Helper::getCounters(
            dcCore::app()->ctx->posts->getExcerpt() . ' ' . dcCore::app()->ctx->posts->getContent(),
            ($widget->wpm ? (int) $widget->wpm : $settings->wpm),
            true,
            $widget->chars,
            $widget->words,
            $widget->folios,
            $widget->time,
            $widget->list
        );

        // Assemble
        if (!$widget->list) {
            $counters = '<p>' . $counters . '</p>' . "\n";
        }
        $res .= $counters;

        // Return final markup
        return $widget->renderDiv($widget->content_only, 'wordcount ' . $widget->class, '', $res);
    }
}
