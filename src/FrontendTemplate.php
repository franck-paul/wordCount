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

use ArrayObject;
use Dotclear\App;

class FrontendTemplate
{
    /**
     * Register template tag
     *
     * {{tpl:WordCount [attributes]}}
     * with attributes may be one or more of:
     * - chars="0|1" show number of characters (0 = default)
     * - words="0|1" show number of words (1 = default)
     * - folios="0|1" show number of folios (0 = default)
     * - time="0|1" : show estimated reading time (0 = default)
     * - wpm="nnn" : words per minute (blog setting by default)
     * - list="0|1" : use ul/li markup (0 = none)
     *
     * Example :
     *
     * ```html
     * <p><strong>{{tpl:lang reading time:}}</strong> {{tpl:WordCount words="0" time="1"}}</p>
     * ```
     *
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr   The attribute
     */
    public static function WordCount(array|ArrayObject $attr): string
    {
        // Check attributes
        $chars  = isset($attr['chars']) ? (int) $attr['chars'] : 0;
        $words  = isset($attr['words']) ? (int) $attr['words'] : 1;
        $folios = isset($attr['folios']) ? (int) $attr['folios'] : 0;
        $time   = isset($attr['time']) ? (int) $attr['time'] : 0;
        $wpm    = isset($attr['wpm']) ? (int) $attr['wpm'] : 0;
        $list   = isset($attr['list']) ? (int) $attr['list'] : 0;
        // Get filters formatter string
        $f = App::frontend()->template()->getFilters($attr);

        return '<?php $settings = App::blog()->settings()->get(\'' . My::id() . '\'); echo ' . sprintf($f, Helper::class . '::getCounters(App::frontend()->context()->posts->getExcerpt()." ".App::frontend()->context()->posts->getContent(),' . ($wpm ?: '$settings->wpm') . ',true,' . $chars . ',' . $words . ',' . $folios . ',' . $time . ',' . $list . ')') . '; ?>';
    }
}
