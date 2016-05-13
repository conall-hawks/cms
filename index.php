<?php 
	define('BASEPATH', '/');
	
	/* Load the global constants. */
	$file = 'config/constants.php';
	file_exists($file) ? require_once($file) : die('Global constants file expected at "'.$file.'".');
	
	/* Load the global functions. */
	$file = 'classes/common.php';
	file_exists($file) ? require_once($file) : die('Global functions file expected at "'.$file.'".');
	
	/* Instantiate the benchmark class, and set a new mark. */
	$benchmark =& load_class('benchmark');
	$benchmark->mark('start');
	
	/* Include the abstract classes. */
	$files = array('controller', 'model');
	foreach($files as $file) require_once(CLASSES.'/'.$file.'.php');
	
	/* Instantiate some essential classes. */
	$security =& load_class('security');
	$uri =& load_class('uri');
	$input =& load_class('input');
	$language =& load_class('language');
	$login =& load_class('login');
	
	/* Instantiate the requested controller. */
	${$uri->class} =& load_class($uri->class, CONTROLLERS);
	
	/* Instantiate the CMS if it is needed. */
	if(${$uri->class}->use_cms) $cms =& load_class('cms');
	
	/* Call the requested method. */
	if(method_exists(${$uri->class}, $uri->method)){
		if($uri->method == 'view'){
			/* Render output to the browser. */
			${$uri->class}->view($uri->arguments);
		}else{
			${$uri->class}->{$uri->method}($uri->arguments);
		}
	}else{
		${$uri->class}->view($uri->class);
		die('Method "'.$uri->method.'" not found in class "'.$uri->class.'".');
	}