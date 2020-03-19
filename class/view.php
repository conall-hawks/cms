<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| View class.                                                                  |
\-----------------------------------------------------------------------------*/
abstract class View {

    /** @var string[]: Metadata. */
    public $meta = [
        'application-name' => TITLE,
        'author'           => AUTHOR,
        'charset'          => 'utf-8',
        'copyright'        => TITLE,
        'description'      => '',
        'favicon'          => '/'.ASSET.'/favicon.png',
        'manifest'         => '/'.ASSET.'/manifest.webmanifest',
        'robots'           => 'follow, index, noarchive',
        'title'            => TITLE,
        'og:description'   => '',
        'og:image'         => '',
        'og:site_name'     => TITLE,
        'og:title'         => TITLE,
        'og:url'           => '',
        'twitter:card'     => '',
        'twitter:site'     => TITLE
    ];

    /** @var string[]: Paths to HTML templates. */
    public $html = [
        'layout'        => TEMPLATE.'/layout.php',
        'head'          => TEMPLATE.'/head.php',
        'style'         => TEMPLATE.'/style.php',
        'header'        => TEMPLATE.'/header.php',
        'aside-left'    => TEMPLATE.'/aside-left.php',
        'aside-left-2'  => TEMPLATE.'/aside-left-2.php',
        'main'          => TEMPLATE.'/main.php',
        'content'       => '',
        'explorer'      => TEMPLATE.'/explorer.php',
        'cms'           => TEMPLATE.'/cms.php',
        'aside-right'   => TEMPLATE.'/aside-right.php',
        'aside-right-2' => TEMPLATE.'/aside-right.php',
        'footer'        => TEMPLATE.'/footer.php',
        'misc'          => TEMPLATE.'/misc.php',
        'navbar'        => TEMPLATE.'/navbar.php',
        'script'        => TEMPLATE.'/script.php'
    ];

    /** @var string[]: Paths to CSS.*/
    protected $css = [
        'head' => [
            'variables'    => ASSET.'/share/code/css/variables.css',
            'list-headers' => ASSET.'/share/code/css/list-headers.css',
            'aside'        => CSS.'/aside.css',
            'login'        => CSS.'/login.css'
        ]
    ];

    /** @var string[]: Paths to JavaScript. */
    protected $js = [
        'head' => [
            'format-bytes'  => ASSET.'/share/code/js/format-bytes.js',
            'format-date'   => ASSET.'/share/code/js/format-date.js',
            'basename'      => ASSET.'/share/code/js/basename.js',
            'trim'          => ASSET.'/share/code/js/trim.js',
            'css-variables' => ASSET.'/share/code/js/polyfill-css-variables.js',
            'user-select'   => ASSET.'/share/code/js/polyfill-user-select.js',
            'clock'         => ASSET.'/share/code/js/clock.js',
            'ajax'          => ASSET.'/share/code/js/ajax.js',
            'ajaxify-form'  => ASSET.'/share/code/js/ajaxify-form.js'
        ]
    ];

    /** @var boolean: HTML viewer will print the file explorer. */
    public $use_explorer = false;

    /** @var string: File explorer default path. */
    public $explorer_path = '';

    /** @var boolean: HTML viewer will print the content management system.*/
    public $use_cms = false;

    /**------------------------------------------------------------------------\
    | Startup; best-effort resolve default values.                             |
    \-------------------------------------------------------------------------*/
    public function __construct($model = NULL){

        // Get model.
        if($model) $this->model = $model;

        // Get all of the template names.
        $templates = array_keys($this->html);

        // Default templates' CSS and JS.
        $count_templates = count($templates);
        for($i = 0; $i < $count_templates; $i++){
            $css = CSS.'/'.$templates[$i].'.css';
            $js  =  JS.'/'.$templates[$i].'.js';
            if(!isset($this->css[$templates[$i]]) || !is_array($this->css[$templates[$i]])) $this->css[$templates[$i]] = [];
            if(!isset($this->js[$templates[$i]])  || !is_array($this->js[$templates[$i]]))  $this->js[$templates[$i]]  = [];
            if(!in_array($css, $this->css[$templates[$i]])) $this->css[$templates[$i]][] = $css;
            if(!in_array($js,  $this->js[$templates[$i]]))  $this->js[$templates[$i]][]  = $js;
        }

        // Resolve each parent path and store in array $paths.
        global $uri;
        $paths = get_parent_paths($uri->route);

        // Default context-specific HTML, CSS, and JS.
        $count_paths = count($paths);
        for($i = 0; $i < $count_paths; $i++){
            for($j = 0; $j < $count_templates; $j++){

                // Default HTML.
                $path = TEMPLATE.'/'.$templates[$j].'/'.$paths[$i].'.php';
                if(is_file($path)) $this->html[$templates[$j]] = $path;

                // Default CSS and parent CSS.
                $path = CSS.'/'.$templates[$j].'/'.$paths[$i].'.css';
                if(is_file($path) && !in_array($path, $this->css[$templates[$j]])){
                    $this->css[$templates[$j]][] = $path;
                }

                // Default JS and parent JS.
                $path = JS.'/'.$templates[$j].'/'.$paths[$i].'.js';
                if(is_file($path) && !in_array($path, $this->js[$templates[$j]])){
                    $this->js[$templates[$j]][] = $path;
                }
            }
        }

        // Begin a session if one has not already started.
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();

        // Special processing for left aside template; don't load if using AJAX and same controller.
        if($uri->is_xhr && isset($_SESSION['controller']) && $_SESSION['controller'] === $uri->class && mb_strtolower($_SERVER['REQUEST_METHOD']) === 'get'){
            $this->html['aside-left'] = '';
        }
        $_SESSION['controller'] = $uri->class;

        // Default content HTML to $uri->route.
        $content = TEMPLATE.'/content'.$uri->route.'.php';
        $this->html['content'] = file_exists($content) ? $content : '';

        // Default explorer.
        if(is_dir(ASSET.'/share/'.$uri->class)) $this->use_explorer = true;

        // Default explorer path.
        if(!$this->explorer_path) $this->explorer_path = ASSET.'/share'.$uri->path;

        // Default copyright metadata.
        $this->meta['copyright'] = TITLE.' '.html_entity_decode('&copy;').date('Y');

        // Default description metadata.
        $this->meta['description'] = mb_ucfirst(implode(' ', array_filter(explode('/', TITLE.$uri->path)))).'.';

        // Default keyword metadata.
        $this->meta['keywords'] = mb_strtolower(str_replace(['/', '-'], [', ', ' '], trim(TITLE.$uri->path, '/')));

        // Default title metadata.
        $this->meta['title'] = rawurldecode(trim(implode(' - ', title(array_reverse(explode('/', ltrim($uri->path, '/')))))));
        if(!$this->meta['title']) $this->meta['title'] = title($uri->class);
        $this->meta['title'] .= ' | '.TITLE;

        // Import CMS class if needed.
        #if($this->use_cms){
        #    require 'class/cms.php';
        #    global $cms;
        #    $cms = new Cms;
        #}
    }

    /**------------------------------------------------------------------------\
    | Generate a random form token.                                            |
    | Used to generate randomized field names (<input name="@return" />).      |
    +--------------------------------------------------------------------------|
    | Example usage:                                                           |
    |                                                                          |
    |     <input name="<?php echo $this->form_token('username'); ?>" />        |
    |     <input name="<?php echo $this->form_token('password'); ?>" />        |
    |                                                                          |
    +---------+--------+--------+----------------------------------------------+
    | @param  | string | $field | Field name.                                  |
    | @param  | string | $field | Field name.                                  |
    | @param  | string | $form  | Form/view class name.                        |
    | @return | string |        | "Form token", essentially a nonce.           |
    \---------+--------+--------+---------------------------------------------*/
    public function form_token($field, $form = NULL){

        if(!$form) $form = get_class($this);

        // Start a session if one has not already started.
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();

        // Store tokens in session.
        $class = trim_suffix(mb_strtolower($form), '_view');
        if(empty($_SESSION['form_'.$class])) $_SESSION['form_'.$class] = [];

        // Generate a randomized form token.
        if(!(isset(    $_SESSION['form_'.$class][$field])
        &&   is_string($_SESSION['form_'.$class][$field])
        &&   strlen(   $_SESSION['form_'.$class][$field]) === 32)){
            do{
                $random = bin2hex(random_bytes(16));
            }while(in_array($random, $_SESSION['form_'.$class]));
            $_SESSION['form_'.$class][$field] = $random;
        }

        // Done.
        return $_SESSION['form_'.$class][$field];
    }

    /**------------------------------------------------------------------------\
    | Render HTML page.                                                        |
    +---------+----------+-------+---------------------------------------------+
    | @param  | string[] | $html | Any arbitrary URL.                          |
    \---------+----------+-------+--------------------------------------------*/
    public function html($layout = ''){

        // Default layout.
        if(!$layout) $layout = $this->html['layout'];

        // 404 on missing content.
        if(!$this->html['content'] && !$this->use_cms && !$this->use_explorer){
            http_response_code(404);
        }

        // 403 on disallowed robots.
        if(preg_match('/nofollow|noindex/i', $this->meta['robots']) && (empty($_SERVER['HTTP_USER_AGENT']) || preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT']))){
            while(ob_get_level()) ob_end_clean();
            http_response_code(403);
            die();
        }

        // Error page handling.
        switch(http_response_code()){

            // OK.
            case 200:
                break;

            // Undesirable.
            default:

                // Preserve some elements.
                $html = $this->html;
                $html['content'] = TEMPLATE.'/content/'.http_response_code().'.php';
                if(!is_file($html['content'])) $html['content'] = TEMPLATE.'/content/404.php';

                // Reset page content.
                global $uri;
                $uri->class           = http_response_code();
                $uri->route           = '/'.http_response_code();
                $this->meta['robots'] = 'none';
                #$this->use_cms        = false;
                $this->use_explorer   = false;
                $this->__construct();

                // Restore preserved elements.
                $this->html = $html;

                break;
        }

        // Import frequently used dependencies.
        global $security, $uri;
        $user = load_controller('user');
        ${$uri->class} = load_controller($uri->class);

        // Make the model and controller available to templates.
        $this->controller = load_controller($uri->class);
        $this->model      = $this->controller->model ?? NULL;

        // View HTML using layout.
        logger('Rendering HTML for: '.$uri->path);
        $_pre = '';
        while(ob_get_level()) $_pre .= ob_get_clean();
        ob_start();
        require($layout);

        // Don't do anything else.
        logger('--------------------------------------------------------------------------------');
        die();
    }


    /**------------------------------------------------------------------------\
    | Build HTML string for stylesheets. Supply a path or an array of paths.   |
    +---------+----------+------+----------------------------------------------+
    | @param  | string   | $css | Path to stylesheet file.                     |
    | @param  | string[] | $css | Paths to stylesheet files.                   |
    | @return | string   |      | HTML to include stylesheets.                 |
    \---------+----------+------+---------------------------------------------*/
    static public function include_css($css, $embed = false){

        // Array parameter: recurse.
        if(is_array($css)) foreach($css as $a) self::include_css($a, $embed);

        // String parameter; include.
        elseif(is_string($css) && is_file($css)){
            $css = preg_replace('/\/+/','/', rtrim($css, '/'));
            $id  = pathinfo($css);
            $id  = str_replace('/', '-', $id['dirname'].'/'.$id['filename']);
            if(empty($security)) global $security;

            // Embedded/Inline.
            if($embed){
                echo '<style id="'.$id.'" nonce="'.$security->nonce().'">'.file_get_contents($css).'</style>'.PHP_EOL;
            }

            // External.
            else{
                echo '<link id="'.$id.'" rel="stylesheet" href="/'.$css.'" nonce="'.$security->nonce().'" />'.PHP_EOL;
            }
        }
    }

    /**------------------------------------------------------------------------\
    | Build HTML string for scripts. Supply a path or an array of paths.       |
    +---------+----------+-----+-----------------------------------------------+
    | @param  | string   | $js | Path to script file.                          |
    | @param  | string[] | $js | Paths to script files.                        |
    | @return | string   |     | HTML to include scripts.                      |
    \---------+----------+-----+----------------------------------------------*/
    static public function include_js($js, $embed = false){

        // Array parameter: recurse.
        if(is_array($js)) foreach($js as $a) self::include_js($a, $embed);

        // String parameter; include.
        elseif(is_string($js) && is_file($js)){
            $js = preg_replace('/\/+/','/', rtrim($js, '/'));
            $id = pathinfo($js);
            $id = str_replace('/', '-', $id['dirname'].'/'.$id['filename']);
            if(empty($security)) global $security;

            // Embedded/Inline.
            if($embed){
                echo '<script id="'.$id.'" nonce="'.$security->nonce().'">'.file_get_contents($js).'</script>'.PHP_EOL;
            }

            // External.
            else{
                echo '<script async id="'.$id.'" src="/'.$js.'" nonce="'.$security->nonce().'"></script>'.PHP_EOL;
            }
        }
    }

    /**------------------------------------------------------------------------\
    | Include the specified template.                                          |
    +---------+---------+----------------+-------------------------------------+
    | @param  | string  | $template      | Name of template.                   |
    | @param  | boolean | $return_string | Return output as string.            |
    | @return | string  |                | Output.                             |
    \---------+---------+----------------+------------------------------------*/
    public function template($template, $return_string = false){
        if(!empty($this->html[$template])){

            // Import frequently used dependencies.
            global $security, $uri;
            $user = load_controller('user');
            ${$uri->class} = load_controller($uri->class);

            // Include template.
            if($return_string) ob_start();
            include($this->html[$template]);
            if($return_string) return ob_get_clean();
        }
    }

    /**------------------------------------------------------------------------\
    | Open a file.                                                             |
    +---------+--------+--------+----------------------------------------------+
    | @param  | string | $file  | Path to file.                                |
    | @param  | string | $name  | Custom filename.                             |
    \---------+--------+--------+---------------------------------------------*/
    static public function file($file, $name = ''){

        // Validate file existence.
        if(!is_file($file)) die('Not a file.');

        // Build "Content-Type" header.
        $mime = mime($file);

        // Allow HTML to be viewed in the browser.
        if(in_array($mime, ['text/html'])) $mime = 'text/plain';

        // Append a character set to textual files' "Content-Type" header.
        if(substr($mime, 0, 5) === 'text/' || $mime === 'application/javascript'){
            $mime .= '; charset=utf-8';
        }

        // Set "Content-Type" and "Content-Length" headers.
        header('Content-Type: '.$mime);
        header('Content-Length: '.filesize($file));

        // Ensure "Content-Disposition" header exists.
        #if(!header_get('Content-Disposition')){
        #    header('Content-Disposition: attachment'.($name ? '; filename="'.$name.'"' : ''));
        #    #header('Content-Disposition: attachment');
        #}

        // Output file.
        if(ENVIRONMENT !== 'development') while(ob_get_level()) ob_end_clean();
        die(file_get_contents($file));
    }
}
