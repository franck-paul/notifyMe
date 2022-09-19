/*global dotclear, notifyBrowser */
'use strict';

function checkNewComments() {
  dotclear.services(
    'notifyMeCheckNewComments',
    (data) => {
      try {
        const response = JSON.parse(data);
        if (response?.success) {
          if (Number(response?.payload.ret) > 0) {
            notifyBrowser(response.payload.msg, dotclear.notify_me.config.title, dotclear.notify_me.config.wait);
            dotclear.notify_me.comments.id = response?.payload.last_id;
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
      last_id: dotclear.notify_me.comments.id,
    },
  );
}

window.addEventListener('load', () => {
  dotclear.notify_me.comments = dotclear.getData('notify_me_comments');
  // Set interval between two checks for new comments
  setInterval(checkNewComments, dotclear.notify_me.comments.check);
});
