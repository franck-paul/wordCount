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

use Dotclear\Helper\Html\Html;

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

require_once __DIR__ . '/_widgets.php';

class tplWordCount
{
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

        return '<?php echo ' . sprintf($f, 'libWordCount::getCounters(dcCore::app()->ctx->posts->getExcerpt()." ".dcCore::app()->ctx->posts->getContent(),' .
            ($wpm ?: 'dcCore::app()->blog->settings->wordcount->wc_wpm') . ',true,' .
            $chars . ',' . $words . ',' . $folios . ',' . $time . ',' . $list . ')') . '; ?>';
    }

    public static function widgetWordCount($w): string
    {
        if ($w->offline) {
            // Widget offline
            return '';
        }

        switch (dcCore::app()->url->type) {
            case 'post':
                if ($w->where != 0 && $w->where != 1) {
                    // Don't display for post
                    return '';
                }

                break;
            case 'pages':
                if ($w->where != 0 && $w->where != 2) {
                    // Don't display for page
                    return '';
                }

                break;
            default:
                // Other contexts, not managed here
                return '';
        }

        // Get widget title
        $res = ($w->title ? $w->renderTitle(Html::escapeHTML($w->title)) . "\n" : '');

        // Get counters
        $counters = libWordCount::getCounters(
            dcCore::app()->ctx->posts->getExcerpt() . ' ' . dcCore::app()->ctx->posts->getContent(),
            ($w->wpm ? (int) $w->wpm : dcCore::app()->blog->settings->wordcount->wc_wpm),
            true,
            $w->chars,
            $w->words,
            $w->folios,
            $w->time,
            $w->list
        );

        // Assemble
        if (!$w->list) {
            $counters = '<p>' . $counters . '</p>' . "\n";
        }
        $res .= $counters;

        // Return final markup
        return $w->renderDiv($w->content_only, 'wordcount ' . $w->class, '', $res);
    }
}

dcCore::app()->tpl->addValue('WordCount', [tplWordCount::class, 'WordCount']);
