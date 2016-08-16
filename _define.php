<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of notifyMe, a plugin for Dotclear 2.
#
# Copyright (c) Franck Paul and contributors
#
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_RC_PATH')) { return; }

$this->registerModule(
	/* Name */				"Browser notifications",
	/* Description*/		"Display notifications in your web browser",
	/* Author */			"Franck Paul and contributors",
	/* Version */			'0.5',
	array(
		/* Dependencies */	'requires' =>		array(array('core','2.8')),
		/* Permissions */	'permissions' =>	'usage,contentadmin',
		/* Type */			'type' =>			'plugin'
	)
);
