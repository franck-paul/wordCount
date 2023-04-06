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

use Dotclear\Helper\Html\Html;

class libWordCount
{
    public static function splitWords($str)
    {
        $non_word = '\x{0000}-\x{002F}\x{003A}-\x{0040}\x{005b}-\x{0060}\x{007B}-\x{007E}\x{00A0}-\x{00BF}\s';
        if (preg_match_all('/([^' . $non_word . ']{1,})/msu', Html::clean($str), $match)) {
            return $match[1];
        }

        return [];
    }

    public static function getCounters(
        $text,
        $wpm = 230,
        $double = false,
        $show_chars = true,
        $show_words = true,
        $show_folios = true,
        $show_time = true,
        $use_list = false
    ) {
        $ret = '';

        $chars = mb_strlen(Html::clean($text));
        if ($chars > 0) {
            if ($double) {
                $chars--;
            }

            $words   = is_countable(self::splitWords($text)) ? count(self::splitWords($text)) : 0;
            $folios  = round($chars / 750) / 2.0;
            $reading = $words              / $wpm;

            $counters = [];

            if ($show_chars) {
                // Characters
                $counters[] = sprintf(__('%d character', '%d characters', $chars), (int) $chars);
            }

            if ($show_words) {
                // Words
                $counters[] = sprintf(__('%d word', '%d words', $words), (int) $words);
            }

            if ($show_folios) {
                // Folios
                $l10n_folios = __('1 folio', 'n folios', 1);
                if ($folios <= 0.5) {
                    // Less or equal 1/2 folio
                    $counters[] = sprintf(__('&frac12; %s'), $l10n_folios);
                } elseif ($folios <= 1.0) {
                    // Less or equal 1 folio
                    $counters[] = sprintf(__('1 %s'), $l10n_folios);
                } elseif ($folios < 2.0) {
                    // Less than 2 folios
                    $counters[] = sprintf(__('1 &frac12; %s'), $l10n_folios);
                } elseif (floor($folios) != $folios) {
                    // Folios and a part of one
                    $folios     = (int) floor($folios);
                    $counters[] = sprintf(__('%d &frac12; folio', '%d &frac12; folios', $folios), $folios);
                } else {
                    // Folios
                    $folios     = (int) $folios;
                    $counters[] = sprintf(__('%d folio', '%d folios', $folios), $folios);
                }
            }

            if ($show_time) {
                // Reading time
                if ($reading < 1) {
                    $counters[] = sprintf(__('less than one minute'));
                } else {
                    $reading    = (int) round($reading);
                    $counters[] = sprintf(__('%d minute', '%d minutes', $reading), $reading);
                }
            }

            if (count($counters)) {
                if ($use_list) {
                    $ret = '<ul>';
                    foreach ($counters as $value) {
                        $ret .= '<li>' . $value . '</li>';
                    }
                    $ret .= '</ul>';
                } else {
                    $ret = implode(' - ', $counters);
                }
            }
        }

        return $ret;
    }
}
