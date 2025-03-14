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

        switch (App::url()->getType()) {
            case 'post':
                if ((int) $widget->get('where') !== 0 && (int) $widget->get('where') !== 1) {
                    // Don't display for post
                    return '';
                }

                break;
            case 'pages':
                if ((int) $widget->get('where') !== 0 && (int) $widget->get('where') !== 2) {
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
        $counters = Helper::getCounters(
            App::frontend()->context()->posts->getExcerpt() . ' ' . App::frontend()->context()->posts->getContent(),
            ($widget->get('wpm') ? (int) $widget->get('wpm') : (int) $settings->wpm),
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

        // Return final markup
        return $widget->renderDiv((bool) $widget->content_only, implode(' ', ['wordcount', $widget->class]), '', $res);
    }
}
