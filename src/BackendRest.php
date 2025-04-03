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

class BackendRest
{
    /**
     * Serve method to update current counters.
     *
     * @param      array<string, string>   $get    The get
     * @param      array<string, string>   $post   The post
     *
     * @return     array<string, mixed>    The payload.
     */
    public static function getCounters(array $get, array $post): array
    {
        $payload = [
            'ret' => false,
        ];

        $settings = My::settings();
        if ($settings->active) {
            $details = $settings->details;

            $excerpt = $post['excerpt'] ?? null;
            $content = $post['content'] ?? null;
            $format  = $post['format']  ?? 'xhtml';

            // Convert textarea's content to HTML
            switch ($format) {
                case 'wiki':
                    App::filter()->initWikiPost();

                    break;

                case 'markdown':
                case 'xhtml':
                default:
                    break;
            }

            if ($excerpt) {
                $excerpt_html = App::formater()->callEditorFormater('dcLegacyEditor', $format, $excerpt);
                $excerpt_html = App::filter()->HTMLfilter($excerpt_html);
            } else {
                $excerpt_html = '';
            }

            if ($content) {
                $content_html = App::formater()->callEditorFormater('dcLegacyEditor', $format, $content);
                $content_html = App::filter()->HTMLfilter($content_html);
            } else {
                $content_html = '';
            }

            # --BEHAVIOR-- coreContentFilter -- string, array<int, array<int, string>> -- since 2.34
            App::behavior()->callBehavior('coreContentFilter', 'post', [
                [&$excerpt, $format],
                [&$content, $format],
                [&$excerpt_html, 'html'],
                [&$content_html, 'html'],
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
                    $html .= __('Excerpt:') . ' ' . ($countersExcerpt ?: '0') . '<br>';
                    $html .= __('Content:') . ' ' . ($countersContent ?: '0') . '<br>';
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
