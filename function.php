<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**--------------------------------------------------------------------------------------------------------------------\
| Global functions.                                                                                                    |
\---------------------------------------------------------------------------------------------------------------------*/

/**----------------------------------------------------------------------------\
| Import or instantiate controller.                                            |
\-----------------------------------------------------------------------------*/
function load_controller($controller){

    /* Controller already exists. */
    global ${$controller};
    if(!empty(${$controller})) return ${$controller};

    /* Import and instantiate new controller. */
    $class = mb_ucfirst($controller);
    $file  = CONTROLLER.'/'.mb_strtolower($controller).'.php';
    if(!class_exists($class) && file_exists($file)) require($file);
    if(class_exists($class)) ${$controller} = new $class;
    return ${$controller};
}

/**----------------------------------------------------------------------------\
| Active links.                                                                |
\-----------------------------------------------------------------------------*/
function link_active($href){

    /* Get link href. */
    $href = rawurldecode($href);

    /* Get current path. */
    global $uri;
    $path = rawurldecode($uri->path);

    /* Check if link is active. */
    if($path === $href) return 'active';
    if(strpos($path, $href.'/') === 0) return 'active parent';

    /* Get current route. */
    $path = rawurldecode($uri->route);

    /* Check if link is active. */
    if($path === $href) return 'active';
    if(strpos($path, $href.'/') === 0) return 'active parent';
}

/**----------------------------------------------------------------------------\
| Parent paths resolution.                                                     |
\-----------------------------------------------------------------------------*/
function get_parent_paths($path){
    $path  = array_filter(explode('/', $path));
    $count = count($path);
    $paths = [];
    for($i = 1; $i <= $count; $i++){
        $paths[] = implode('/', array_slice($path, 0, $i));
    }
    return $paths;
}

/**----------------------------------------------------------------------------\
| Subtitle HTML construction; creates links to each parent path.               |
\-----------------------------------------------------------------------------*/
function html_subtitle(){

    /* Break apart URL path. */
    global $uri;
    $paths = array_values(array_filter(explode('/', $uri->path), function($value){return $value !== '';}));

    /* Default empty or slash to index. */
    if(in_array($paths, ['', '/']) || !$paths) $paths = ['index'];

    /* Build subtitle. */
    $count  = count($paths);
    $href   = '';
    $output = '';
    for($i = 0; $i < $count; ++$i){
        $paths[$i] = rawurldecode($paths[$i]);
        $path  = htmlspecialchars($paths[$i]);
        $href .= '/'.rawurlencode($paths[$i]);
        if($i === $count - 1){
            $output .= '/'.$path;
        }else{
            $output .= '/<a class="ajax" href="'.$href.'">'.$path.'</a>';
        }
    }

    return $output;
}

/**----------------------------------------------------------------------------\
| Determines if a directory is empty.                                          |
\-----------------------------------------------------------------------------*/
function is_empty_dir($path){
    $handle = opendir($path);
    while(($inode = readdir($handle)) !== false){
        if($inode !== '.' && $inode !== '..'){
            return false;
        }
    }
    return true;
}

/**----------------------------------------------------------------------------\
| Linked generalized functions.                                                |
\-----------------------------------------------------------------------------*/

/* Shorten long byte values. */
require(ASSET.'/share/code/php/format-bytes.php');

/* Recursive glob(). */
require(ASSET.'/share/code/php/glob-recursive.php');

/* Case-insensitive glob(). */
require(ASSET.'/share/code/php/glob-insensitive.php');

/* Get response header by key. */
require(ASSET.'/share/code/php/header-get.php');

/* Convert HTTP status code into it's message. */
require(ASSET.'/share/code/php/http-status-message.php');

/* File MIME type resolution. */
require(ASSET.'/share/code/php/mime.php');

/* MIME type common extension resolution. */
require(ASSET.'/share/code/php/mime-to-extension.php');

/* Uppercase first letter (multibyte-compatible). */
require(ASSET.'/share/code/php/multibyte-ucfirst.php');

/* Title case conversion. */
require(ASSET.'/share/code/php/title-case.php');

/* Prefix trimming. */
require(ASSET.'/share/code/php/trim-prefix.php');

/* Suffix trimming. */
require(ASSET.'/share/code/php/trim-suffix.php');

/* Delete a file or directories recursively. */
require(ASSET.'/share/code/php/unlink-recursive.php');

/**----------------------------------------------------------------------------\
| Standard logging facility.                                                   |
\-----------------------------------------------------------------------------*/
function logger($message){

    /* Begin a session if one has not already started. */
    if(session_status() !== PHP_SESSION_ACTIVE) session_start();

    /* Create session log it it doesn't exist. */
    if(!isset($_SESSION['log'])) $_SESSION['log'] = '';

    /* Append session log. */
    $time = new DateTime;
    $_SESSION['log'] .= '['.$time->format('H:i:s:u').'] '.str_replace("\0", '', print_r($message, true)).PHP_EOL;

    /* Truncate log. */
    while(mb_strlen($_SESSION['log']) > 4096){
        $_SESSION['log'] = mb_substr($_SESSION['log'], strpos($_SESSION['log'], PHP_EOL) + 1);
    }
}

/**----------------------------------------------------------------------------\
| Verify CAPTCHA.                                                              |
\-----------------------------------------------------------------------------*/
function captcha_verify($post_captcha = NULL, $session_captcha = NULL){
    $post_captcha = $post_captcha ?? $_POST['captcha'];
    if(empty($post_captcha)){
        logger('Missing CAPTCHA in POST.');
        return false;
    }
    $session_captcha = $session_captcha ?? $_SESSION['captcha'];
    if(empty($session_captcha)){
        logger('Missing CAPTCHA session variable.');
        return false;
    }
    if($post_captcha !== $session_captcha){
        logger('Wrong CAPTCHA.');
        if(ENVIRONMENT === 'development'){
            logger('Wrong CAPTCHA. Expected: "'.$session_captcha.'". Got: "'.$post_captcha.'".');
        }
        return false;
    }
    return true;
}

/**----------------------------------------------------------------------------\
| Verify CAPTCHA.                                                              |
\-----------------------------------------------------------------------------*/
function adjustBrightness($hex, $steps){

    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0,min(255,$color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}
