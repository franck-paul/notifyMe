/*global $, dotclear, notifyBrowser, getData */
'use strict';

function checkNewComments() {
  $.get('services.php', {
      f: 'notifyMeCheckNewComments',
      xd_check: dotclear.nonce,
      last_id: dotclear.notify_me.comments.id
    })
    .done(function(data) {
      if ($('rsp[status=failed]', data).length > 0) {
        // For debugging purpose only:
        // console.log($('rsp',data).attr('message'));
        window.console.log('Dotclear REST server error');
      } else {
        const new_comments = Number($('rsp>check', data).attr('ret'));
        if (new_comments > 0) {
          notifyBrowser($('rsp>check', data).attr('msg'), dotclear.notify_me.config.title);
          dotclear.notify_me.comments.id = $('rsp>check', data).attr('last_id');
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
  dotclear.notify_me.comments = getData('notify_me_comments');
  // Set interval between two checks for new comments
  setInterval(checkNewComments, dotclear.notify_me.comments.check);
});
