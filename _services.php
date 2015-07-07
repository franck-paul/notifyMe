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
}
