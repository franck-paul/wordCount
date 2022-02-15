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
$_menu['Blog']->addItem(
    __('Word Count'),
    'plugin.php?p=wordCount',
    [urldecode(dcPage::getPF('wordCount/icon.svg')), urldecode(dcPage::getPF('wordCount/icon-dark.svg'))],
    preg_match('/plugin.php\?p=wordCount(&.*)?$/', $_SERVER['REQUEST_URI']),
    $core->auth->check('contentadmin', $core->blog->id)
);

require __DIR__ . '/_widgets.php';

// Add behaviour callback for post
$core->addBehavior('adminPostForm', ['adminWordCount', 'wordCount']);
$core->addBehavior('adminPostHeaders', ['adminWordCount', 'adminPostHeaders']);
// Add behaviour callback for page
$core->addBehavior('adminPageForm', ['adminWordCount', 'wordCount']);
$core->addBehavior('adminPageHeaders', ['adminWordCount', 'adminPostHeaders']);

if ($core->blog->settings->wordcount->wc_active && $core->blog->settings->wordcount->wc_autorefresh) {
    // Register REST methods
    $core->rest->addFunction('wordCountGetCounters', ['restWordCount', 'getCounters']);
}

class adminWordCount
{
    public static function adminPostHeaders()
    {
        global $core;

        if ($core->blog->settings->wordcount->wc_active) {
            $ret = dcPage::cssModuleLoad('wordCount/style.css', 'screen', $core->getVersion('wordCount'));
            if ($core->blog->settings->wordcount->wc_autorefresh) {
                $ret .= dcPage::jsModuleLoad('wordCount/js/service.js', $core->getVersion('wordCount'));
            }

            return $ret;
        }
    }

    public static function wordCount($post)
    {
        global $core;

        if ($core->blog->settings->wordcount->wc_active) {
            $details = $core->blog->settings->wordcount->wc_details;
            echo '<div class="wordcount"><details open><summary>' . __('Word Count') . '</summary><p>';
            if ($post != null) {
                $wpm             = $core->blog->settings->wordcount->wc_wpm;
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
