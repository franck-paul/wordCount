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
__('Word Count').__('Counts characters, words and folios, reading time of entry');

// Add menu item in blog menu
$_menu['Blog']->addItem(__('Word Count'),'plugin.php?p=wordCount','index.php?pf=wordCount/icon.png',
		preg_match('/plugin.php\?p=wordCount(&.*)?$/',$_SERVER['REQUEST_URI']),
		$core->auth->check('contentadmin',$core->blog->id));

require dirname(__FILE__).'/_widgets.php';

// Add behaviour callback for post
$core->addBehavior('adminPostForm',array('adminWordCount','wordCount'));
$core->addBehavior('adminPostHeaders',array('adminWordCount','adminPostHeaders'));
// Add behaviour callback for page
$core->addBehavior('adminPageForm',array('adminWordCount','wordCount'));
$core->addBehavior('adminPageHeaders',array('adminWordCount','adminPostHeaders'));

class adminWordCount
{
	public static function adminPostHeaders()
	{
		global $core;

		if ($core->blog->settings->wordcount->wc_active) {
			return '<link rel="stylesheet" href="index.php?pf=wordCount/style.css" type="text/css" media="screen" />'."\n";
		}
	}

	public static function wordCount($post)
	{
		global $core;

		if ($core->blog->settings->wordcount->wc_active) {
			if ($post != null) {
				$wpm = $core->blog->settings->wordcount->wc_wpm;
				if ($core->blog->settings->wordcount->wc_details) {
					$countersExcerpt = libWordCount::getCounters($post->post_excerpt_xhtml,$wpm);
					$countersContent = libWordCount::getCounters($post->post_content_xhtml,$wpm);
				}
				$text = ($post->post_excerpt_xhtml != '' ? $post->post_excerpt_xhtml.' ' : '');
				$text .= $post->post_content_xhtml;
				$countersTotal = libWordCount::getCounters($text,$wpm,($post->post_excerpt_xhtml != ''));

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
