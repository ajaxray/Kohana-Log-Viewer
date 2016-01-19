<?php defined('SYSPATH') or die('No direct script access.');

return array(
    'log_path'    => APPPATH . 'logs',
		// if your admin can delete logs, set this to true)
		'allow_delete'=>true,
		// if your site ALREADY use bootstrap, or JQuery, specify absolute path (or call URL::site manually) to used files
		'bootstrap_css' => '', // location of bootstrap.min.css (bootstrap's style sheet, full or minimized) empty string = default asset
		'style_css' => '', // location of custom styles
		'jquery_js' => '', // location of jquery (version may include, CDN expected)

);
