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

// dead but useful code, in order to have translations
__('Browser notifications') . __('Display notifications in your web browser');

dcCore::app()->addBehavior('adminBeforeUserOptionsUpdate', ['notifyMeBehaviors', 'adminBeforeUserOptionsUpdate']);
dcCore::app()->addBehavior('adminPreferencesForm', ['notifyMeBehaviors', 'adminPreferencesForm']);

// On all admin pages
dcCore::app()->addBehavior('adminPageHTMLHead', ['notifyMeBehaviors', 'adminPageHTMLHead']);

// On post and page editing mode
dcCore::app()->addBehavior('adminPostHeaders', ['notifyMeBehaviors', 'adminPostHeaders']);
dcCore::app()->addBehavior('adminPageHeaders', ['notifyMeBehaviors', 'adminPostHeaders']);

// Transform error and standard DC notices to notifications
dcCore::app()->addBehavior('adminPageNotificationError', ['notifyMeBehaviors', 'adminPageNotificationError']);
dcCore::app()->addBehavior('adminPageNotification', ['notifyMeBehaviors', 'adminPageNotification']);

class notifyMeBehaviors
{
    private static function NotifyBrowser($message, $title = 'Dotclear', $silent = false)
    {
        return dcPage::jsJson('notify_me_msg_' . time(), [
            'message' => str_replace("\n", '. ', $message),
            'title'   => $title,
            'silent'  => $silent,
        ]);
    }

    public static function adminPageNotificationError($core, $err)
    {
        dcCore::app()->auth->user_prefs->addWorkspace('notifyMe');
        if (dcCore::app()->auth->user_prefs->notifyMe->active) {
            if (dcCore::app()->auth->user_prefs->notifyMe->system && dcCore::app()->auth->user_prefs->notifyMe->system_error) {

                // Set notification title
                $title = sprintf(__('Dotclear : %s'), dcCore::app()->blog->name) . __(' - error');

                // Set notification text
                $msg = (string) $err;

                return self::NotifyBrowser($msg, $title, false);
            }
        }
    }

    public static function adminPageNotification($core, $notice)
    {
        dcCore::app()->auth->user_prefs->addWorkspace('notifyMe');
        if (dcCore::app()->auth->user_prefs->notifyMe->active) {
            if (dcCore::app()->auth->user_prefs->notifyMe->system) {
                $type = [
                    'success' => '',
                    'warning' => __(' - warning'),
                    'error'   => __(' - error'), ];

                // Set notification title
                $title  = sprintf(__('Dotclear : %s'), dcCore::app()->blog->name);
                $silent = true;
                if (isset($type[$notice['class']])) {
                    $title .= $type[$notice['class']];
                    if ($notice['class'] == 'error') {
                        $silent = false;
                    }
                }

                // Set notification text
                $msg = $notice['text'];

                return self::notifyBrowser($msg, $title, $silent);
            }
        }
    }

    public static function adminBeforeUserOptionsUpdate()
    {
        // Get and store user's prefs for plugin options
        dcCore::app()->auth->user_prefs->addWorkspace('notifyMe');

        try {
            $notifyMe_newcomments = (int) $_POST['notifyMe_new_comments'];
            if ($notifyMe_newcomments < 1) {
                $notifyMe_newcomments = 30; // seconds
            }
            $notifyMe_currentpost = (int) $_POST['notifyMe_current_post'];
            if ($notifyMe_currentpost < 1) {
                $notifyMe_currentpost = 60; // seconds
            }
            dcCore::app()->auth->user_prefs->notifyMe->put('active', !empty($_POST['notifyMe_active']), 'boolean');
            dcCore::app()->auth->user_prefs->notifyMe->put('wait', !empty($_POST['notifyMe_wait']), 'boolean');
            dcCore::app()->auth->user_prefs->notifyMe->put('system', !empty($_POST['notifyMe_system']), 'boolean');
            dcCore::app()->auth->user_prefs->notifyMe->put('system_error', !empty($_POST['notifyMe_system_error']), 'boolean');
            dcCore::app()->auth->user_prefs->notifyMe->put('new_comments_on', !empty($_POST['notifyMe_new_comments_on']), 'boolean');
            dcCore::app()->auth->user_prefs->notifyMe->put('new_comments', $notifyMe_newcomments);
            dcCore::app()->auth->user_prefs->notifyMe->put('current_post_on', !empty($_POST['notifyMe_current_post_on']), 'boolean');
            dcCore::app()->auth->user_prefs->notifyMe->put('current_post', $notifyMe_currentpost);
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }
    }

    public static function adminPreferencesForm()
    {
        // Add fieldset for plugin options
        dcCore::app()->auth->user_prefs->addWorkspace('notifyMe');

        echo
        '<div class="fieldset" id="notify-me"><h5>' . __('Browser notifications') . '</h5>' .

        '<div class="two-boxes">' .
        '<p><label for="notifyMe_active" class="classic">' .
        form::checkbox('notifyMe_active', 1, dcCore::app()->auth->user_prefs->notifyMe->active) . ' ' .
        __('Display browser notification') . '</label></p>' .

        '<p class="form-note">' . __('The notifications will have to be explicitly granted for the current session before displaying the first one.') . '</p>' .

        '<p><label for="notifyMe_wait" class="classic">' .
        form::checkbox('notifyMe_wait', 1, dcCore::app()->auth->user_prefs->notifyMe->wait) . ' ' .
        __('Wait for user interaction before closing notification') . '</label></p>' .

        '</div><div class="two-boxes">' .

        '<p><label for="notifyMe_system" class="classic">' .
        form::checkbox('notifyMe_system', 1, dcCore::app()->auth->user_prefs->notifyMe->system) . ' ' .
        __('Replace Dotclear notifications') . '</label></p>' .

        '<p><label for="notifyMe_system_error" class="classic">' .
        form::checkbox('notifyMe_system_error', 1, dcCore::app()->auth->user_prefs->notifyMe->system_error) . ' ' .
        __('Including Dotclear errors') . '</label></p>' .

        '</div>' .
        '<hr />' .
        '<h5>' . __('Notifications:') . '</h5>' .

        '<p><label for="notifyMe_new_comments" class="classic">' .
        form::checkbox('notifyMe_new_comments_on', 1, dcCore::app()->auth->user_prefs->notifyMe->new_comments_on) . ' ' .
        __('Check new comments every (in seconds, default: 30):') . ' ' .
        form::field('notifyMe_new_comments', 5, 4, (int) dcCore::app()->auth->user_prefs->notifyMe->new_comments) . '</label></p>' .

        '<p class="form-note">' . __('Only new non-junk comments for the current blog will be checked, whatever is the moderation setting. Your own comments or trackbacks will be ignored.') . '</p>' .

        '<p><label for="notifyMe_current_post" class="classic">' .
        form::checkbox('notifyMe_current_post_on', 1, dcCore::app()->auth->user_prefs->notifyMe->current_post_on) . ' ' .
        __('Check current edited post every (in seconds, default: 60):') . ' ' .
        form::field('notifyMe_current_post', 5, 4, (int) dcCore::app()->auth->user_prefs->notifyMe->current_post) . '</label></p>' .

            '</div>';
    }

    public static function adminPageHTMLHead()
    {
        dcCore::app()->auth->user_prefs->addWorkspace('notifyMe');
        if (dcCore::app()->auth->user_prefs->notifyMe->active) {

            // Set notification title
            $title = sprintf(__('Dotclear : %s'), dcCore::app()->blog->name);

            echo
            dcPage::jsJson('notify_me_config', [
                'title' => $title,
                'wait'  => dcCore::app()->auth->user_prefs->notifyMe->wait,
            ]) .
            dcPage::jsModuleLoad('notifyMe/js/notify.js', dcCore::app()->getVersion('notifyMe')) .
            dcPage::jsModuleLoad('notifyMe/js/queue.js', dcCore::app()->getVersion('notifyMe'));

            if (dcCore::app()->auth->user_prefs->notifyMe->new_comments_on) {
                $sqlp = [
                    'limit'              => 1,                 // only the last one
                    'no_content'         => true,              // content is not required
                    'comment_status_not' => -2,                // ignore spam
                    'order'              => 'comment_id DESC', // get last first
                ];

                $email = dcCore::app()->auth->getInfo('user_email');
                $url   = dcCore::app()->auth->getInfo('user_url');
                if ($email && $url) {
                    // Ignore own comments/trackbacks
                    $sqlp['sql'] = " AND (comment_email <> '" . $email . "' OR comment_site <> '" . $url . "')";
                }

                $rs = dcCore::app()->blog->getComments($sqlp);

                if ($rs->count()) {
                    $rs->fetch();
                    $last_comment_id = $rs->comment_id;
                } else {
                    $last_comment_id = -1;
                }

                // Get interval between two check
                $interval = (int) dcCore::app()->auth->user_prefs->notifyMe->new_comments;
                if (!$interval) {
                    $interval = 30; // 30 seconds by default
                }

                echo
                dcPage::jsJson('notify_me_comments', [
                    'check' => $interval * 1000,
                    'id'    => $last_comment_id,
                ]) .
                dcPage::jsModuleLoad('notifyMe/js/common.js', dcCore::app()->getVersion('notifyMe'));
            }
        }
    }

    public static function adminPostHeaders()
    {
        global $post_id;

        dcCore::app()->auth->user_prefs->addWorkspace('notifyMe');
        if (dcCore::app()->auth->user_prefs->notifyMe->active && dcCore::app()->auth->user_prefs->notifyMe->current_post_on && $post_id) {
            $sqlp = ['post_id' => $post_id];
            $rs   = dcCore::app()->blog->getPosts($sqlp);
            if ($rs->isEmpty()) {
                // Not recorded
                return;
            }
            $media = new dcMedia();
            $rsm   = $media->getPostMedia($post_id);
            $hash  = notifyMeRest::hashPost($rs, $rsm);
            $dt    = $rs->post_upddt;

            // Get interval between two check
            $interval = (int) dcCore::app()->auth->user_prefs->notifyMe->current_post;
            if (!$interval) {
                $interval = 60; // 60 seconds by default
            }

            return
            dcPage::jsJson('notify_me_post', [
                'check' => $interval * 1000,
                'id'    => $post_id,
                'hash'  => $hash,
                'dt'    => $dt,
            ]) .
            dcPage::jsModuleLoad('notifyMe/js/post.js', dcCore::app()->getVersion('notifyMe'));
        }
    }
}

class notifyMe
{
    public static function NotifyBrowser($message, $title = 'Dotclear')
    {
        return dcPage::jsJson('notify_me_msg_' . time(), [
            'message' => str_replace("\n", '. ', $message),
            'title'   => $title,
            'silent'  => false,
        ]);
    }
}
