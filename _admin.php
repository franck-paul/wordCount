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

if (!defined('DC_CONTEXT_ADMIN')) { return; }

// dead but useful code, in order to have translations
__('Word Count').__('Counts characters, words and folios of edited entry');

// Add menu item in blog menu
$_menu['Blog']->addItem(__('Word Count'),'plugin.php?p=wordCount','index.php?pf=wordCount/icon.png',
		preg_match('/plugin.php\?p=wordCount(&.*)?$/',$_SERVER['REQUEST_URI']),
		$core->auth->check('contentadmin',$core->blog->id));

// Add behavior callback
$core->addBehavior('adminPostForm',array('adminWordCount','wordCount'));
$core->addBehavior('adminPageHTMLHead',array('adminWordCount','adminPageHTMLHead'));

class adminWordCount
{
	public static function adminPageHTMLHead()
	{
		global $core;
		if ($core->blog->settings->wordcount->wc_active) {
			echo '<link rel="stylesheet" href="index.php?pf=wordCount/style.css" type="text/css" media="screen" />'."\n";
		}
	}

	static function splitWords($str)
	{
		$non_word = '\x{0000}-\x{002F}\x{003A}-\x{0040}\x{005b}-\x{0060}\x{007B}-\x{007E}\x{00A0}-\x{00BF}\s';
		if (preg_match_all('/([^'.$non_word.']{1,})/msu',html::clean($str),$match)) {
			return $match[1];
		}
		return array();
	}

	static function showCounters($text,$double = false)
	{
		$chars = mb_strlen(html::clean($text));
		if ($chars > 0) {
			if ($double) $chars--;
			$words = count(adminWordCount::splitWords($text));
			$folios = round($chars / 750) / 2.0;

			$l10n_chars = sprintf(__('%d character','%d characters',$chars),(integer)$chars);
			$l10n_words = sprintf(__('%d word','%d words',$words),(integer)$words);
			$l10n_folios = __('1 folio','n folios',1);

			if ($folios <= 0.5 ) {
				// Less or equal 1/2 folio
				return sprintf(__('%s - %s - &frac12; %s'),$l10n_chars,$l10n_words,$l10n_folios);
			} elseif ($folios <= 1.0) {
				// Less or equal 1 folio
				return sprintf(__('%s - %s - 1 %s'),$l10n_chars,$l10n_words,$l10n_folios);
			} elseif ($folios < 2.0) {
				// Less than 2 folios
				return sprintf(__('%s - %s - 1 &frac12; %s'),$l10n_chars,$l10n_words,$l10n_folios);

			} elseif (floor($folios) != $folios) {
				// Folios and a part of one
				$folios = floor($folios);
				$l10n_folios = sprintf(__('%d &frac12; folio','%d &frac12; folios',$folios),$folios);
				return sprintf(__('%s - %s - %s'),$l10n_chars,$l10n_words,$l10n_folios);
			} else {
				// Folios
				$l10n_folios = sprintf(__('%d folio','%d folios',$folios),$folios);
				return sprintf(__('%s - %s - %s'),$l10n_chars,$l10n_words,$l10n_folios);
			}
		}
		return '';
	}

	public static function wordCount($post)
	{
		global $core;
		if ($core->blog->settings->wordcount->wc_active) {
			if ($post != null) {
				if ($core->blog->settings->wordcount->wc_details) {
					$countersExcerpt = adminWordCount::showCounters($post->post_excerpt_xhtml);
					$countersContent = adminWordCount::showCounters($post->post_content_xhtml);
				}
				$text = ($post->post_excerpt_xhtml != '' ? $post->post_excerpt_xhtml.' ' : '');
				$text .= $post->post_content_xhtml;
				$countersTotal = adminWordCount::showCounters($text,($post->post_excerpt_xhtml != ''));

				if ($countersTotal != '') {
					echo '<div class="wordcount"><p>';
					if ($core->blog->settings->wordcount->wc_details && $countersExcerpt) {
						echo __('Excerpt:').' '.$countersExcerpt.'<br />';
						echo __('Content:').' '.$countersContent.'<br />';
						echo __('Total:').' '.$countersTotal;
					} else {
						echo __('Counters:').' '.$countersTotal;
					}
					echo '</p></div>';
				}
			}
		}
	}
}
