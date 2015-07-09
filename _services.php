<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of notifyMe, a plugin for Dotclear 2.
#
# Copyright (c) Franck Paul and contributors
#
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_CONTEXT_ADMIN')) { return; }

class notifyMeRest
{
	/**
	 * Serve method to check new comments for current blog.
	 *
	 * @param	core	<b>dcCore</b>	dcCore instance
	 * @param	get		<b>array</b>	cleaned $_GET
	 *
	 * @return	<b>xmlTag</b>	XML representation of response
	 */
	public static function checkNewComments($core,$get)
	{
		global $core;

		$last_id = !empty($get['last_id']) ? $get['last_id'] : -1;

		$params = array(
			'no_content' => true,			// content is not required
			'comment_status_not' => -2,		// ignore spam
			'order' => 'comment_id ASC',

			'sql' => 'AND comment_id > '.$last_id 		// only new ones
			);

		$email = $core->auth->getInfo('user_email');
		$url = $core->auth->getInfo('user_url');
		if ($email && $url) {
			// Ignore own comments/trackbacks
			$params['sql'] .= " AND (comment_email <> '".$email."' OR comment_site <> '".$url."')";
		}

		$comments = $core->blog->getComments($params);
		$count = $core->blog->getComments($params,true)->f(0);

		if ($count) {
			while ($comments->fetch()) {
				$last_comment_id = $comments->comment_id;
			}
		} else {
			$last_comment_id = -1;
		}
		$rsp = new xmlTag('check');
		$rsp->ret = $count;
		if ($count) {
			$rsp->msg = sprintf(__('One new comment has been posted','%s new comments have been posted',(int)$count),$count);
			$rsp->last_id = $last_comment_id;
		}

		return $rsp;
	}

	/**
	 * Serve method to check current edited post.
	 *
	 * @param	core	<b>dcCore</b>	dcCore instance
	 * @param	get		<b>array</b>	cleaned $_GET
	 *
	 * @return	<b>xmlTag</b>	XML representation of response
	 */
	public static function checkCurrentPost($core,$get)
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

		$params = array('post_id' => (integer) $get['post_id']);

		if (isset($get['post_type'])) {
			$params['post_type'] = $get['post_type'];
		} else {
			$params['post_type'] = '';	// Check any type of post
		}

		$rsp = new xmlTag('post');

		$rs = $core->blog->getPosts($params);
		if ($rs->isEmpty()) {
			// Post does not exists yet
			$rsp->ret = 'ok';
		} else {
			$core->media = new dcMedia($core);
			$rs_media = $core->media->getPostMedia($rs->post_id);
			$hash = self::hashPost($rs,$rs_media);
			if ($hash == $get['post_hash']) {
				$rsp->ret = 'ok';
			} else {
				// Fire a notification only if it has not been already fired
				$dt = $rs->post_upddt;
				if ($dt != $get['post_dt']) {
					$rsp->ret = 'dirty';
					$rsp->msg = __('Warning: The current entry has been changed elsewhere!');
					$rsp->post_dt = $dt;
				} else {
					$rsp->ret = 'ok';
				}
			}
		}

		return $rsp;
	}

	public static function hashPost($rs,$rs_media)
	{
		$l = array();
		if ($rs->fetch()) {
			// Do not take into account nb of comments or trackbacks, neither updated datetime
			$cols = $rs->columns();
			foreach ($cols as $i => $c) {
				if (!in_array($i, array('nb_comment','nb_trackback','post_upddt'))) {
					$l[] = $rs->f($c);
				}
			}
		}
		if (!empty($rs_media)) {
			foreach ($rs_media as $f) {
				$l[] = $f->media_id;
			}
		}
		$str = serialize($l);
		return hash('md5',$str);
	}
}
