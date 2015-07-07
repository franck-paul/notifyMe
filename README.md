Plugin notifyMe for Dotclear 2
==============================

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

notifyMe::Notify(msg[,title]);

```
