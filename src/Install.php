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
declare(strict_types=1);

namespace Dotclear\Plugin\notifyMe;

use Dotclear\App;
use Dotclear\Core\Process;
use Exception;

class Install extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        try {
            if ($preferences = My::prefs()) {
                if (!$preferences->prefExists('active')) {
                    $preferences->put(
                        'active',
                        false,
                        'boolean',
                        'Active'
                    );
                }

                if (!$preferences->prefExists('system')) {
                    $preferences->put(
                        'system',
                        false,
                        'boolean',
                        'Replace Dotclear notifications'
                    );
                }

                if (!$preferences->prefExists('system_error')) {
                    $preferences->put(
                        'system_error',
                        false,
                        'boolean',
                        'Including Dotclear errors?'
                    );
                }

                if (!$preferences->prefExists('new_comments_on')) {
                    $preferences->put(
                        'new_comments_on',
                        true,
                        'boolean',
                        'Notify for new comments/trackbacks'
                    );
                }

                if (!$preferences->prefExists('new_comments')) {
                    $preferences->put(
                        'new_comments',
                        30,
                        'integer',
                        'Interval in seconds between new comments checking'
                    );
                }

                if (!$preferences->prefExists('current_post_on')) {
                    $preferences->put(
                        'current_post_on',
                        true,
                        'boolean',
                        'Notify for entry changes'
                    );
                }

                if (!$preferences->prefExists('current_post')) {
                    $preferences->put(
                        'current_post',
                        60,
                        'integer',
                        'Interval in seconds betwwen current edited post checking'
                    );
                }
            }
        } catch (Exception $exception) {
            App::error()->add($exception->getMessage());
        }

        return true;
    }
}
