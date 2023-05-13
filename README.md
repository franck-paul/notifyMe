# Plugin notifyMe for Dotclear 2

[![Release](https://img.shields.io/github/v/release/franck-paul/notifyMe)](https://github.com/franck-paul/notifyMe/releases)
[![Date](https://img.shields.io/github/release-date/franck-paul/notifyMe)](https://github.com/franck-paul/notifyMe/releases)
[![Issues](https://img.shields.io/github/issues/franck-paul/notifyMe)](https://github.com/franck-paul/notifyMe/issues)
[![Dotaddict](https://img.shields.io/badge/dotaddict-official-green.svg)](https://plugins.dotaddict.org/dc2/details/notifyMe)
[![License](https://img.shields.io/github/license/franck-paul/notifyMe)](https://github.com/franck-paul/notifyMe/blob/master/LICENSE)

![Notification sample](http://open-time.net/public/screenshots/2015/notify-me-display.jpg)

This plugin display notification in your admin' pages when new comments/trackbacks are posted on the currently selected blog, or if the currently edited post (or page) has been modified elsewhere (other browser, machine, user, …).

![Plugin settings](http://open-time.net/public/screenshots/2015/notify-me-prefs.jpg)

By default new comments are checked every 30 seconds and entries' modifications every 60 seconds. Note that spam comments/trackbacks do not fire any notification, neither your own comments or trackbacks. Theses intervals may be changed in "My preferences", tab "My options".

Each kind of notification may be disabled or enabled regardless the plugin is enabled or not.

## API

How to display browser notification using this plugin.

### in Javascript

## Legacy way

Load (if necessary) /js/notify.js, and call notifyBrowser(msg[,title]):

```html
<script src="index.php?pf=NotifyMe/js/notify.js"></script>
<script>
    notifyBrowser('Hello world!');
</script>
```

## Using module

```html
<script>
    import { notifyBrowser } from './index.php?pf=notifyMe/js/notify.mjs';
    notifyBrowser('Hello world!');
</script>
```

Or:

```html
<script>
    const notifyMe = import('./index.php?pf=notifyMe/js/notify.mjs');
    notifyMe.notifyBrowser('Hello world!');
</script>
```

### in PHP

```php
#!php
use Dotclear\Plugin\notifyMe\Helper as NotifyMe;
NotifyMe::NotifyBrowser(msg[,title]);
```

## LICENCE

GPL v2, fork and distribute it freely. You may pay me a 🍺 or even 🍻 if you find this plugin useful!

## Known Issues

- ⚡ autosave plugin will fire a notification at every save.
