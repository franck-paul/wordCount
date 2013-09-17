<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of wordCount, a plugin for Dotclear 2.
#
# Copyright (c) 2013 Franck Paul and contributors
# carnet.franck.paul@gmail.com
#
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

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
			if ($folios <= 0.5 ) {
				return sprintf(__('%d characters - %d words - &frac12; folios'),$chars,$words);
			} elseif ($folios <= 1.0) {
				return sprintf(__('%d characters - %d words - 1 folio'),$chars,$words);
			} elseif ($folios < 2.0) {
				return sprintf(__('%d characters - %d words - 1 &frac12; folios'),$chars,$words);
			} elseif (floor($folios) != $folios) {
				$folios = floor($folios);
				return sprintf(__('%d characters - %d words - %d &frac12; folios'),$chars,$words,$folios);
			} else {
				return sprintf(__('%d characters - %d words - %d folios'),$chars,$words,$folios);
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
?>