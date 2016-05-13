<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	/* General constants. */
	define('ENVIRONMENT', 'development');
	define('TITLE', 'Cenari.us');
	
	/* General credentials. */
	define('ROOT', 'root');
	define('ROOTPASS', '');
	define('ADMIN', 'admin');
	define('ADMINPASS', 'password');
	
	/* Database credentials. */
	define('DB_TYPE', 'mysql');
	define('DB_HOST', '127.0.0.1');
	define('DB_USER', ROOT);
	define('DB_PASS', ROOTPASS);
	
	/* SMTP credentials. */
	define('SMTP_USER', 'cenari.verifier@gmail.com');
	define('SMTP_PASS', '');
	
	/* Instagram credentials. */
	define('INSTAGRAM_ID', '');
	define('INSTAGRAM_SECRET', '');
	
	/* Change this key to invalidate all cookies. */
	define('COOKIE_KEY', 'cAbreSpaTEmede8a');
	
	/* Folder locations. */
	define('CLASSES', 'classes');
	define('CONFIG', 'config');
	define('CONTROLLERS', 'controllers');
	define('IMAGES', 'images');
	define('INCLUDES', 'includes');
	define('MODELS', 'models');
	define('TEMPLATES', 'templates');
	define('VIEWS', 'views');

	/* Confirm the above folder locations. */
	$folders = array(CLASSES, CONTROLLERS, IMAGES, INCLUDES, MODELS, TEMPLATES, VIEWS);
	foreach($folders as $folder) if(!is_dir($folder)) mkdir($folder);