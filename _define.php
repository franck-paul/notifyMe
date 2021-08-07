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
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'Browser notifications',                     // Name
    'Display notifications in your web browser', // Description
    'Franck Paul and contributors',              // Author
    '0.8',                                       // Version
    [
        'requires'    => [['core', '2.19']],                        // Dependencies
        'permissions' => 'usage,contentadmin',                      // Permissions
        'type'        => 'plugin',                                  // Type
        'details'     => 'https://open-time.net/?q=notifyMe',       // Details URL
        'support'     => 'https://github.com/franck-paul/notifyMe', // Support URL
        'settings'    => [                                          // Settings
            'pref' => '#user-options.notify-me'
        ]
    ]
);
