/*global dotclear, notifyBrowser */
'use strict';

window.addEventListener('load', () => {
  // Load settings
  dotclear.notify_me.post = dotclear.getData('notify_me_post');

  // Helper
  dotclear.notify_me.checkCurrentPost = () => {
    dotclear.jsonServicesGet(
      'notifyMeCheckCurrentPost',
      (payload) => {
        if (payload.ret === 'dirty') {
          notifyBrowser(payload.msg, dotclear.notify_me.config.title, dotclear.notify_me.config.wait);
          dotclear.notify_me.post.dt = payload.post_dt;
        }
      },
      {
        post_id: dotclear.notify_me.post.id,
        post_hash: dotclear.notify_me.post.hash,
        post_dt: dotclear.notify_me.post.dt,
        post_type: '', // check any type of post
      },
    );
  };

  // Set interval between two checks for current post
  setInterval(dotclear.notify_me.checkCurrentPost, dotclear.notify_me.post.check);
});
