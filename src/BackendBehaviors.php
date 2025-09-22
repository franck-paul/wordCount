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
        if ($settings->active) {
            $ret = My::cssLoad('style.css');
            if ($settings->autorefresh) {
                $interval = (int) ($settings->timeout ?? 60);
                $ret .= App::backend()->page()->jsJson('wordcount', ['interval' => $interval]) .
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
        if ($settings->active) {
            $details = $settings->details;
            $infos   = [];
            if ($post != null) {
                $wpm             = $settings->wpm;
                $countersExcerpt = $details ? Helper::getCounters($post->post_excerpt_xhtml, $wpm) : '';
                $countersContent = $details ? Helper::getCounters($post->post_content_xhtml, $wpm) : '';

                $text = ($post->post_excerpt_xhtml != '' ? $post->post_excerpt_xhtml . ' ' : '') . $post->post_content_xhtml;

                $countersTotal = Helper::getCounters($text, $wpm, ($post->post_excerpt_xhtml != ''));

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
