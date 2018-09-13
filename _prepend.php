<?php
/**
 * @brief notifyMe, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('DC_RC_PATH')) {return;}

// Public and Admin mode

if (!defined('DC_CONTEXT_ADMIN')) {return false;}

// Admin mode

$__autoload['notifyMeRest'] = dirname(__FILE__) . '/_services.php';

$this->core->rest->addFunction('notifyMeCheckNewComments', ['notifyMeRest', 'checkNewComments']);
$this->core->rest->addFunction('notifyMeCheckCurrentPost', ['notifyMeRest', 'checkCurrentPost']);
