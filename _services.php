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

class restWordCount
{
    /**
     * Serve method to update current counters.
     *
     * @param      dcCore  $core   The core
     * @param      array   $get    The get
     *
     * @return     xmlTag  The xml tag.
     */
    public static function getCounters($core, $get)
    {
        global $core;

        $rsp      = new xmlTag('check');
        $rsp->ret = false;

        if ($core->blog->settings->wordcount->wc_active) {
            $details = $core->blog->settings->wordcount->wc_details;

            $excerpt = $_GET['excerpt'] ?? null;
            $content = $_GET['content'] ?? null;
            $format  = $_GET['format']  ?? 'xhtml';

            // Convert textarea's content to HTML
            switch ($format) {
                case 'wiki':
                    $core->initWikiPost();

                    break;

                case 'markdown':
                case 'xhtml':
                default:
                    break;
            }

            if ($excerpt) {
                $excerpt_html = $core->callFormater($format, $excerpt);
                $excerpt_html = $core->HTMLfilter($excerpt_html);
            } else {
                $excerpt_html = '';
            }

            if ($content) {
                $content_html = $core->callFormater($format, $content);
                $content_html = $core->HTMLfilter($content_html);
            } else {
                $content_html = '';
            }
            # --BEHAVIOR-- coreAfterPostContentFormat
            $core->callBehavior('coreAfterPostContentFormat', [
                'excerpt'       => &$excerpt,
                'content'       => &$content,
                'excerpt_xhtml' => &$excerpt_html,
                'content_xhtml' => &$content_html,
            ]);

            $html = '';

            if ($excerpt !== null || $content !== null) {
                $wpm = $core->blog->settings->wordcount->wc_wpm;

                $countersExcerpt = $details ? libWordCount::getCounters($excerpt_html, $wpm) : '';
                $countersContent = $details ? libWordCount::getCounters($content_html, $wpm) : '';

                $text = ($excerpt_html != '' ? $excerpt_html . ' ' : '');
                $text .= $content_html;
                $countersTotal = libWordCount::getCounters($text, $wpm, ($excerpt != ''));

                if ($details) {
                    $html .= __('Excerpt:') . ' ' . ($countersExcerpt ?: '0') . '<br />';
                    $html .= __('Content:') . ' ' . ($countersContent ?: '0') . '<br />';
                    $html .= __('Total:') . ' ' . ($countersTotal ?: '0');
                } else {
                    $html .= __('Counters:') . ' ' . ($countersTotal ?: '0');
                }
            } else {
                $html .= __('Counters:') . ' ' . '0';
            }

            $rsp->html = $html;
            $rsp->ret  = true;
        }

        return $rsp;
    }
}
