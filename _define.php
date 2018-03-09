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

$this->registerModule(
    "Browser notifications",                     // Name
    "Display notifications in your web browser", // Description
    "Franck Paul and contributors",              // Author
    '0.6',                                       // Version
    array(
        'requires'    => array(array('core', '2.11')),              // Dependencies
        'permissions' => 'usage,contentadmin',                      // Permissions
        'type'        => 'plugin',                                  // Type
        'settings'    => array('pref' => '#user-options.notifyMe') // Settings
    )
);
