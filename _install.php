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
#
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_CONTEXT_ADMIN')){return;}

$new_version = $core->plugins->moduleInfo('wordCount','version');
$old_version = $core->getVersion('wordCount');

if (version_compare($old_version,$new_version,'>=')) return;

try
{
	if (version_compare(DC_VERSION,'2.4','<'))
	{
		throw new Exception('Word Count requires Dotclear 2.4');
	}

	$core->blog->settings->addNamespace('wordcount');

	// Default state is active
	$core->blog->settings->wordcount->put('wc_active',true,'boolean','Active',false,true);
	$core->blog->settings->wordcount->put('wc_details',false,'boolean','Details',false,true);

	$core->setVersion('wordCount',$new_version);

	return true;
}
catch (Exception $e)
{
	$core->error->add($e->getMessage());
}
return false;
?>