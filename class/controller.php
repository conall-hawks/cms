<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| Controller class.                                                            |
\-----------------------------------------------------------------------------*/
abstract class Controller {

    /** @var object: Handle to view. */
    public $view = NULL;

    /** @var object: Handle to model. */
    public $model = NULL;

    /** @var object: Alias for $model. */
    public $db = NULL;

    /**------------------------------------------------------------------------\
    | Startup; resolve defaults then instantiate model and view.               |
    \-------------------------------------------------------------------------*/
    public function __construct($autoload_view = true, $autoload_model = true){

        /* Feedback. */
        logger('Loading controller: '.get_class($this));

        if($autoload_model || $autoload_view){

            /* Default import path. */
            if((new ReflectionClass($this))->isAnonymous()){
                global $uri;
                $path = $uri->route;
            }else{
                $path = mb_strtolower(get_class($this));
            }

            /* Resolve each parent path and store in $paths. */
            $paths = get_parent_paths($path);

            /* Default context-specific model and view. */
            $classes = [];
            if($autoload_model) $classes[] = MODEL;
            if($autoload_model) $classes[] = VIEW;
            foreach($classes as $class){

                /* Skip already-defined class. */
                if($this->$class) continue;

                /* Locate class file most-to-least specificity. */
                for($i = count($paths) - 1; $i >= 0; $i--){
                    $path = $class.'/'.$paths[$i].'.php';
                    if(is_file($path)){

                        /* Import and instantiate class. */
                        require_once($path);
                        $subclass = mb_ucfirst(pathinfo($path, PATHINFO_FILENAME)).'_'.$class;
                        if(class_exists($subclass)){
                            if($class === 'view' && $this->model){
                                $this->$class = new $subclass($this->model);
                            }else{
                                $this->$class = new $subclass;
                            }
                        }
                        break;
                    }
                }
            }

            /* Fallback; default anonymous model and view. */
            if(!$this->model && $autoload_model) $this->model = new class extends Model {};
            if(!$this->view  && $autoload_view){
                if(!$this instanceof _Controller) $this->view = new class extends View {};
                else $this->view = new class($this->model ?? NULL) extends View {public function __construct(){}};
            }
        }
    }

    /**------------------------------------------------------------------------\
    | Import and instantiate model.                                            |
    \-------------------------------------------------------------------------*/
    public function model($file = NULL){

        /* Resolve model class name and file. */
        $class = get_class($this);
        if(!$file) $file = MODEL.'/'.trim(strtolower($class), '_').'.php';
        $class .= '_model';

        /* Import. */
        if(!class_exists($class) && file_exists($file)) require_once($file);
        if(!class_exists($class)) return false;

        /* Default class model. */
        if(!$this->model) $this->model = new $class;
        if(!$this->db !== $this->model) $this->db = &$this->model;
        return $this->model;
    }

    /* Alias for model(). */
    public function db($file = NULL){
        return $this->model($file);
    }
}

/**----------------------------------------------------------------------------\
| Private controller class (cannot be accessed by a URL).                      |
\-----------------------------------------------------------------------------*/
abstract class _Controller extends Controller {}
