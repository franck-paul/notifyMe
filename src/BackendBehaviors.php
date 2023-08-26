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

use dcBlog;
use dcCore;
use dcMedia;
use Dotclear\Core\Backend\Page;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Div;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Number;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Text;
use Exception;

class BackendBehaviors
{
    private static function NotifyBrowser($message, $title = 'Dotclear', $silent = false)
    {
        return Page::jsJson('notify_me_msg_' . time(), [
            'message' => str_replace("\n", '. ', $message),
            'title'   => $title,
            'silent'  => $silent,
        ]);
    }

    public static function adminPageNotificationError($unused, $err)
    {
        $settings = My::prefs();
        if ($settings->active) {
            if ($settings->system && $settings->system_error) {
                // Set notification title
                $title = sprintf(__('Dotclear : %s'), dcCore::app()->blog->name) . __(' - error');

                // Set notification text
                $msg = (string) $err;

                return self::NotifyBrowser($msg, $title, false);
            }
        }
    }

    public static function adminPageNotification($unused, $notice)
    {
        $settings = My::prefs();
        if ($settings->active) {
            if ($settings->system) {
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
        $settings = My::prefs();

        try {
            $notifyMe_newcomments = (int) $_POST['notifyMe_new_comments'];
            if ($notifyMe_newcomments < 1) {
                $notifyMe_newcomments = 30; // seconds
            }
            $notifyMe_currentpost = (int) $_POST['notifyMe_current_post'];
            if ($notifyMe_currentpost < 1) {
                $notifyMe_currentpost = 60; // seconds
            }
            $settings->put('active', !empty($_POST['notifyMe_active']), 'boolean');
            $settings->put('wait', !empty($_POST['notifyMe_wait']), 'boolean');
            $settings->put('system', !empty($_POST['notifyMe_system']), 'boolean');
            $settings->put('system_error', !empty($_POST['notifyMe_system_error']), 'boolean');
            $settings->put('new_comments_on', !empty($_POST['notifyMe_new_comments_on']), 'boolean');
            $settings->put('new_comments', $notifyMe_newcomments);
            $settings->put('current_post_on', !empty($_POST['notifyMe_current_post_on']), 'boolean');
            $settings->put('current_post', $notifyMe_currentpost);
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }
    }

    public static function adminPreferencesForm()
    {
        $settings = My::prefs();

        // Add fieldset for plugin options
        echo
        (new Fieldset('notify-me'))
        ->legend((new Legend(__('Browser notifications'))))
        ->fields([
            (new Div())->class('two-boxes')->items([
                (new Para())->items([
                    (new Checkbox('notifyMe_active', $settings->active))
                        ->value(1)
                        ->label((new Label(__('Display browser notification'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Para())->items([
                    (new Text(null, __('The notifications will have to be explicitly granted for the current session before displaying the first one.')))
                        ->class(['clear', 'form-note']),
                ]),
                (new Para())->items([
                    (new Checkbox('notifyMe_wait', $settings->wait))
                        ->value(1)
                        ->label((new Label(__('Wait for user interaction before closing notification'), Label::INSIDE_TEXT_AFTER))),
                ]),
            ]),
            (new Div())->class('two-boxes')->items([
                (new Para())->items([
                    (new Checkbox('notifyMe_system', $settings->system))
                        ->value(1)
                        ->label((new Label(__('Replace Dotclear notifications'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Para())->items([
                    (new Checkbox('notifyMe_system_error', $settings->system_error))
                        ->value(1)
                        ->label((new Label(__('Including Dotclear errors'), Label::INSIDE_TEXT_AFTER))),
                ]),
            ]),
            (new Text(null, '<hr />')),
            (new Text('h5', __('Notifications:'))),
            (new Div())->class('two-boxes')->items([
                (new Para())->items([
                    (new Checkbox('notifyMe_new_comments_on', $settings->new_comments_on))
                        ->value(1)
                        ->label((new Label(__('Check new comments'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Para())->items([
                    (new Text(null, __('Only new non-junk comments for the current blog will be checked, whatever is the moderation setting. Your own comments or trackbacks will be ignored.')))
                        ->class(['clear', 'form-note']),
                ]),
                (new Para())->items([
                    (new Number('notifyMe_new_comments', 0, 3600, (int) $settings->new_comments))
                        ->default(30)
                        ->label((new Label(__('Check new comments every (in seconds, default: 30):'), Label::INSIDE_TEXT_BEFORE))),
                ]),
            ]),
            (new Div())->class('two-boxes')->items([
                (new Para())->items([
                    (new Checkbox('notifyMe_current_post_on', $settings->current_post_on))
                        ->value(1)
                        ->label((new Label(__('Check current edited post'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Para())->items([
                    (new Number('notifyMe_current_post', 0, 3600, (int) $settings->current_post))
                        ->default(60)
                        ->label((new Label(__('Check current edited post every (in seconds, default: 60):'), Label::INSIDE_TEXT_BEFORE))),
                ]),
            ]),
        ])
        ->render();
    }

    public static function adminPageHTMLHead()
    {
        $settings = My::prefs();

        if ($settings->active) {
            // Set notification title
            $title = sprintf(__('Dotclear : %s'), dcCore::app()->blog->name);

            echo
            Page::jsJson('notify_me_config', [
                'title' => $title,
                'wait'  => $settings->wait,
            ]) .
            My::jsLoad('notify.js') .
            My::jsLoad('queue.js');

            if ($settings->new_comments_on) {
                $sqlp = [
                    'limit'              => 1,                      // only the last one
                    'no_content'         => true,                   // content is not required
                    'comment_status_not' => dcBlog::COMMENT_JUNK,   // ignore spam
                    'order'              => 'comment_id DESC',      // get last first
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
                $interval = (int) $settings->new_comments;
                if (!$interval) {
                    $interval = 30; // 30 seconds by default
                }

                echo
                Page::jsJson('notify_me_comments', [
                    'check' => $interval * 1000,
                    'id'    => $last_comment_id,
                ]) .
                My::jsLoad('common.js');
            }
        }
    }

    public static function adminPostHeaders()
    {
        $preferences = My::prefs();

        if ($preferences->active && $preferences->current_post_on && dcCore::app()->admin->post_id) {
            $sqlp = ['post_id' => dcCore::app()->admin->post_id];   // set in admin/post.php and plugins/pages/page.php
            $rs   = dcCore::app()->blog->getPosts($sqlp);
            if ($rs->isEmpty()) {
                // Not recorded
                return;
            }
            $media = new dcMedia();
            $rsm   = $media->getPostMedia((int) dcCore::app()->admin->post_id);
            $hash  = BackendRest::hashPost($rs, $rsm);
            $dt    = $rs->post_upddt;

            // Get interval between two check
            $interval = (int) $preferences->current_post;
            if (!$interval) {
                $interval = 60; // 60 seconds by default
            }

            return
            Page::jsJson('notify_me_post', [
                'check' => $interval * 1000,
                'id'    => dcCore::app()->admin->post_id,
                'hash'  => $hash,
                'dt'    => $dt,
            ]) .
            My::jsLoad('post.js');
        }
    }
}
