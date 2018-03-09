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

// dead but useful code, in order to have translations
__('Browser notifications') . __('Display notifications in your web browser');

$core->addBehavior('adminBeforeUserOptionsUpdate', array('notifyMeBehaviors', 'adminBeforeUserOptionsUpdate'));
$core->addBehavior('adminPreferencesForm', array('notifyMeBehaviors', 'adminPreferencesForm'));

// On all admin pages
$core->addBehavior('adminPageHTMLHead', array('notifyMeBehaviors', 'adminPageHTMLHead'));

// On post and page editing mode
$core->addBehavior('adminPostHeaders', array('notifyMeBehaviors', 'adminPostHeaders'));
$core->addBehavior('adminPageHeaders', array('notifyMeBehaviors', 'adminPostHeaders'));

// Transform error and standard DC notices to notifications
$core->addBehavior('adminPageNotificationError', array('notifyMeBehaviors', 'adminPageNotificationError'));
$core->addBehavior('adminPageNotification', array('notifyMeBehaviors', 'adminPageNotification'));

class notifyMeBehaviors
{
    private static function NotifyBrowser($message, $title = 'Dotclear', $silent = false)
    {
        return '<script type="text/javascript">' .
        'notifyBrowser(\'' .
        html::escapeJS(str_replace("\n", '. ', $message)) . "','" .
        html::escapeJS($title) . "'," .
            ($silent ? '1' : '0') . ");" .
            '</script>' . "\n";
    }

    public static function adminPageNotificationError($core, $err)
    {
        $core->auth->user_prefs->addWorkspace('notifyMe');
        if ($core->auth->user_prefs->notifyMe->active) {
            if ($core->auth->user_prefs->notifyMe->system && $core->auth->user_prefs->notifyMe->system_error) {

                // Set notification title
                $title = sprintf(__('Dotclear : %s'), $core->blog->name) . __(' - error');

                // Set notification text
                $msg = (string) $err;

                return self::NotifyBrowser($msg, $title, false);
            }
        }
    }

    public static function adminPageNotification($core, $notice)
    {
        $core->auth->user_prefs->addWorkspace('notifyMe');
        if ($core->auth->user_prefs->notifyMe->active) {
            if ($core->auth->user_prefs->notifyMe->system) {

                $type = array(
                    'success' => '',
                    'warning' => __(' - warning'),
                    'error'   => __(' - error'));

                // Set notification title
                $title  = sprintf(__('Dotclear : %s'), $core->blog->name);
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

    public static function adminBeforeUserOptionsUpdate($cur, $userID)
    {
        global $core;

        // Get and store user's prefs for plugin options
        $core->auth->user_prefs->addWorkspace('notifyMe');
        try {
            $notifyMe_newcomments = (integer) $_POST['notifyMe_new_comments'];
            if ($notifyMe_newcomments < 1) {
                $notifyMe_newcomments = 30; // seconds
            }
            $notifyMe_currentpost = (integer) $_POST['notifyMe_current_post'];
            if ($notifyMe_currentpost < 1) {
                $notifyMe_currentpost = 60; // seconds
            }
            $core->auth->user_prefs->notifyMe->put('active', !empty($_POST['notifyMe_active']), 'boolean');
            $core->auth->user_prefs->notifyMe->put('system', !empty($_POST['notifyMe_system']), 'boolean');
            $core->auth->user_prefs->notifyMe->put('system_error', !empty($_POST['notifyMe_system_error']), 'boolean');
            $core->auth->user_prefs->notifyMe->put('new_comments_on', !empty($_POST['notifyMe_new_comments_on']), 'boolean');
            $core->auth->user_prefs->notifyMe->put('new_comments', $notifyMe_newcomments);
            $core->auth->user_prefs->notifyMe->put('current_post_on', !empty($_POST['notifyMe_current_post_on']), 'boolean');
            $core->auth->user_prefs->notifyMe->put('current_post', $notifyMe_currentpost);
        } catch (Exception $e) {
            $core->error->add($e->getMessage());
        }
    }

    public static function adminPreferencesForm($core)
    {
        // Add fieldset for plugin options
        $core->auth->user_prefs->addWorkspace('notifyMe');

        echo
        '<div class="fieldset" id="notifyMe"><h5>' . __('Browser notifications') . '</h5>' .

        '<div class="two-boxes">' .
        '<p><label for="notifyMe_active" class="classic">' .
        form::checkbox('notifyMe_active', 1, $core->auth->user_prefs->notifyMe->active) . ' ' .
        __('Display browser notification') . '</label></p>' .

        '<p class="form-note">' . __('The notifications will have to be explicitly granted for the current session before displaying the first one.') . '</p>' .

        '</div><div class="two-boxes">' .

        '<p><label for="notifyMe_system" class="classic">' .
        form::checkbox('notifyMe_system', 1, $core->auth->user_prefs->notifyMe->system) . ' ' .
        __('Replace Dotclear notifications') . '</label></p>' .

        '<p><label for="notifyMe_system_error" class="classic">' .
        form::checkbox('notifyMe_system_error', 1, $core->auth->user_prefs->notifyMe->system_error) . ' ' .
        __('Including Dotclear errors') . '</label></p>' .

        '</div>' .
        '<hr />' .
        '<h5>' . __('Notifications:') . '</h5>' .

        '<p><label for="notifyMe_new_comments" class="classic">' .
        form::checkbox('notifyMe_new_comments_on', 1, $core->auth->user_prefs->notifyMe->new_comments_on) . ' ' .
        __('Check new comments every (in seconds, default: 30):') . ' ' .
        form::field('notifyMe_new_comments', 5, 4, (integer) $core->auth->user_prefs->notifyMe->new_comments) . '</label></p>' .

        '<p class="form-note">' . __('Only new non-junk comments for the current blog will be checked, whatever is the moderation setting. Your own comments or trackbacks will be ignored.') . '</p>' .

        '<p><label for="notifyMe_current_post" class="classic">' .
        form::checkbox('notifyMe_current_post_on', 1, $core->auth->user_prefs->notifyMe->current_post_on) . ' ' .
        __('Check current edited post every (in seconds, default: 60):') . ' ' .
        form::field('notifyMe_current_post', 5, 4, (integer) $core->auth->user_prefs->notifyMe->current_post) . '</label></p>' .

            '</div>';
    }

    public static function adminPageHTMLHead()
    {
        global $core;

        $core->auth->user_prefs->addWorkspace('notifyMe');
        if ($core->auth->user_prefs->notifyMe->active) {

            // Set notification title
            $title = sprintf(__('Dotclear : %s'), $core->blog->name);

            echo
            '<script type="text/javascript">' . "\n" .
            dcPage::jsVar('dotclear.notifyMe_Title', $title) .
            "</script>\n" .
            dcPage::jsLoad(urldecode(dcPage::getPF('notifyMe/js/notify.js')), $core->getVersion('notifyMe'));

            if ($core->auth->user_prefs->notifyMe->new_comments_on) {

                $sqlp = array(
                    'limit'              => 1,                 // only the last one
                    'no_content'         => true,              // content is not required
                    'comment_status_not' => -2,                // ignore spam
                    'order'              => 'comment_id DESC' // get last first
                );

                $email = $core->auth->getInfo('user_email');
                $url   = $core->auth->getInfo('user_url');
                if ($email && $url) {
                    // Ignore own comments/trackbacks
                    $sqlp['sql'] = " AND (comment_email <> '" . $email . "' OR comment_site <> '" . $url . "')";
                }

                $rs = $core->blog->getComments($sqlp);

                if ($rs->count()) {
                    $rs->fetch();
                    $last_comment_id = $rs->comment_id;
                } else {
                    $last_comment_id = -1;
                }

                // Get interval between two check
                $interval = (integer) $core->auth->user_prefs->notifyMe->new_comments;
                if (!$interval) {
                    $interval = 30; // 30 seconds by default
                }

                echo
                '<script type="text/javascript">' . "\n" .
                dcPage::jsVar('dotclear.notifyMe_CheckNewComments', $interval * 1000) .
                dcPage::jsVar('dotclear.notifyMe_LastCommentId', $last_comment_id) .
                "</script>\n" .
                dcPage::jsLoad(urldecode(dcPage::getPF('notifyMe/js/common.js')), $core->getVersion('notifyMe'));
            }
        }
    }

    public static function adminPostHeaders()
    {
        global $core, $post_id;

        $core->auth->user_prefs->addWorkspace('notifyMe');
        if ($core->auth->user_prefs->notifyMe->active &&
            $core->auth->user_prefs->notifyMe->current_post_on &&
            $post_id) {

            $sqlp = array('post_id' => $post_id);
            $rs   = $core->blog->getPosts($sqlp);
            if ($rs->isEmpty()) {
                // Not record ?
                return;
            }
            $media = new dcMedia($core);
            $rsm   = $media->getPostMedia($post_id);
            $hash  = notifyMeRest::hashPost($rs, $rsm);
            $dt    = $rs->post_upddt;

            // Set notification title
            $title = sprintf(__('Dotclear : %s'), $core->blog->name);

            // Get interval between two check
            $interval = (integer) $core->auth->user_prefs->notifyMe->current_post;
            if (!$interval) {
                $interval = 60; // 60 seconds by default
            }

            return
            '<script type="text/javascript">' . "\n" .
            dcPage::jsVar('dotclear.notifyMe_CheckCurrentPost', $interval * 1000) .
            dcPage::jsVar('dotclear.notifyMe_CurrentPostId', $post_id) .
            dcPage::jsVar('dotclear.notifyMe_CurrentPostHash', $hash) .
            dcPage::jsVar('dotclear.notifyMe_CurrentPostDT', $dt) .
            "</script>\n" .
            dcPage::jsLoad(urldecode(dcPage::getPF('notifyMe/js/post.js')), $core->getVersion('notifyMe'));
        }
    }
}

class notifyMe
{
    public static function NotifyBrowser($message, $title = 'Dotclear')
    {
        echo '<script type="text/javascript">' .
        'notifyBrowser(\'' .
        html::escapeJS(str_replace("\n", '. ', $message)) . "','" .
        html::escapeJS($title) . "');" .
            '</script>' . "\n";
    }
}
