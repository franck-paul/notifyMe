/*global $, dotclear, notifyBrowser, getData */
'use strict';

dotclear.notify_me = {
  queue: [],
  add: function(msg, title = null, silent = false) {
    dotclear.notify_me.queue.push({
      message: msg,
      title: title,
      silent: silent
    });
  },
  check: function() {
    // Look for timestamped message(s)
    const list = document.querySelectorAll("[id^='notify_me_msg_']");
    if (list.length) {
      list.forEach(function(elt) {
        let id = elt.getAttribute('id');
        if (id.match(/-data$/)) {
          id = elt.getAttribute('id').substring(0, id.length - 5);
          let msg = getData(id);
          if (Object.keys(msg).length !== 0 && msg.constructor === Object) {
            dotclear.notify_me.add(msg.message, msg.title || null, msg.silent || false);
          }
        }
      });
    }
    // Look for generic message
    let data = getData('notify_me_msg');
    if (Object.keys(data).length !== 0 && data.constructor === Object) {
      dotclear.notify_me.add(data.message, data.title || null, data.silent || false);
    }
  },
  display: function() {
    if (dotclear.notify_me.queue.length) {
      dotclear.notify_me.queue.forEach(function(elt) {
        notifyBrowser(elt.message, elt.title || null, elt.silent || false);
      });
      dotclear.notify_me.queue = [];
    }
  }
};

$(function() {
  dotclear.notify_me.config = getData('notify_me_config');
  // Set interval to get new message
  setInterval(dotclear.notify_me.check, 100);
  // Set interval to empty queue
  setInterval(dotclear.notify_me.display, 100);
});
