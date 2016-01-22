# Kohana 3.x Log Viewer module

## A Kohana module for exploring log files

### Disclaimer

This module forks original one from https://github.com/ajaxray adding suport for Kohana's 3.2 log file format, which includes stack trace

### Installation:

1. Download this module and add the **logviewer** folder it to your `MODPATH`
2. Enable it in the `bootstrap` file
3. Specify custom `bootstrap.css`, `style.css`, `jquery.js` files in config file (copy it from `MODPATH/logviewer/config/logviewer.php` to `APPPATH/config/logviewer.php` and edit. You may skip this step, default assets will be used.
4. If you wish, you can translate LogViewer to any language, look at `MODPATH\logviewer/i18n/ru.php` for example. You may skip this step, if only English language is enough.
5. Go to _http://your-app-root/logs_
6. You are done!

![Kohana Log Viewer interface](http://ajaxray.com/files/log_formatted.png "Kohana Log Viewer interface")

### How to use?

It's completely self explanatory. Here are some points for quick refs -

- All months are listed on top nav. e.g, **2011/11**
- Left sidebar has a list of available log files in selected month
- If not specified, today's (current month and day) log file will be displayed
- If you want to see a fresh log for next call, just delete today's file. Kohana will generate it and add
- Formatted mode (default) may not extract all info correctly for displaying in rows. Use **raw mode** for those situations. _NEED IMPROVEMENT_ here.
- You can use *Level* listbox for filtering by log levels.

### How to limit access to LogViewer

LogViewer use standart Kohana naming convention, so you can easily extend it in your application. For example, if you use standart Auth module, and wish to limit access to LogViewer only for admins, create `APPPATH/classes/Controller/Logs.php` file and type something like:
```
<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Logs extends Kohana_Controller_Logs {
	function before(){
		$auth=new Auth_ORM(Kohana::$config->load('auth'));
		if(!$auth->logged_in('admin'))
			throw HTTP_Exception::factory(403,__('Access denied'))->request($this->request);
		// skip next line, if you don't want to limit log erasure
		$this->_allow_delete=$auth->logged_in('power_admin');
		parent::before();
	}
}
```
If you use your own authentication system, you may easily adapt this code to you conditions

### Notes:

- _http://your-app-root/logs_ should display the log reports interface. If it don't, please check the routing in `modules/logviewer/init.php`
- If you change the folder name, change the paths in `modules/logviewer/views/logs/layout.php` accordingly.
- If you want to improve, please fork and participate.
- If you've a suggestion or found a bug, please let me know at - anisniit(at)gmail.com
- BE CAREFUL ABOUT USING ON PRODUCTION!


