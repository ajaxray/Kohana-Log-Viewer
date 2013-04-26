<?php defined('SYSPATH') or die('No direct script access.');

// Disable in CLI
if (defined("PHP_SAPI") && PHP_SAPI == 'cli')
	return;

Route::set('logdelete', 'logs/delete/<year>/<month>/<logfile>', array('logfile' => '.*'))
	->defaults(array(
		'controller' => 'Logs',
		'action'     => 'delete',
	));
Route::set('logviewer', 'logs/(<year>(/<month>(/<day>(/<level>))))')
	->defaults(array(
		'controller' => 'Logs',
		'action'     => 'index',
	));
