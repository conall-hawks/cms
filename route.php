<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); $route = [];
/**----------------------------------------------------------------------------\
| Customize URI routes; maps URI requests to specific controller functions.    |
+------------------------------------------------------------------------------+
| Example usage:                                                               |
|                                                                              |
|     // Make the front page of /pix point to my Instagram controller.         |
|     $route['/pix'] = '/instagram/rss';                                       |
|                                                                              |
|     // Make any number after /news point to my custom method.                |
|     $route['/news/(:num)'] = '/news/view_article';                           |
|                                                                              |
|     // Make anything after /news point to my custom method.                  |
|     $route['/news/(:any)'] = '/news/feed';                                   |
|                                                                              |
|     // RegEx capturing groups, this will take the 2nd                        |
|     // matching--the (:num)--and put it in the $2.                           |
|     $route['/imageboard/(:any)/(:num)'] = '/imageboard/thread/$2';           |
|                                                                              |
|     // Make anything (recursively) after /news point to my custom method.    |
|     // Custom routing; useful for things like file explorers and CMSs.       |
|     $route['/news/(:all)'] = '/news/get_article_by_name';                    |
|                                                                              |
\-----------------------------------------------------------------------------*/

/* Index */
$route['/'] = '/index';

/* News */
$route['/newz(:all)'] = '/news$1';

/* Imageboard */
$route['/pix/progress']       = '/pix/progress';
$route['/pix/select']         = '/pix/select';
$route['/pix/(:any)']         = '/pix/board';
$route['/pix/(:any)/(:num)']  = '/pix/thread/$2';

/* Profile */
$route['/profile/(:any)']              = '/profile';
$route['/profile/(:any)/upload(:all)'] = '/upload/private$2';

/* CAPTCHA link masquerading as an image file. */
$route['/asset/image/captcha.png'] = '/captcha';

/* Direct links to file uploads. */
$route['/asset/upload/public/(:all)'] = '/404';

/* Workarounds; buggy URLs. */
$route['/sm/(:any).map'] = '/asset/misc/blank.map';

/* Development only. */
if(ENVIRONMENT !== 'development'){
    $route['/test(:all)'] = '/404';
}

/* Meta classes; disabled. */
#$classes = array_diff(scandir('class'), ['..', '.']);
#foreach($classes as $class){
#    $route['/'.pathinfo($class, PATHINFO_FILENAME).'(:all)'] = '/404';
#}
