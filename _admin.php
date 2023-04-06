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

// dead but useful code, in order to have translations
__('Word Count') . __('Counts characters, words and folios, reading time of entry');

// Add menu item in blog menu
dcCore::app()->menu[dcAdmin::MENU_BLOG]->addItem(
    __('Word Count'),
    'plugin.php?p=wordCount',
    [urldecode(dcPage::getPF('wordCount/icon.svg')), urldecode(dcPage::getPF('wordCount/icon-dark.svg'))],
    preg_match('/plugin.php\?p=wordCount(&.*)?$/', $_SERVER['REQUEST_URI']),
    dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([
        dcAuth::PERMISSION_CONTENT_ADMIN,
    ]), dcCore::app()->blog->id)
);

require_once __DIR__ . '/_widgets.php';

if (dcCore::app()->blog->settings->wordcount->wc_active && dcCore::app()->blog->settings->wordcount->wc_autorefresh) {
    // Register REST methods
    dcCore::app()->rest->addFunction('wordCountGetCounters', [restWordCount::class, 'getCounters']);
}

class adminWordCount
{
    public static function adminPostHeaders()
    {
        if (dcCore::app()->blog->settings->wordcount->wc_active) {
            $ret = dcPage::cssModuleLoad('wordCount/css/style.css', 'screen', dcCore::app()->getVersion('wordCount'));
            if (dcCore::app()->blog->settings->wordcount->wc_autorefresh) {
                $interval = (int) (dcCore::app()->blog->settings->wordcount->wc_interval ?? 60);
                $ret .= dcPage::jsJson('wordcount', ['interval' => $interval]) .
                    dcPage::jsModuleLoad('wordCount/js/service.js', dcCore::app()->getVersion('wordCount'));
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
                $countersExcerpt = $details ? libWordCount::getCounters($post->post_excerpt_xhtml, $wpm) : '';
                $countersContent = $details ? libWordCount::getCounters($post->post_content_xhtml, $wpm) : '';
                $text            = ($post->post_excerpt_xhtml != '' ? $post->post_excerpt_xhtml . ' ' : '');
                $text .= $post->post_content_xhtml;
                $countersTotal = libWordCount::getCounters($text, $wpm, ($post->post_excerpt_xhtml != ''));

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

dcCore::app()->addBehaviors([
    // Add behaviour callback for post
    'adminPostForm'    => [adminWordCount::class, 'wordCount'],
    'adminPostHeaders' => [adminWordCount::class, 'adminPostHeaders'],
    // Add behaviour callback for page
    'adminPageForm'    => [adminWordCount::class, 'wordCount'],
    'adminPageHeaders' => [adminWordCount::class, 'adminPostHeaders'],
]);
