<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of wordCount, a plugin for Dotclear 2.
# 
# Copyright (c) 2012 Franck Paul and contributors
# carnet.franck.paul@gmail.com
# 
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

/* Add menu item in extension list */
$_menu['Plugins']->addItem(__('Word Count'),'plugin.php?p=wordCount','index.php?pf=wordCount/icon.png',
		preg_match('/plugin.php\?p=wordCount(&.*)?$/',$_SERVER['REQUEST_URI']),
		$core->auth->check('contentadmin',$core->blog->id));

/* Add behavior callback */
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

	public static function wordCount($post)
	{
		global $core;
		if ($core->blog->settings->wordcount->wc_active) {
			if ($post != null) {
				$text = ($post->post_excerpt_xhtml != '' ? $post->post_excerpt_xhtml.' ' : '');
				$text .= $post->post_content_xhtml;
				$chars = mb_strlen(html::clean($text));
				if ($chars > 0) {
					$words = count(adminWordCount::splitWords($text));
					$folios = round($chars / 750) / 2.0;
					echo '<div class="wordcount"><p>'.__('Counters:').' ';
					if ($folios <= 0.5 ) {
						echo sprintf(__('%d characters - %d words - &frac12; folios'),$chars,$words);
					} elseif ($folios <= 1.0) {
						echo sprintf(__('%d characters - %d words - 1 folio'),$chars,$words);
					} elseif ($folios < 2.0) {
						echo sprintf(__('%d characters - %d words - 1 &frac12; folios'),$chars,$words);
					} elseif (floor($folios) != $folios) {
						$folios = floor($folios);
						echo sprintf(__('%d characters - %d words - %d &frac12; folios'),$chars,$words,$folios);
					} else {
						echo sprintf(__('%d characters - %d words - %d folios'),$chars,$words,$folios);
					}
					echo '</p></div>';
				}
			}
		}
	}
}
?>