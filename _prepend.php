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

// Public and Admin mode

if (!defined('DC_CONTEXT_ADMIN')) { return false; }

// Admin mode

$__autoload['notifyMeRest'] = dirname(__FILE__).'/_services.php';

$this->core->rest->addFunction('notifyMeCheckNewComments', array('notifyMeRest', 'checkNewComments'));
$this->core->rest->addFunction('notifyMeCheckCurrentPost', array('notifyMeRest', 'checkCurrentPost'));
