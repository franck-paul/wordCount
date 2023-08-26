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

if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

class BackendRest
{
    /**
     * Serve method to update current counters.
     *
     * @param      array   $get    The get
     *
     * @return     array   The payload.
     */
    public static function getCounters($get): array
    {
        $payload = [
            'ret' => false,
        ];

        $settings = My::settings();
        if ($settings->active) {
            $details = $settings->details;

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
                $wpm = $settings->wpm;

                $countersExcerpt = $details ? Helper::getCounters($excerpt_html, $wpm) : '';
                $countersContent = $details ? Helper::getCounters($content_html, $wpm) : '';

                $text = ($excerpt_html != '' ? $excerpt_html . ' ' : '');
                $text .= $content_html;
                $countersTotal = Helper::getCounters($text, $wpm, ($excerpt != ''));

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
