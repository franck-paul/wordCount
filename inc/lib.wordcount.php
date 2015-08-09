<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of wordCount, a plugin for Dotclear 2.
#
# Copyright (c) Franck Paul and contributors
# carnet.franck.paul@gmail.com
#
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

class libWordCount
{
	static function splitWords($str)
	{
		$non_word = '\x{0000}-\x{002F}\x{003A}-\x{0040}\x{005b}-\x{0060}\x{007B}-\x{007E}\x{00A0}-\x{00BF}\s';
		if (preg_match_all('/([^'.$non_word.']{1,})/msu',html::clean($str),$match)) {
			return $match[1];
		}
		return array();
	}

	static function getCounters($text,$wpm = 230,$double = false,
		$show_chars = true,$show_words = true,$show_folios = true,$show_time = true,
		$use_list = false)
	{
		$ret = '';

		$chars = mb_strlen(html::clean($text));
		if ($chars > 0) {
			if ($double) $chars--;
			$words = count(self::splitWords($text));
			$folios = round($chars / 750) / 2.0;
			$reading = $words / $wpm;

			$counters = array();

			if ($show_chars) {
				// Characters
				$counters[] = sprintf(__('%d character','%d characters',$chars),(integer)$chars);
			}

			if ($show_words) {
				// Words
				$counters[] = sprintf(__('%d word','%d words',$words),(integer)$words);
			}

			if ($show_folios) {
				// Folios
				$l10n_folios = __('1 folio','n folios',1);
				if ($folios <= 0.5 ) {
					// Less or equal 1/2 folio
					$counters[] = sprintf(__('&frac12; %s'),$l10n_folios);
				} elseif ($folios <= 1.0) {
					// Less or equal 1 folio
					$counters[] = sprintf(__('1 %s'),$l10n_folios);
				} elseif ($folios < 2.0) {
					// Less than 2 folios
					$counters[] = sprintf(__('1 &frac12; %s'),$l10n_folios);
				} elseif (floor($folios) != $folios) {
					// Folios and a part of one
					$folios = floor($folios);
					$counters[] = sprintf(__('%d &frac12; folio','%d &frac12; folios',$folios),$folios);
				} else {
					// Folios
					$counters[] = sprintf(__('%d folio','%d folios',$folios),$folios);
				}
			}

			if ($show_time) {
				// Reading time
				if ($reading < 1) {
					$counters[] = sprintf(__('less than one minute'));
				} else {
					$reading = round($reading);
					$counters[] = sprintf(__('%d minute','%d minutes',$reading),$reading);
				}
			}

			if (count($counters)) {
				if ($use_list) {
					$ret = '<ul>';
					foreach ($counters as $value) {
						$ret .= '<li>'.$value.'</li>';
					}
					$ret .= '</ul>';
				} else {
					$ret = implode(' - ',$counters);
				}
			}
		}
		return $ret;
	}
}
