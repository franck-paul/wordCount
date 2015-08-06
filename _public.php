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
if (!defined('DC_RC_PATH')) { return; }

// Register template tag
//
// {{tpl:WordCount [attributes]}}
// with attributes may be one or more of:
// - chars="0|1" show number of characters (0 = default)
// - words="0|1" show number of words (1 = default)
// - folios="0|1" show number of folios (0 = default)
// - time="0|1" : show estimated reading time (0 = default)
// - wpm="nnn" : words per minute (blog setting by default)
//
// Example : <p><strong>{{tpl:lang reading time:}}</strong> {{tpl:WordCount words="0" time="1"}}</p>

$core->tpl->addValue('WordCount',array('tplWordCount','WordCount'));

class tplWordCount
{
	public static function WordCount($attr)
	{
		// Check attributes
		$chars = isset($attr['chars']) ? (integer)$attr['chars'] : 0;
		$words = isset($attr['words']) ? (integer)$attr['words'] : 1;
		$folios = isset($attr['folios']) ? (integer)$attr['folios'] : 0;
		$time = isset($attr['time']) ? (integer)$attr['time'] : 0;
		$wpm = isset($attr['wpm']) ? (integer)$attr['wpm'] : 0;

		// Get filters formatter string
		$f = $GLOBALS['core']->tpl->getFilters($attr);

		return '<?php echo '.sprintf($f,'libWordCount::getCounters(
			$_ctx->posts->getExcerpt()." ".$_ctx->posts->getContent(),'.
			($wpm ? $wpm : '$core->blog->settings->wordcount->wc_wpm').',true,'.
			$chars.','.$words.','.$folios.','.$time.')').'; ?>';
	}
}
