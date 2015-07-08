# Plugin notifyMe for Dotclear 2

![Notification sample](http://open-time.net/public/screenshots/2015/notify-me-display.jpg)

This plugin display notification in your admin' pages when new comments/trackbacks are posted on the currently selected blog, or if the currently edited post (or page) has been modified elsewhere (other browser, machine, user, …).

![Plugin settings](http://open-time.net/public/screenshots/2015/notify-me-prefs.jpg)

By default new comments are checked every 30 seconds and entries' modifications every 60 seconds. Note that spam comments/trackbacks do not fire any notification. Theses intervals may be changed in "My preferences", tab "My options".

## API

howto display browser notification using this plugin :

1. in Javascript

Load (if necessary) /js/notify.js, and call notifyBrowser(msg[,title]):


```
#!html

<script type="text/javascript" src="index.php?pf=NotifyMe/js/notify.js"></script>
<script type="text/javascript">
	notifyBrowser('Hello world!');
</script>

```

2. in PHP

Autoload notifyMe class if necessary :

```
#!php
$__autoload['notifyMe'] = dirname(__FILE__).'/_admin.php';
```

Call Notify() function :

```
#!php
notifyMe::NotifyBrowser(msg[,title]);
```

## LICENCE

GPL v2, fork and distribute it freely. You may pay me a 🍺 or even 🍻 if you find this plugin useful!

## CHANGELOG

0.1 - 2015/07/07 (not public release)

- Initial packaging with new comments event

0.2 - 2015/07/08 (first public release)

- Add currently edited entry survey
