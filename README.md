# Plugin notifyMe for Dotclear 2

![Notification sample](http://open-time.net/public/screenshots/2015/notify-me-display.jpg)

This plugin display notification in your admin' pages when new comments/trackbacks are posted on the currently selected blog, or if the currently edited post (or page) has been modified elsewhere (other browser, machine, user, …).

![Plugin settings](http://open-time.net/public/screenshots/2015/notify-me-prefs.jpg)

By default new comments are checked every 30 seconds and entries' modifications every 60 seconds. Note that spam comments/trackbacks do not fire any notification, neither your own comments or trackbacks. Theses intervals may be changed in "My preferences", tab "My options".

Each kind of notification may be disabled or enabled regardless the plugin is enabled or not.

## API

How to display browser notification using this plugin.

### in Javascript

Load (if necessary) /js/notify.js, and call notifyBrowser(msg[,title]):

```html
<script src="index.php?pf=NotifyMe/js/notify.js"></script>
<script>
    notifyBrowser('Hello world!');
</script>
```

### in PHP

Autoload notifyMe class if necessary :

```php
#!php
Clearbricks::lib()->autoload(['notifyMe' => $path_to_plugins.'/notifyMe/_admin.php']);
```

Call Notify() function :

```php
#!php
notifyMe::NotifyBrowser(msg[,title]);
```

## LICENCE

GPL v2, fork and distribute it freely. You may pay me a 🍺 or even 🍻 if you find this plugin useful!

## Known Issues

- ⚡ autosave plugin will fire a notification at every save.

## Ideas

May be implemented if necessary and asked for:

- Publication of programmed posts : "N programmed post(s) has/have been published".
- Incorrect user login (for super-admin) : "Incorrect login occurs."
