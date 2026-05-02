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

namespace Dotclear\Plugin\notifyMe;

use Dotclear\App;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Div;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Number;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Interface\Core\ErrorInterface;
use Exception;

class BackendBehaviors
{
    private static function notifyBrowser(string $message, string $title = 'Dotclear', bool $silent = false): string
    {
        return App::backend()->page()->jsJson('notify_me_msg_' . time(), [
            'message' => str_replace("\n", '. ', $message),
            'title'   => $title,
            'silent'  => $silent,
        ]);
    }

    /**
     * @param      mixed            $unused  The unused
     * @param      ErrorInterface   $err     The error
     */
    public static function adminPageNotificationError($unused, ErrorInterface $err): string
    {
        $settings = My::prefs();
        if ($settings->active && $settings->system && $settings->system_error && $err->flag()) {
            $message = '';
            $title   = sprintf(__('Dotclear : %s'), App::blog()->name()) . __(' - error');

            foreach ($err->dump() as $msg) {
                $message .= ($message === '' ? '' : ' – ') . $msg;
            }

            return self::notifyBrowser($message, $title, false);
        }

        return '';
    }

    /**
     * @param      mixed                    $unused  The unused
     * @param      array<string, string>    $notice  The notice
     */
    public static function adminPageNotification($unused, array $notice): string
    {
        $settings = My::prefs();
        if ($settings->active && $settings->system) {
            $type = [
                'success' => '',
                'warning' => __(' - warning'),
                'error'   => __(' - error'), ];
            // Set notification title
            $title  = sprintf(__('Dotclear : %s'), App::blog()->name());
            $silent = true;
            if (isset($type[$notice['class']])) {
                $title .= $type[$notice['class']];
                if ($notice['class'] === 'error') {
                    $silent = false;
                }
            }

            // Set notification text
            $msg = $notice['text'];

            return self::notifyBrowser($msg, $title, $silent);
        }

        return '';
    }

    public static function adminBeforeUserOptionsUpdate(): string
    {
        // Get and store user's prefs for plugin options
        $settings = My::prefs();

        try {
            $newcomments = isset($_POST['notifyMe_new_comments']) && is_numeric($newcomments = $_POST['notifyMe_new_comments']) ? (int) $newcomments : 0;
            if ($newcomments < 1) {
                $newcomments = 30; // seconds
            }

            $currentpost = isset($_POST['notifyMe_current_post']) && is_numeric($currentpost = $_POST['notifyMe_current_post']) ? (int) $currentpost : 0;
            if ($currentpost < 1) {
                $currentpost = 60; // seconds
            }

            $settings->put('active', !empty($_POST['notifyMe_active']), App::blogWorkspace()::NS_BOOL);
            $settings->put('wait', !empty($_POST['notifyMe_wait']), App::blogWorkspace()::NS_BOOL);
            $settings->put('system', !empty($_POST['notifyMe_system']), App::blogWorkspace()::NS_BOOL);
            $settings->put('system_error', !empty($_POST['notifyMe_system_error']), App::blogWorkspace()::NS_BOOL);
            $settings->put('new_comments_on', !empty($_POST['notifyMe_new_comments_on']), App::blogWorkspace()::NS_BOOL);
            $settings->put('new_comments', $newcomments, App::blogWorkspace()::NS_INT);
            $settings->put('current_post_on', !empty($_POST['notifyMe_current_post_on']), App::blogWorkspace()::NS_BOOL);
            $settings->put('current_post', $currentpost, App::blogWorkspace()::NS_INT);
        } catch (Exception $exception) {
            App::error()->add($exception->getMessage());
        }

        return '';
    }

    public static function adminPreferencesForm(): string
    {
        $settings = My::prefs();

        $active          = is_bool($active = $settings->active)                   && $active;
        $wait            = is_bool($wait = $settings->wait)                       && $wait;
        $system          = is_bool($system = $settings->system)                   && $system;
        $system_error    = is_bool($system_error = $settings->system_error)       && $system_error;
        $new_comments_on = is_bool($new_comments_on = $settings->new_comments_on) && $new_comments_on;
        $new_comments    = is_numeric($new_comments = $settings->new_comments) ? (int) $new_comments : 0;
        $current_post_on = is_bool($current_post_on = $settings->current_post_on) && $current_post_on;
        $current_post    = is_numeric($current_post = $settings->current_post) ? (int) $current_post : 0;

        // Add fieldset for plugin options
        echo
        (new Fieldset('notify-me'))
        ->legend((new Legend(__('Browser notifications'))))
        ->fields([
            (new Div())->class('two-boxes')->items([
                (new Para())->items([
                    (new Checkbox('notifyMe_active', $active))
                        ->value(1)
                        ->label((new Label(__('Display browser notification'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Para())->items([
                    (new Text(null, __('The notifications will have to be explicitly granted for the current session before displaying the first one.')))
                        ->class(['clear', 'form-note']),
                ]),
                (new Para())->items([
                    (new Checkbox('notifyMe_wait', $wait))
                        ->value(1)
                        ->label((new Label(__('Wait for user interaction before closing notification'), Label::INSIDE_TEXT_AFTER))),
                ]),
            ]),
            (new Div())->class('two-boxes')->items([
                (new Para())->items([
                    (new Checkbox('notifyMe_system', $system))
                        ->value(1)
                        ->label((new Label(__('Replace Dotclear notifications'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Para())->items([
                    (new Checkbox('notifyMe_system_error', $system_error))
                        ->value(1)
                        ->label((new Label(__('Including Dotclear errors'), Label::INSIDE_TEXT_AFTER))),
                ]),
            ]),
            (new Text(null, '<hr>')),
            (new Text('h5', __('Notifications:'))),
            (new Div())->class('two-boxes')->items([
                (new Para())->items([
                    (new Checkbox('notifyMe_new_comments_on', $new_comments_on))
                        ->value(1)
                        ->label((new Label(__('Check new comments'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Para())->items([
                    (new Text(null, __('Only new non-junk comments for the current blog will be checked, whatever is the moderation setting. Your own comments or trackbacks will be ignored.')))
                        ->class(['clear', 'form-note']),
                ]),
                (new Para())->items([
                    (new Number('notifyMe_new_comments', 0, 3600, $new_comments))
                        ->default(30)
                        ->label((new Label(__('Check new comments every (in seconds, default: 30):'), Label::INSIDE_TEXT_BEFORE))),
                ]),
            ]),
            (new Div())->class('two-boxes')->items([
                (new Para())->items([
                    (new Checkbox('notifyMe_current_post_on', $current_post_on))
                        ->value(1)
                        ->label((new Label(__('Check current edited post'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Para())->items([
                    (new Number('notifyMe_current_post', 0, 3600, $current_post))
                        ->default(60)
                        ->label((new Label(__('Check current edited post every (in seconds, default: 60):'), Label::INSIDE_TEXT_BEFORE))),
                ]),
            ]),
        ])
        ->render();

        return '';
    }

    public static function adminPageHTMLHead(): string
    {
        $settings = My::prefs();

        if ($settings->active) {
            // Set notification title
            $title = sprintf(__('Dotclear : %s'), App::blog()->name());

            echo
            App::backend()->page()->jsJson('notify_me_config', [
                'title' => $title,
                'wait'  => $settings->wait,
            ]) .
            My::jsLoad('notify.js') .
            My::jsLoad('queue.js');

            if ($settings->new_comments_on) {
                $sqlp = [
                    'limit'              => 1,                              // only the last one
                    'no_content'         => true,                           // content is not required
                    'comment_status_not' => App::status()->comment()::JUNK, // ignore spam
                    'order'              => 'comment_id DESC',              // get last first
                ];

                $email = is_string($email = App::auth()->getInfo('user_email')) ? $email : '';
                $url   = is_string($url = App::auth()->getInfo('user_url')) ? $url : '';
                if ($email !== '' && $url !== '') {
                    // Ignore own comments/trackbacks
                    $sqlp['sql'] = " AND (comment_email <> '" . $email . "' OR comment_site <> '" . $url . "')";
                }

                $rs = App::blog()->getComments($sqlp);

                if ($rs->count()) {
                    $rs->fetch();
                    $last_comment_id = $rs->comment_id;
                } else {
                    $last_comment_id = -1;
                }

                // Get interval between two check
                $interval = is_numeric($interval = $settings->new_comments) ? (int) $interval : 0;
                if ($interval === 0) {
                    $interval = 30; // 30 seconds by default
                }

                echo
                App::backend()->page()->jsJson('notify_me_comments', [
                    'check' => $interval * 1000,
                    'id'    => $last_comment_id,
                ]) .
                My::jsLoad('common.js');
            }
        }

        return '';
    }

    public static function adminPostHeaders(): string
    {
        $settings = My::prefs();

        if ($settings->active && $settings->current_post_on && App::backend()->post_id) {
            $post_id = is_numeric($post_id = App::backend()->post_id) ? (int) $post_id : 0;
            if ($post_id !== 0) {
                return '';
            }

            $sqlp = ['post_id' => $post_id];   // set in admin/post.php and plugins/pages/page.php
            $rs   = App::blog()->getPosts($sqlp);
            if ($rs->isEmpty()) {
                // Not recorded
                return '';
            }

            $media = App::media();
            $rsm   = $media->getPostMedia($post_id);
            $hash  = BackendRest::hashPost($rs, $rsm);
            $dt    = $rs->post_upddt;

            // Get interval between two check
            $interval = is_numeric($interval = $settings->current_post) ? (int) $interval : 0;
            if ($interval === 0) {
                $interval = 60; // 60 seconds by default
            }

            return
            App::backend()->page()->jsJson('notify_me_post', [
                'check' => $interval * 1000,
                'id'    => $post_id,
                'hash'  => $hash,
                'dt'    => $dt,
            ]) .
            My::jsLoad('post.js');
        }

        return '';
    }
}
