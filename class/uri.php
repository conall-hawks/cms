<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| Routing and request resolution.                                              |
\-----------------------------------------------------------------------------*/
class URI {

    /** @var string: URI path pre-routed. */
    public $path = '';

    /** @var string: URI path post-routed. */
    public $route = '';

    /** @var string: Class name. */
    public $class = '';

    /** @var string: Method name. */
    public $method = '';

    /** @var string: Arguments passed to method. */
    public $arguments = '';

    /** @var boolean: Request is an XMLHTTPRequest. */
    public $is_xhr = false;

    /**------------------------------------------------------------------------\
    | Startup.                                                                 |
    \-------------------------------------------------------------------------*/
    public function __construct($uri = NULL){

        // Set XMLHTTPRequest (AJAX) flag.
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && mb_strtolower(trim($_SERVER['HTTP_X_REQUESTED_WITH'])) === 'xmlhttprequest'){
            $this->is_xhr = true;
        }

        // Map the URI to a route.
        $this->set_route($uri ?? $_SERVER['REQUEST_URI']);
    }

    /**------------------------------------------------------------------------\
    | Determine what should be served based on the URI request and any         |
    | routes that have been set in the routing configuration file.             |
    +---------+-----------+-----------+----------------------------------------+
    | @param  | string    | $url      | Any arbitrary URL.                     |
    \---------+-----------+-----------+---------------------------------------*/
    public function set_route($url = NULL){

        // Remove redundant slashes.
        $url = '/'.implode('/', array_filter(explode('/', $url)));

        // Extract the path.
        $url = utf8_decode(parse_url(utf8_encode($url))['path']);

        // Save for convenient access.
        $this->path = $url;

        // Load the routes; inject filled $route variable.
        $route = [];
        require('route.php');

        // Search for a literal match.
        if(!empty($route[$url])) $url = $route[$url];

        // Search for a wildcard match.
        foreach($route as $key => $value){

            // Convert wildcards to respective RegEx.
            $regex = str_replace([':all', ':any', ':num'], ['.*', '[^/]+', '[0-9]+'], $key);

            // Search for a match.
            if(preg_match('#^'.$regex.'$#', $url)){
                $url = preg_replace('#^'.$regex.'$#', $value, $url);
                break;
            }
        }

        // Save for convenient access.
        $this->route = $url;

        // Explode into an array.
        $url = explode('/', ltrim($url, '/'));

        // Set class.
        if(isset($url[0])) $this->class = str_replace('-', '_', $url[0]);

        // Set method.
        if(isset($url[1])) $this->method = ltrim(str_replace('-', '_', $url[1]), '_');

        // Set argument(s). If more than one argument, pass them as an array.
        if(isset($url[3])) $url[2] = array_slice($url, 2);
        if(isset($url[2])) $this->arguments = $url[2];
    }
}
