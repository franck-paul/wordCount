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

use Dotclear\Helper\Html\Html;

class Helper
{
    /**
     * Splits words.
     *
     * @param      string  $str    The string
     *
     * @return     array<string>
     */
    public static function splitWords(string $str): array
    {
        $non_word = '\x{0000}-\x{002F}\x{003A}-\x{0040}\x{005b}-\x{0060}\x{007B}-\x{007E}\x{00A0}-\x{00BF}\s';
        if (preg_match_all('/([^' . $non_word . ']{1,})/msu', Html::clean($str), $match)) {
            return $match[1];
        }

        return [];
    }

    /**
     * Gets the counters.
     *
     * @param      string  $text         The text
     * @param      int     $wpm          The word per minute (average reading speed)
     * @param      bool    $double       The double (true if $text is combination of two different parts, as excerpt and content)
     * @param      bool    $show_chars   The show characters
     * @param      bool    $show_words   The show words
     * @param      bool    $show_folios  The show folios
     * @param      bool    $show_time    The show time
     * @param      bool    $use_list     The use list (ul/li if true, dash separated string if false)
     *
     * @return     string  The counters.
     */
    public static function getCounters(
        string $text,
        int $wpm = 230,
        bool $double = false,
        bool $show_chars = true,
        bool $show_words = true,
        bool $show_folios = true,
        bool $show_time = true,
        bool $use_list = false
    ): string {
        $ret = '';

        $chars = mb_strlen(Html::clean($text));
        if ($chars > 0) {
            if ($double) {
                $chars--;
            }

            $words   = count(self::splitWords($text));
            $folios  = round($chars / 750) / 2.0;
            $reading = $words              / $wpm;

            $counters = [];

            if ($show_chars) {
                // Characters
                $counters[] = sprintf(__('%d character', '%d characters', $chars), $chars);
            }

            if ($show_words) {
                // Words
                $counters[] = sprintf(__('%d word', '%d words', $words), $words);
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
