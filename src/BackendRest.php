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
use Dotclear\Database\MetaRecord;
use Dotclear\Interface\Core\BlogInterface;
use Exception;

class BackendRest
{
    /**
     * Serve method to check new comments for current blog.
     *
     * @param      array<string, string>      $get    The cleaned $_GET
     *
     * @return     array<string, mixed>   The payload.
     */
    public static function checkNewComments(array $get): array
    {
        $last_id         = !empty($get['last_id']) ? $get['last_id'] : -1;
        $last_comment_id = -1;

        $sqlp = [
            'no_content'         => true,                   // content is not required
            'comment_status_not' => BlogInterface::COMMENT_JUNK,   // ignore spam
            'order'              => 'comment_id ASC',

            'sql' => 'AND comment_id > ' . $last_id, // only new ones
        ];

        $email = App::auth()->getInfo('user_email');
        $url   = App::auth()->getInfo('user_url');
        if ($email && $url) {
            // Ignore own comments/trackbacks
            $sqlp['sql'] .= " AND (comment_email <> '" . $email . "' OR comment_site <> '" . $url . "')";
        }

        $rs    = App::blog()->getComments($sqlp);
        $count = $rs->count();

        if ($count) {
            while ($rs->fetch()) {
                $last_comment_id = $rs->comment_id;
            }
        }

        return [
            'ret'     => $count,
            'msg'     => sprintf(__('One new comment has been posted', '%s new comments have been posted', $count), $count),
            'suggest' => $last_comment_id,
        ];
    }

    /**
     * Serve method to check current edited post.
     *
     * @param      array<string, string>      $get    The cleaned $_GET
     *
     * @throws     Exception
     *
     * @return     array<string, mixed>   The payload.
     */
    public static function checkCurrentPost(array $get): array
    {
        if (empty($get['post_id'])) {
            throw new Exception('No post ID');
        }
        if (empty($get['post_hash'])) {
            throw new Exception('No post Hash');
        }
        if (empty($get['post_dt'])) {
            throw new Exception('No post DT');
        }

        $sqlp = ['post_id' => (int) $get['post_id']];

        if (isset($get['post_type'])) {
            $sqlp['post_type'] = $get['post_type'];
        } else {
            $sqlp['post_type'] = ''; // Check any type of post
        }

        $payload = [
            'ret' => 'ok',
        ];

        $rs = App::blog()->getPosts($sqlp);
        if (!$rs->isEmpty()) {
            $media = App::media();
            $rsm   = $media->getPostMedia((int) $rs->post_id);
            $hash  = self::hashPost($rs, $rsm);
            if ($hash !== $get['post_hash']) {
                // Fire a notification only if it has not been already fired
                $dt = $rs->post_upddt;
                if ($dt !== $get['post_dt']) {
                    $payload = [
                        'ret'     => 'dirty',
                        'msg'     => __('Warning: The current entry has been changed elsewhere!'),
                        'post_dt' => $dt,
                    ];
                }
            }
        }

        return $payload;
    }

    /**
     * @param      MetaRecord                               $rs     Recordset
     * @param      array<int,\Dotclear\Helper\File\File>    $rsm    The rsm
     *
     * @return     string
     */
    public static function hashPost(MetaRecord $rs, array $rsm): string
    {
        $l = [];
        if ($rs->fetch()) {
            // Do not take into account nb of comments or trackbacks, neither updated datetime
            $cols = $rs->columns();
            foreach ($cols as $i => $c) {
                if (!in_array($i, ['nb_comment', 'nb_trackback', 'post_upddt'])) {
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
