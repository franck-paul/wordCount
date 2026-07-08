<?php

/**
 * @brief wordCount, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\wordCount;

use Dotclear\App;
use Dotclear\Database\MetaRecord;
use Dotclear\Helper\Html\Form\Details;
use Dotclear\Helper\Html\Form\Div;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Single;
use Dotclear\Helper\Html\Form\Summary;
use Dotclear\Helper\Html\Form\Text;

class BackendBehaviors
{
    /**
     * adminPostHeaders behavior callback
     */
    public static function adminPostHeaders(): string
    {
        $settings = My::settings();
        if ($settings->getBool('active')) {
            $ret = My::cssLoad('style.css');
            if ($settings->getBool('autorefresh')) {
                $timeout = $settings->getInt('timeout', false) ?: My::DEFAULT_INTERVAL;
                $ret .= App::backend()->page()->jsJson('wordcount', ['timeout' => $timeout]) .
                    My::jsLoad('service.js');
            }

            return $ret;
        }

        return '';
    }

    /**
     * wordCount  behavior callback
     *
     * @param      MetaRecord|null  $post   The post
     */
    public static function wordCount(?MetaRecord $post): string
    {
        $settings = My::settings();
        if ($settings->getBool('active')) {
            $details = $settings->getBool('details');
            $infos   = [];
            if ($post instanceof MetaRecord) {
                $wpm = $settings->getInt('wpm', false) ?: My::DEFAULT_WPM;

                $excerpt = $post->strField('post_excerpt_xhtml');
                $content = $post->strField('post_content_xhtml');

                $countersExcerpt = $details ? Helper::getCounters($excerpt, $wpm) : '';
                $countersContent = $details ? Helper::getCounters($content, $wpm) : '';

                $text = implode(' ', array_filter([$excerpt, $content]));

                $countersTotal = Helper::getCounters($text, $wpm, ($excerpt !== ''));

                if ($details) {
                    $infos[] = __('Excerpt:') . ' ' . ($countersExcerpt ?: '0');
                    $infos[] = __('Content:') . ' ' . ($countersContent ?: '0');
                    $infos[] = __('Total:') . ' ' . ($countersTotal ?: '0');
                } else {
                    $infos[] = __('Counters:') . ' ' . ($countersTotal ?: '0');
                }
            } else {
                $infos[] = __('Counters:') . ' ' . '0';
            }

            echo (new Div())
                ->class('wordcount')
                ->items([
                    (new Details())
                        ->open(true)
                        ->summary(new Summary(__('Word Count')))
                        ->items([
                            (new Para())
                                ->separator((new Single('br'))->render())
                                ->items(array_map(fn ($item): Text => (new Text(null, $item)), $infos)),
                        ]),
                ])
            ->render();
        }

        return '';
    }
}
