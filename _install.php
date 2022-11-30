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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

$new_version = dcCore::app()->plugins->moduleInfo('notifyMe', 'version');
$old_version = dcCore::app()->getVersion('notifyMe');

if (version_compare((string) $old_version, $new_version, '>=')) {
    return;
}

try {
    // Default user settings
    dcCore::app()->auth->user_prefs->addWorkspace('notifyMe');

    if (!dcCore::app()->auth->user_prefs->notifyMe->prefExists('active')) {
        dcCore::app()->auth->user_prefs->notifyMe->put('active', false, 'boolean', 'Active');
    }

    if (!dcCore::app()->auth->user_prefs->notifyMe->prefExists('system')) {
        dcCore::app()->auth->user_prefs->notifyMe->put(
            'system',
            false,
            'boolean',
            'Replace Dotclear notifications'
        );
    }

    if (!dcCore::app()->auth->user_prefs->notifyMe->prefExists('system_error')) {
        dcCore::app()->auth->user_prefs->notifyMe->put(
            'system_error',
            false,
            'boolean',
            'Including Dotclear errors?'
        );
    }

    if (!dcCore::app()->auth->user_prefs->notifyMe->prefExists('new_comments_on')) {
        dcCore::app()->auth->user_prefs->notifyMe->put(
            'new_comments_on',
            true,
            'boolean',
            'Notify for new comments/trackbacks'
        );
    }
    if (!dcCore::app()->auth->user_prefs->notifyMe->prefExists('new_comments')) {
        dcCore::app()->auth->user_prefs->notifyMe->put(
            'new_comments',
            30,
            'integer',
            'Interval in seconds between new comments checking'
        );
    }

    if (!dcCore::app()->auth->user_prefs->notifyMe->prefExists('current_post_on')) {
        dcCore::app()->auth->user_prefs->notifyMe->put(
            'current_post_on',
            true,
            'boolean',
            'Notify for entry changes'
        );
    }
    if (!dcCore::app()->auth->user_prefs->notifyMe->prefExists('current_post')) {
        dcCore::app()->auth->user_prefs->notifyMe->put(
            'current_post',
            60,
            'integer',
            'Interval in seconds betwwen current edited post checking'
        );
    }

    dcCore::app()->setVersion('notifyMe', $new_version);

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

return false;
