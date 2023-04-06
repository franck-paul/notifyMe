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

use dcCore;
use dcNsProcess;
use Exception;

class Install extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = defined('DC_CONTEXT_ADMIN')
            && My::phpCompliant()
            && dcCore::app()->newVersion(My::id(), dcCore::app()->plugins->moduleInfo(My::id(), 'version'));

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        try {
            $settings = dcCore::app()->auth->user_prefs->notifyMe;

            if (!$settings->prefExists('active')) {
                $settings->put(
                    'active',
                    false,
                    'boolean',
                    'Active'
                );
            }

            if (!$settings->prefExists('system')) {
                $settings->put(
                    'system',
                    false,
                    'boolean',
                    'Replace Dotclear notifications'
                );
            }

            if (!$settings->prefExists('system_error')) {
                $settings->put(
                    'system_error',
                    false,
                    'boolean',
                    'Including Dotclear errors?'
                );
            }

            if (!$settings->prefExists('new_comments_on')) {
                $settings->put(
                    'new_comments_on',
                    true,
                    'boolean',
                    'Notify for new comments/trackbacks'
                );
            }
            if (!$settings->prefExists('new_comments')) {
                $settings->put(
                    'new_comments',
                    30,
                    'integer',
                    'Interval in seconds between new comments checking'
                );
            }

            if (!$settings->prefExists('current_post_on')) {
                $settings->put(
                    'current_post_on',
                    true,
                    'boolean',
                    'Notify for entry changes'
                );
            }
            if (!$settings->prefExists('current_post')) {
                $settings->put(
                    'current_post',
                    60,
                    'integer',
                    'Interval in seconds betwwen current edited post checking'
                );
            }

            return true;
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }
}
