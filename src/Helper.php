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

use Dotclear\Helper\Html\Form\Li;
use Dotclear\Helper\Html\Form\Set;
use Dotclear\Helper\Html\Form\Span;
use Dotclear\Helper\Html\Form\Ul;
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
                --$chars;
            }

            $words   = count(self::splitWords($text));
            $folios  = round($chars / 750) / 2.0;
            $reading = $words              / $wpm;

            $counters = [];

            if ($show_chars) {
                // Characters
                $class      = 'characters';
                $counters[] = (new Span(sprintf(__('%d character', '%d characters', $chars), $chars)))
                    ->class($class);
            }

            if ($show_words) {
                // Words
                $class      = 'words';
                $counters[] = (new Span(sprintf(__('%d word', '%d words', $words), $words)))
                    ->class($class);
            }

            if ($show_folios) {
                // Folios
                $class       = 'folios';
                $l10n_folios = __('1 folio', 'n folios', 1);
                if ($folios <= 0.5) {
                    // Less or equal 1/2 folio
                    $counters[] = (new Span(sprintf(__('&frac12; %s'), $l10n_folios)))
                        ->class($class);
                } elseif ($folios <= 1.0) {
                    // Less or equal 1 folio
                    $counters[] = (new Span(sprintf(__('1 %s'), $l10n_folios)))
                        ->class($class);
                } elseif ($folios < 2.0) {
                    // Less than 2 folios
                    $counters[] = (new Span(sprintf(__('1 &frac12; %s'), $l10n_folios)))
                        ->class($class);
                } elseif (floor($folios) !== $folios) {
                    // Folios and a part of one
                    $folios     = (int) floor($folios);
                    $counters[] = (new Span(sprintf(__('%d &frac12; folio', '%d &frac12; folios', $folios), $folios)))
                        ->class($class);
                } else {
                    // Folios
                    $folios     = (int) $folios;
                    $counters[] = (new Span(sprintf(__('%d folio', '%d folios', $folios), $folios)))
                        ->class($class);
                }
            }

            if ($show_time) {
                // Reading time
                $class = 'time';
                if ($reading < 1) {
                    $counters[] = (new Span(__('less than one minute')))
                        ->class($class);
                } else {
                    $reading    = (int) round($reading);
                    $counters[] = (new Span(sprintf(__('%d minute', '%d minutes', $reading), $reading)))
                        ->class($class);
                }
            }

            if ($counters !== []) {
                if ($use_list) {
                    $ret = (new Ul())
                        ->items([
                            ... array_map(fn ($item) => (new Li())->items([$item]), $counters),
                        ])
                    ->render();
                } else {
                    $ret = (new Set())
                        ->separator(' - ')
                        ->items($counters)
                    ->render();
                }
            }
        }

        return $ret;
    }
}
