<?php defined('SYSPATH') or die('No direct script access.');

// Disable in CLI
if (Kohana::$is_cli)
	return;

Route::set('logdelete', 'logs/delete/<year>/<month>/<logfile>', array('logfile' => '.*'))
	->defaults(array(
		'controller' => 'logs',
		'action'     => 'delete',
	));
Route::set('logviewer', 'logs/(<year>(/<month>(/<day>(/<level>))))')
	->defaults(array(
		'controller' => 'logs',
		'action'     => 'index',
	));
