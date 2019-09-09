/*global $, dotclear, notifyBrowser */
'use strict';

function checkCurrentPost() {
  $.get('services.php', {
      f: 'notifyMeCheckCurrentPost',
      xd_check: dotclear.nonce,
      post_id: dotclear.notifyMe_CurrentPostId,
      post_hash: dotclear.notifyMe_CurrentPostHash,
      post_dt: dotclear.notifyMe_CurrentPostDT,
      post_type: '' // check any type of post
    })
    .done(function(data) {
      if ($('rsp[status=failed]', data).length > 0) {
        // For debugging purpose only:
        // console.log($('rsp',data).attr('message'));
        window.console.log('Dotclear REST server error');
      } else {
        const dirty_post = $('rsp>post', data).attr('ret');
        if (dirty_post == 'dirty') {
          notifyBrowser($('rsp>post', data).attr('msg'), dotclear.notifyMe_Title);
          dotclear.notifyMe_CurrentPostDT = $('rsp>post', data).attr('post_dt');
        }
      }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      window.console.log(`AJAX ${textStatus} (status: ${jqXHR.status} ${errorThrown})`);
    })
    .always(function() {
      // Nothing here
    });
}

$(function() {
  // Set interval between two checks for current post
  setInterval(checkCurrentPost, dotclear.notifyMe_CheckCurrentPost);
});
