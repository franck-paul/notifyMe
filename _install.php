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

if (!defined('DC_CONTEXT_ADMIN')) {return;}

$new_version = $core->plugins->moduleInfo('notifyMe', 'version');
$old_version = $core->getVersion('notifyMe');

if (version_compare($old_version, $new_version, '>=')) {
    return;
}

try
{
    // Default user settings
    $core->auth->user_prefs->addWorkspace('notifyMe');

    if (!$core->auth->user_prefs->notifyMe->prefExists('active')) {
        $core->auth->user_prefs->notifyMe->put('active', false, 'boolean', 'Active');
    }

    if (!$core->auth->user_prefs->notifyMe->prefExists('system')) {
        $core->auth->user_prefs->notifyMe->put('system', false, 'boolean',
            'Replace Dotclear notifications');
    }

    if (!$core->auth->user_prefs->notifyMe->prefExists('system_error')) {
        $core->auth->user_prefs->notifyMe->put('system_error', false, 'boolean',
            'Including Dotclear errors?');
    }

    if (!$core->auth->user_prefs->notifyMe->prefExists('new_comments_on')) {
        $core->auth->user_prefs->notifyMe->put('new_comments_on', true, 'boolean',
            'Notify for new comments/trackbacks');
    }
    if (!$core->auth->user_prefs->notifyMe->prefExists('new_comments')) {
        $core->auth->user_prefs->notifyMe->put('new_comments', 30, 'integer',
            'Interval in seconds between new comments checking');
    }

    if (!$core->auth->user_prefs->notifyMe->prefExists('current_post_on')) {
        $core->auth->user_prefs->notifyMe->put('current_post_on', true, 'boolean',
            'Notify for entry changes');
    }
    if (!$core->auth->user_prefs->notifyMe->prefExists('current_post')) {
        $core->auth->user_prefs->notifyMe->put('current_post', 60, 'integer',
            'Interval in seconds betwwen current edited post checking');
    }

    $core->setVersion('notifyMe', $new_version);

    return true;
} catch (Exception $e) {
    $core->error->add($e->getMessage());
}
return false;
