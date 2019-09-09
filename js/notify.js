/*exported notifyBrowser */
'use strict';

/* Browser notification
   (adpated from https://developer.mozilla.org/fr/docs/Web/API/Notification)
-------------------------------------------------------*/
function notifyBrowser(msg, title, silent) {
  const notify_options = {
    body: msg,
    icon: 'images/favicon.ico',
    silent: false
  };

  // Set title to default value if not provided
  title = title || 'Dotclear';

  // Set silent option to false if not defined
  silent = silent || false;
  if (silent) {
    notify_options.silent = true;
  }

  if ('Notification' in window) {
    // Check if user has already granted notification for this session
    if (Notification.permission === 'granted') {
      // Notifications granted, push it
      let notification = new Notification(title, notify_options);
      setTimeout(notification.close.bind(notification), 4000);
    }

    // Else, check if notification has not already been denied
    else if (Notification.permission !== 'denied') {
      // Ask permission for notification for this session
      Notification.requestPermission(function(permission) {

        // Store user's answer
        if (!('permission' in Notification)) {
          Notification.permission = permission;
        }

        // If notification granted, push it
        if (permission === 'granted') {
          let notification = new Notification(title, notify_options);
          setTimeout(notification.close.bind(notification), 4000);
        }
      });
    }
  }
}
