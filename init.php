<?php defined('SYSPATH') or die('No direct script access.');

// Disable in CLI
if (defined("PHP_SAPI") && PHP_SAPI == 'cli')
	return;

Route::set('logviewer', 'logs/(<year>(/<month>(/<day>(/<level>))))',array('year'=>'\d+','month'=>'\d+','day'=>'\d+'))
	->defaults(array(
		'controller' => 'Logs',
		'action'     => 'index',
	));

Route::set('logviewerAsset', 'logs/<filename>',array('filename'=>'[A-Za-z\.]+'))
	->defaults(array(
		'controller' => 'Logs',
		'action'     => 'asset',
	));