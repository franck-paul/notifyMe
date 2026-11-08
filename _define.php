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
declare(strict_types=1);

if (isset($this) && is_object($this) && method_exists($this, 'registerModule') && isset($this->id) && is_string($this->id)) {
    $this->registerModule(
        'Browser notifications',
        'Display notifications in your web browser',
        'Franck Paul and contributors',
        '9.0',
        [
            'date'        => '2026-11-08T10:18:49+0100',
            'requires'    => [['core', '2.40']],
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
}
