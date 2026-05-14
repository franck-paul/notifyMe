<?php

/**
 * @brief notifyMe, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
$this->registerModule(
    'Browser notifications',
    'Display notifications in your web browser',
    'Franck Paul and contributors',
    '7.0',
    [
        'date'        => '2026-05-14T15:53:21+0200',
        'requires'    => [['core', '2.38']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'settings'    => [
            'pref' => '#user-options.notify-me',
        ],

        'details'    => 'https://open-time.net/?q=notifyMe',
        'support'    => 'https://github.com/franck-paul/notifyMe',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/notifyMe/main/dcstore.xml',
        'license'    => 'gpl2',
    ]
);
