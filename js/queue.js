/*global dotclear, notifyBrowser */
'use strict';

dotclear.notify_me = {
  queue: [],
  running: false,
  add(msg, title = null, silent = false) {
    dotclear.notify_me.queue.push({
      message: msg,
      title,
      silent,
    });
  },
  check() {
    if (dotclear.notify_me.running) {
      // Still in action, pass my turn
      return;
    }
    dotclear.notify_me.running = true;
    // Look for timestamped message(s)
    const list = document.querySelectorAll("[id^='notify_me_msg_']");
    if (list.length) {
      for (const elt of list) {
        let id = elt.getAttribute('id');
        if (id.match(/-data$/)) {
          id = elt.getAttribute('id').substring(0, id.length - 5);
          const msg = dotclear.getData(id);
          if (Object.keys(msg).length !== 0 && msg.constructor === Object) {
            dotclear.notify_me.add(msg.message, msg.title || null, msg.silent || false);
          }
        }
      }
    }
    // Look for generic message
    const data = dotclear.getData('notify_me_msg');
    if (Object.keys(data).length !== 0 && data.constructor === Object) {
      dotclear.notify_me.add(data.message, data.title || null, data.silent || false);
    }
    // Display notifications
    if (dotclear.notify_me.queue.length) {
      for (const elt of dotclear.notify_me.queue) {
        notifyBrowser(elt.message, elt.title || null, elt.silent || false, dotclear.notify_me.config.wait);
      }
      dotclear.notify_me.queue = [];
    }
    dotclear.notify_me.running = false;
  },
};

window.addEventListener('load', () => {
  dotclear.notify_me.config = dotclear.getData('notify_me_config');
  // Set interval to empty queue
  setInterval(dotclear.notify_me.check, 100);
});
