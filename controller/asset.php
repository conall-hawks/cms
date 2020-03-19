<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| Public assets management.                                                    |
\-----------------------------------------------------------------------------*/
class Asset extends Controller {
    public function __construct(){

        // Load asset.
        global $uri;
        if(in_array($uri->class, [ASSET, CSS, JS])){
            $asset = ltrim($uri->route, '/');
            if(is_file($asset)) View::file($asset);
        }

        // Invalid asset; 404.
        parent::__construct();
        http_response_code(404);
    }
}
