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

// dead but useful code, in order to have translations
__('Browser notifications').__('Display notifications in your web browser');

$core->addBehavior('adminBeforeUserOptionsUpdate',array('notifyMeBehaviors','adminBeforeUserOptionsUpdate'));
$core->addBehavior('adminPreferencesForm',array('notifyMeBehaviors','adminPreferencesForm'));

$core->addBehavior('adminPageHTMLHead',array('notifyMeBehaviors','adminPageHTMLHead'));

class notifyMeBehaviors
{
	public static function adminBeforeUserOptionsUpdate($cur,$userID)
	{
		global $core;

		// Get and store user's prefs for plugin options
		$core->auth->user_prefs->addWorkspace('notifyMe');
		try {
			$interval = (integer) $_POST['notifyMe_interval'];
			if ($interval < 1) {
				$interval = 30;	// seconds
			}
			$core->auth->user_prefs->notifyMe->put('active',!empty($_POST['notifyMe_active']),'boolean');
			$core->auth->user_prefs->notifyMe->put('new_comments',$interval);
		}
		catch (Exception $e)
		{
			$core->error->add($e->getMessage());
		}
	}

	public static function adminPreferencesForm($core)
	{
		// Add fieldset for plugin options
		$core->auth->user_prefs->addWorkspace('notifyMe');

		echo
		'<div class="fieldset"><h5>'.__('Browser notifications').'</h5>'.

		'<p><label for="notifyMe_active" class="classic">'.
		form::checkbox('notifyMe_active',1,$core->auth->user_prefs->notifyMe->active).' '.
		__('Display browser notification').'</label></p>'.

		'<p><label for="notifyMe_interval">'.__('Check new comments every (in seconds, default: 30):').' '.
		form::field('notifyMe_interval',5,4,(integer) $core->auth->user_prefs->notifyMe->new_comments).'</label></p>'.

		'<p class="form-note">'.__('Only new non-junk comments for the current blog will be checked, whatever is the moderation setting.').'</p>'.

		'</div>';
	}

	public static function adminPageHTMLHead()
	{
		global $core;

		$core->auth->user_prefs->addWorkspace('notifyMe');
		if ($core->auth->user_prefs->notifyMe->active) {

			$params = array(
				'limit' => 1,					// only the last one
				'no_content' => true,			// content is not required
				'comment_status_not' => -2,		// ignore spam
				'order' => 'comment_id DESC'	// get last first
				);
			$comments = $core->blog->getComments($params);
			$count = $core->blog->getComments($params,true);

			if ($count) {
				$comments->fetch();
				$last_comment_id = $comments->comment_id;
			} else {
				$last_comment_id = -1;
			}

			// Set notification title
			$title = sprintf(__('Dotclear : %s'),$core->blog->name);

			// Get interval between two check
			$interval = (integer) $core->auth->user_prefs->notifyMe->new_comments;
			if (!$interval) {
				$interval = 30;	// 30 seconds by default
			}

			echo
				'<script type="text/javascript">'."\n".
				"//<![CDATA[\n".
				dcPage::jsVar('dotclear.notifyMe_Title',$title).
				dcPage::jsVar('dotclear.notifyMe_Interval',$interval * 1000).
				dcPage::jsVar('dotclear.notifyMe_LastCommentId',$last_comment_id).
				"\n//]]>\n".
				"</script>\n".
				'<script type="text/javascript" src="index.php?pf=notifyMe/js/notify.js"></script>'.
				'<script type="text/javascript" src="index.php?pf=notifyMe/js/services.js"></script>';
		}
	}
}

class notifyMe
{
	public static function Notify($message,$title='Dotclear')
	{
		echo '<script type="text/javascript">'.
			'notifyBrowser("'.
				html::escapeJS(str_replace("\n",'. ',$message))."','".
				html::escapeJS($title)."');".
			'</script>';
	}
}
