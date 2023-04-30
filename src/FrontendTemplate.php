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

class FrontendTemplate
{
    // Register template tag
    //
    // {{tpl:WordCount [attributes]}}
    // with attributes may be one or more of:
    // - chars="0|1" show number of characters (0 = default)
    // - words="0|1" show number of words (1 = default)
    // - folios="0|1" show number of folios (0 = default)
    // - time="0|1" : show estimated reading time (0 = default)
    // - wpm="nnn" : words per minute (blog setting by default)
    // - list="0|1" : use ul/li markup (0 = none)
    //
    // Example : <p><strong>{{tpl:lang reading time:}}</strong> {{tpl:WordCount words="0" time="1"}}</p>

    public static function WordCount($attr): string
    {
        // Check attributes
        $chars  = isset($attr['chars']) ? (int) $attr['chars'] : 0;
        $words  = isset($attr['words']) ? (int) $attr['words'] : 1;
        $folios = isset($attr['folios']) ? (int) $attr['folios'] : 0;
        $time   = isset($attr['time']) ? (int) $attr['time'] : 0;
        $wpm    = isset($attr['wpm']) ? (int) $attr['wpm'] : 0;
        $list   = isset($attr['list']) ? (int) $attr['list'] : 0;
        // Get filters formatter string
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, Helper::class . '::getCounters(dcCore::app()->ctx->posts->getExcerpt()." ".dcCore::app()->ctx->posts->getContent(),' .
            ($wpm ?: 'dcCore::app()->blog->settings->wordcount->wc_wpm') . ',true,' .
            $chars . ',' . $words . ',' . $folios . ',' . $time . ',' . $list . ')') . '; ?>';
    }

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
        $counters = Helper::getCounters(
            dcCore::app()->ctx->posts->getExcerpt() . ' ' . dcCore::app()->ctx->posts->getContent(),
            ($widget->wpm ? (int) $widget->wpm : dcCore::app()->blog->settings->wordcount->wc_wpm),
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
