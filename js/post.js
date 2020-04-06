/*global $, dotclear, notifyBrowser, getData */
'use strict';

function checkCurrentPost() {
  $.get('services.php', {
      f: 'notifyMeCheckCurrentPost',
      xd_check: dotclear.nonce,
      post_id: dotclear.notify_me.post.id,
      post_hash: dotclear.notify_me.post.hash,
      post_dt: dotclear.notify_me.post.dt,
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
          notifyBrowser($('rsp>post', data).attr('msg'), dotclear.notify_me.config.title);
          dotclear.notify_me.post.dt = $('rsp>post', data).attr('post_dt');
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
  dotclear.notify_me.post = getData('notify_me_post');
  // Set interval between two checks for current post
  setInterval(checkCurrentPost, dotclear.notify_me.post.check);
});
