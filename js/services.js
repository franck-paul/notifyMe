function checkNewComments() {
	var params = {
		f: 'notifyMeCheckNewComments',
		xd_check: dotclear.nonce,
		last_id: dotclear.notifyMe_LastCommentId
	};
	$.get('services.php',params,function(data) {
		if ($('rsp[status=failed]',data).length > 0) {
			// For debugging purpose only:
			// console.log($('rsp',data).attr('message'));
			console.log('Dotclear REST server error');
		} else {
			var new_comments = Number($('rsp>check',data).attr('ret'));
			if (new_comments > 0) {
				notifyBrowser($('rsp>check',data).attr('msg'),dotclear.notifyMe_Title);
				dotclear.notifyMe_LastCommentId = $('rsp>check',data).attr('last_id');
			}
		}
	});
}

// Set interval between two checks for new comments
var notifyMe_checkNewComments_Timer = setInterval(checkNewComments,dotclear.notifyMe_Interval);
