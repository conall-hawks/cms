<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	/* 
	 * Map URI requests to specific controller functions.
	 * Wildcard examples: $route['(:any)'], $route['(:num)']
	 *
	 */
	$route['programming/(:any)'] = 'programming';
	$route['programming/(:any)/(:any)'] = 'programming';
	$route['programming/(:any)/(:any)/(:any)'] = 'programming';
	
	$route['imageboard/(:any)'] = 'imageboard/view/imageboard/thread';
	$route['imageboard/(:any)/(:num)'] = 'imageboard/view/imageboard/thread';
	$route['imageboard/(:any)/(:any)'] = '404';
	
	$route['starcraft-2/(:any)'] = 'starcraft-2';
	$route['starcraft-2/(:any)/(:any)'] = 'starcraft-2';
	
	$route['profile/(:any)'] = 'profile/home';
	$route['profile/(:any)/settings'] = 'profile/settings';
	$route['miscellaneous/(:any)'] = 'miscellaneous';/*
	$route['programming'] = 'navigation';
	$route['programming/(:any)/(:any)'] = 'navigation';
	$route['security'] = 'navigation';
	$route['security/(:any)'] = 'navigation';
	$route['tips/(:any)'] = 'tips/categories';
	$route['world-of-warcraft'] = 'navigation';
	$route['world-of-warcraft/(:any)'] = 'navigation';
	$route['world-of-warcraft/(:any)/(:any)'] = 'navigation';
	$route['world-of-warcraft/guild-portal/forum'] = 'forum/guild';
	$route['world-of-warcraft/guild-portal/raid-signup'] = 'guild-portal/raid-signup';*/