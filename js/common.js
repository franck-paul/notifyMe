/*global dotclear, notifyBrowser */
'use strict';

dotclear.notify_me.checkNewComments = () => {
  dotclear.jsonServicesGet(
    'notifyMeCheckNewComments',
    (payload) => {
      if (payload.ret) {
        notifyBrowser(response.payload.msg, dotclear.notify_me.config.title, dotclear.notify_me.config.wait);
        dotclear.notify_me.comments.id = payload.last_id;
      }
    },
    {
      last_id: dotclear.notify_me.comments.id,
    },
  );
};

window.addEventListener('load', () => {
  dotclear.notify_me.comments = dotclear.getData('notify_me_comments');
  // First check
  dotclear.notify_me.checkNewComments();
  // Set interval between two checks for new comments
  setInterval(dotclear.notify_me.checkNewComments, dotclear.notify_me.comments.check);
});
