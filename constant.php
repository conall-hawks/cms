<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**--------------------------------------------------------------------------------------------------------------------\
| Global constants.                                                                                                    |
\---------------------------------------------------------------------------------------------------------------------*/

/* Environment. */
#define('ENVIRONMENT', 'development');
#define('ENVIRONMENT', 'production');
define('ENVIRONMENT', $_SERVER['SERVER_ADDR'] === '::1' || explode('.', $_SERVER['SERVER_ADDR'])[0] === '127' || $_SERVER['SERVER_NAME'] === 'localhost' ? 'development' : 'production');

/* Import private constants in production environment. */
if(ENVIRONMENT === 'production' && file_exists('private.php')) require('private.php');

/* Directory paths. */
if(!defined('CONTROLLER')) define('CONTROLLER', 'controller');
if(!defined('LIBRARY'))    define('LIBRARY'   , 'library');
if(!defined('MODEL'))      define('MODEL'     , 'model');
if(!defined('VIEW'))       define('VIEW'      , 'view');

if(!defined('ASSET'))      define('ASSET'     , 'asset');
if(!defined('CSS'))        define('CSS'       , ASSET.'/css');
if(!defined('JS'))         define('JS'        , ASSET.'/js');
if(!defined('IMAGE'))      define('IMAGE'     , ASSET.'/image');

if(!defined('TEMPLATE'))   define('TEMPLATE'  , 'template');
if(!defined('ASIDE'))      define('ASIDE'     , TEMPLATE.'/aside');

/* File paths. */
if(!defined('FAVICON')) define('FAVICON', ASSET.'/favicon.ico');

/* Metadata. */
if(!defined('TITLE'))  define('TITLE' , ucfirst(preg_replace('/.*?([^\.]+)(\.((co\.\w+)|\w+))$/i','\1\2', filter_var($_SERVER['SERVER_NAME'], FILTER_VALIDATE_IP) || filter_var(trim($_SERVER['SERVER_NAME'], '[]'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? 'localhost' : $_SERVER['SERVER_NAME'])));
if(!defined('AUTHOR')) define('AUTHOR', 'Anonymous');

/* General credentials. */
if(!defined('ROOT'))      define('ROOT'     , 'root');
if(!defined('ROOTPASS'))  define('ROOTPASS' , 'toor');
if(!defined('ADMIN'))     define('ADMIN'    , 'admin');
if(!defined('ADMINPASS')) define('ADMINPASS', 'password');

/* Database credentials. */
if(!defined('DB_TYPE')) define('DB_TYPE', 'mysql');
if(!defined('DB_HOST')) define('DB_HOST', 'localhost'); // Note: 127.0.0.1 requires: "setsebool httpd_can_network_connect_db 1".
if(!defined('DB_NAME')) define('DB_NAME', mb_strtolower(TITLE));
if(!defined('DB_USER')) define('DB_USER', 'root');
if(!defined('DB_PASS')) define('DB_PASS', '');

/* Google Adsense credentials. */
if(!defined('ADSENSE_CLIENT_KEY')) define('ADSENSE_CLIENT_KEY', 'pub-0000000000000000');
if(!defined('ADSENSE_SLOT_KEY'))   define('ADSENSE_SLOT_KEY'  , '0000000000');

/* Google Analytics credentials. */
if(!defined('ANALYTICS_KEY')) define('ANALYTICS_KEY', 'UA-00000000-0');
