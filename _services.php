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

class notifyMeRest
{
    /**
     * Serve method to check new comments for current blog.
     *
     * @param    core    <b>dcCore</b>    dcCore instance
     * @param    get        <b>array</b>    cleaned $_GET
     *
     * @return    <b>xmlTag</b>    XML representation of response
     */
    public static function checkNewComments($core, $get)
    {
        global $core;

        $last_id = !empty($get['last_id']) ? $get['last_id'] : -1;

        $sqlp = array(
            'no_content'         => true, // content is not required
            'comment_status_not' => -2,   // ignore spam
            'order'              => 'comment_id ASC',

            'sql'                => 'AND comment_id > ' . $last_id // only new ones
        );

        $email = $core->auth->getInfo('user_email');
        $url   = $core->auth->getInfo('user_url');
        if ($email && $url) {
            // Ignore own comments/trackbacks
            $sqlp['sql'] .= " AND (comment_email <> '" . $email . "' OR comment_site <> '" . $url . "')";
        }

        $rs    = $core->blog->getComments($sqlp);
        $count = $rs->count();

        if ($count) {
            while ($rs->fetch()) {
                $last_comment_id = $rs->comment_id;
            }
        }
        $rsp      = new xmlTag('check');
        $rsp->ret = $count;
        if ($count) {
            $rsp->msg     = sprintf(__('One new comment has been posted', '%s new comments have been posted', $count), $count);
            $rsp->last_id = $last_comment_id;
        }

        return $rsp;
    }

    /**
     * Serve method to check current edited post.
     *
     * @param    core    <b>dcCore</b>    dcCore instance
     * @param    get        <b>array</b>    cleaned $_GET
     *
     * @return    <b>xmlTag</b>    XML representation of response
     */
    public static function checkCurrentPost($core, $get)
    {
        global $core;

        if (empty($get['post_id'])) {
            throw new Exception('No post ID');
        }
        if (empty($get['post_hash'])) {
            throw new Exception('No post Hash');
        }
        if (empty($get['post_dt'])) {
            throw new Exception('No post DT');
        }

        $sqlp = array('post_id' => (integer) $get['post_id']);

        if (isset($get['post_type'])) {
            $sqlp['post_type'] = $get['post_type'];
        } else {
            $sqlp['post_type'] = ''; // Check any type of post
        }

        $rsp = new xmlTag('post');

        $rs = $core->blog->getPosts($sqlp);
        if ($rs->isEmpty()) {
            // Post does not exists yet
            $rsp->ret = 'ok';
        } else {
            $media = new dcMedia($core);
            $rsm   = $media->getPostMedia($rs->post_id);
            $hash  = self::hashPost($rs, $rsm);
            if ($hash == $get['post_hash']) {
                $rsp->ret = 'ok';
            } else {
                // Fire a notification only if it has not been already fired
                $dt = $rs->post_upddt;
                if ($dt != $get['post_dt']) {
                    $rsp->ret     = 'dirty';
                    $rsp->msg     = __('Warning: The current entry has been changed elsewhere!');
                    $rsp->post_dt = $dt;
                } else {
                    $rsp->ret = 'ok';
                }
            }
        }

        return $rsp;
    }

    public static function hashPost($rs, $rsm)
    {
        $l = array();
        if ($rs->fetch()) {
            // Do not take into account nb of comments or trackbacks, neither updated datetime
            $cols = $rs->columns();
            foreach ($cols as $i => $c) {
                if (!in_array($i, array('nb_comment', 'nb_trackback', 'post_upddt'))) {
                    $l[] = $rs->f($c);
                }
            }
        }
        if (!empty($rsm)) {
            foreach ($rsm as $f) {
                $l[] = $f->media_id;
            }
        }
        $str = serialize($l);
        return hash('md5', $str);
    }
}
