/*global dotclear, notifyBrowser */
'use strict';

function checkCurrentPost() {
  dotclear.services(
    'notifyMeCheckCurrentPost',
    (data) => {
      try {
        const response = JSON.parse(data);
        if (response?.success) {
          if (response?.payload.ret === 'dirty') {
            notifyBrowser(response.payload.msg, dotclear.notify_me.config.title, dotclear.notify_me.config.wait);
            dotclear.notify_me.post.dt = response?.payload.post_dt;
          }
        } else {
          console.log(dotclear.debug && response?.message ? response.message : 'Dotclear REST server error');
          return;
        }
      } catch (e) {
        console.log(e);
      }
    },
    (error) => {
      console.log(error);
    },
    true, // Use GET method
    {
      json: 1, // Use JSON format for payload
      post_id: dotclear.notify_me.post.id,
      post_hash: dotclear.notify_me.post.hash,
      post_dt: dotclear.notify_me.post.dt,
      post_type: '', // check any type of post
    },
  );
}

window.addEventListener('load', () => {
  dotclear.notify_me.post = dotclear.getData('notify_me_post');
  // Set interval between two checks for current post
  setInterval(checkCurrentPost, dotclear.notify_me.post.check);
});
