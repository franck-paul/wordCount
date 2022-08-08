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
     * @param      array   $get    The get
     *
     * @return     The payload.
     */
    public static function getCounters($get)
    {
        $payload = [
            'ret' => false,
        ];

        if (dcCore::app()->blog->settings->wordcount->wc_active) {
            $details = dcCore::app()->blog->settings->wordcount->wc_details;

            $excerpt = $get['excerpt'] ?? null;
            $content = $get['content'] ?? null;
            $format  = $get['format']  ?? 'xhtml';

            // Convert textarea's content to HTML
            switch ($format) {
                case 'wiki':
                    dcCore::app()->initWikiPost();

                    break;

                case 'markdown':
                case 'xhtml':
                default:
                    break;
            }

            if ($excerpt) {
                $excerpt_html = dcCore::app()->callFormater($format, $excerpt);
                $excerpt_html = dcCore::app()->HTMLfilter($excerpt_html);
            } else {
                $excerpt_html = '';
            }

            if ($content) {
                $content_html = dcCore::app()->callFormater($format, $content);
                $content_html = dcCore::app()->HTMLfilter($content_html);
            } else {
                $content_html = '';
            }
            # --BEHAVIOR-- coreAfterPostContentFormat
            dcCore::app()->callBehavior('coreAfterPostContentFormat', [
                'excerpt'       => &$excerpt,
                'content'       => &$content,
                'excerpt_xhtml' => &$excerpt_html,
                'content_xhtml' => &$content_html,
            ]);

            $html = '';

            if ($excerpt !== null || $content !== null) {
                $wpm = dcCore::app()->blog->settings->wordcount->wc_wpm;

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

            $payload = [
                'ret'  => true,
                'html' => $html,
            ];
        }

        return $payload;
    }
}
