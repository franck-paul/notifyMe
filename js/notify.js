/*exported notifyBrowser */
'use strict';

/* Browser notification
   (adpated from https://developer.mozilla.org/fr/docs/Web/API/Notification)
-------------------------------------------------------*/
function notifyBrowser(msg, title = 'Dotclear', silent = false, wait = false) {
  const notify_options = {
    body: msg,
    icon: 'images/favicon.ico',
    silent,
    requireInteraction: wait,
  };

  if ('Notification' in window) {
    // Check if user has already granted notification for this session
    if (Notification.permission === 'granted') {
      // Notifications granted, push it
      const notification = new Notification(title, notify_options);
      if (wait === false) {
        setTimeout(notification.close.bind(notification), 4000);
      }
    }

    // Else, check if notification has not already been denied
    else if (Notification.permission !== 'denied') {
      // Ask permission for notification for this session
      Notification.requestPermission((permission) => {
        // Store user's answer
        if (!('permission' in Notification)) {
          Notification.permission = permission;
        }

        // If notification granted, push it
        if (permission === 'granted') {
          const notification = new Notification(title, notify_options);
          if (wait === false) {
            setTimeout(notification.close.bind(notification), 4000);
          }
        }
      });
    }
  }
}
