Plugin notifyMe for Dotclear 2
==============================

howto display browser notification using this plugin :

1. in Javascript

Load (if necessary) /js/notify.js, and call notifyBrowser(msg[,title]):

<pre>
\<script type="text/javascript" src="index.php?pf=NotifyMe/js/notify.js"></script>
\<script type="text/javascript">
	notifyBrowser('Hello world!');
\</script>
</pre>

2. in PHP

Autoload notifyMe class if necessary :
<pre>
$\_\_autoload['notifyMe'] = dirname(\_\_FILE\_\_).'/_admin.php';
</pre>

Call Notify() function :
<pre>
notifyMe::Notify(msg[,title]);
</pre>
