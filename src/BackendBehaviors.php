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
use dcPage;

class BackendBehaviors
{
    public static function adminPostHeaders()
    {
        if (dcCore::app()->blog->settings->wordcount->wc_active) {
            $ret = dcPage::cssModuleLoad(My::id() . '/css/style.css', 'screen', dcCore::app()->getVersion(My::id()));
            if (dcCore::app()->blog->settings->wordcount->wc_autorefresh) {
                $interval = (int) (dcCore::app()->blog->settings->wordcount->wc_interval ?? 60);
                $ret .= dcPage::jsJson('wordcount', ['interval' => $interval]) .
                    dcPage::jsModuleLoad(My::id() . '/js/service.js', dcCore::app()->getVersion('wordCount'));
            }

            return $ret;
        }
    }

    public static function wordCount($post)
    {
        if (dcCore::app()->blog->settings->wordcount->wc_active) {
            $details = dcCore::app()->blog->settings->wordcount->wc_details;
            echo '<div class="wordcount"><details open><summary>' . __('Word Count') . '</summary><p>';
            if ($post != null) {
                $wpm             = dcCore::app()->blog->settings->wordcount->wc_wpm;
                $countersExcerpt = $details ? Helper::getCounters($post->post_excerpt_xhtml, $wpm) : '';
                $countersContent = $details ? Helper::getCounters($post->post_content_xhtml, $wpm) : '';
                $text            = ($post->post_excerpt_xhtml != '' ? $post->post_excerpt_xhtml . ' ' : '');
                $text .= $post->post_content_xhtml;
                $countersTotal = Helper::getCounters($text, $wpm, ($post->post_excerpt_xhtml != ''));

                if ($details) {
                    echo __('Excerpt:') . ' ' . ($countersExcerpt ?: '0') . '<br />';
                    echo __('Content:') . ' ' . ($countersContent ?: '0') . '<br />';
                    echo __('Total:') . ' ' . ($countersTotal ?: '0');
                } else {
                    echo __('Counters:') . ' ' . ($countersTotal ?: '0');
                }
            } else {
                echo __('Counters:') . ' ' . '0';
            }
            echo '</p></details></div>';
        }
    }
}
