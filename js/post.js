/*global $, dotclear, notifyBrowser */
/*exported notifyMe_checkCurrentPost_Timer */
'use strict';

function checkCurrentPost() {
  var params = {
    f: 'notifyMeCheckCurrentPost',
    xd_check: dotclear.nonce,
    post_id: dotclear.notifyMe_CurrentPostId,
    post_hash: dotclear.notifyMe_CurrentPostHash,
    post_dt: dotclear.notifyMe_CurrentPostDT,
    post_type: '' // check any type of post
  };
  $.get('services.php', params, function(data) {
    if ($('rsp[status=failed]', data).length > 0) {
      // For debugging purpose only:
      // console.log($('rsp',data).attr('message'));
      window.console.log('Dotclear REST server error');
    } else {
      var dirty_post = $('rsp>post', data).attr('ret');
      if (dirty_post == 'dirty') {
        notifyBrowser($('rsp>post', data).attr('msg'), dotclear.notifyMe_Title);
        dotclear.notifyMe_CurrentPostDT = $('rsp>post', data).attr('post_dt');
      }
    }
  });
}
// Set interval between two checks for current post
var notifyMe_checkCurrentPost_Timer = setInterval(checkCurrentPost, dotclear.notifyMe_CheckCurrentPost);
