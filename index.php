<?php ob_start(); define('ROOTPATH' , '/');
/**--------------------------------------------------------------------------------------------------------------------\
| Global entry point.                                                                                                  |
\---------------------------------------------------------------------------------------------------------------------*/

/* Define constants. */
require('constant.php');

/* Define functions. */
require('function.php');

/* Load abstract classes. */
require('class/controller.php');
require('class/model.php');
require('class/view.php');

/* Enforce security. */
require('class/security.php');
$security = new Security;

/* Resolve the URI. */
require('class/uri.php');
$uri = new URI;

/**----------------------------------------------------------------------------\
| Load the requested controller.                                               |
\-----------------------------------------------------------------------------*/

/* Load controller if one is present. */
$controller = CONTROLLER.'/'.$uri->class.'.php';
if(file_exists($controller)) require($controller);
if(class_exists($uri->class)){
    ${$uri->class} = new $uri->class;
    $_controller[$uri->class] = &${$uri->class};
}

/* Otherwise load an anonymous controller. */
else ${$uri->class} = new class extends Controller {};

/* Call public controllers from URL. */
if(${$uri->class} instanceof Controller && !${$uri->class} instanceof _Controller){

    /* If the requested method doesn't exist, assume we want to view a web page. */
    if(!method_exists(${$uri->class}, $uri->method)){
        if(method_exists(${$uri->class}->view, 'html')) ${$uri->class}->view->html();
    }

    /* Call the requested method. */
    elseif((new ReflectionMethod(${$uri->class}, $uri->method))->isPublic()){
        ${$uri->class}->{$uri->method}($uri->arguments);
    }
}

/* Fallback; load 404 page. */
http_response_code(404);
(new class extends Controller {})->view->html();
