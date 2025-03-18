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

class FrontendTemplateCode
{
    /**
     * PHP code for tpl:WordCount value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function WordCount(
        string $_id_,
        int $_wpm_,
        bool $_chars_,
        bool $_words_,
        bool $_folios_,
        bool $_time_,
        bool $_list_,
        array $_params_,
        string $_tag_
    ): void {
        $wordcount_settings = App::blog()->settings()->get($_id_);
        $wordcount_buffer   = \Dotclear\Plugin\wordCount\Helper::getCounters(
            App::frontend()->context()->posts->getExcerpt() . ' ' . App::frontend()->context()->posts->getContent(),
            $_wpm_ ?: $wordcount_settings->wpm,
            true,
            $_chars_,
            $_words_,
            $_folios_,
            $_time_,
            $_list_
        );
        echo \Dotclear\Core\Frontend\Ctx::global_filters(
            $wordcount_buffer,
            $_params_,
            $_tag_
        );
        unset($wordcount_buffer, $wordcount_settings);
    }
}
