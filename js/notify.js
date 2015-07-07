/* Browser notification
   (adpated from https://developer.mozilla.org/fr/docs/Web/API/Notification)
-------------------------------------------------------*/
function notifyBrowser(msg,title) {
	var notify_options = {
		body: msg,
		icon: "images/favicon.ico"
	};

	// Set title to default value if not provided
	title = title || 'Dotclear';

	if ("Notification" in window) {
		// Check if user has already granted notification for this session
		if (Notification.permission === "granted") {
			// Notifications granted, push it
			var notification = new Notification(title,notify_options);
		}

		// Else, check if notification has not already been denied
		else if (Notification.permission !== 'denied') {
			// Ask permission for notification for this session
			Notification.requestPermission(function (permission) {

				// Store user's answer
				if(!('permission' in Notification)) {
					Notification.permission = permission;
				}

				// If notification granted, push it
				if (permission === "granted") {
					var notification = new Notification(title,notify_options);
				}
			});
		}
	}
}
