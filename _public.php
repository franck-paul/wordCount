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

require dirname(__FILE__) . '/_widgets.php';

$core->tpl->addValue('WordCount', ['tplWordCount', 'WordCount']);

class tplWordCount
{
    public static function WordCount($attr)
    {
        // Check attributes
        $chars  = isset($attr['chars']) ? (integer) $attr['chars'] : 0;
        $words  = isset($attr['words']) ? (integer) $attr['words'] : 1;
        $folios = isset($attr['folios']) ? (integer) $attr['folios'] : 0;
        $time   = isset($attr['time']) ? (integer) $attr['time'] : 0;
        $wpm    = isset($attr['wpm']) ? (integer) $attr['wpm'] : 0;
        $list   = isset($attr['list']) ? (integer) $attr['list'] : 0;
        // Get filters formatter string
        $f = $GLOBALS['core']->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'libWordCount::getCounters(
      $_ctx->posts->getExcerpt()." ".$_ctx->posts->getContent(),' .
            ($wpm ?: '$core->blog->settings->wordcount->wc_wpm') . ',true,' .
            $chars . ',' . $words . ',' . $folios . ',' . $time . ',' . $list . ')') . '; ?>';
    }

    public static function widgetWordCount($w)
    {
        global $core, $_ctx;

        if ($w->offline)
        // Widget offline
        {
            return;
        }

        switch ($core->url->type) {
            case 'post':
                if ($w->where != 0 && $w->where != 1) {
                    // Don't display for post
                    return;
                }
                break;
            case 'pages':
                if ($w->where != 0 && $w->where != 2) {
                    // Don't display for page
                    return;
                }
                break;
            default:
                // Other contexts, not managed here
                return;
        }

        // Get widget title
        $res = ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) . "\n" : '');

        // Get counters
        $counters = libWordCount::getCounters(
            $_ctx->posts->getExcerpt() . " " . $_ctx->posts->getContent(),
            ($w->wpm ? (integer) $w->wpm : $core->blog->settings->wordcount->wc_wpm),
            true, $w->chars, $w->words, $w->folios, $w->time, $w->list);

        // Assemble
        if (!$w->list) {
            $counters = '<p>' . $counters . '</p>' . "\n";
        }
        $res .= $counters;

        // Return final markup
        return $w->renderDiv($w->content_only, 'wordcount ' . $w->class, '', $res);
    }
}
