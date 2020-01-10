<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| Imageboard view controller.                                                  |
\-----------------------------------------------------------------------------*/
class Pix_view extends View {

    /**------------------------------------------------------------------------\
    | Override construct.                                                      |
    \-------------------------------------------------------------------------*/
    public function __construct($model = NULL){

        /* Load defaults. */
        parent::__construct($model);

        /* Override default title metadata. */
        global $uri;
        $title = array_filter(explode('/', ltrim($uri->path, '/')));
        $title = '/'.mb_strtolower(rawurldecode(trim(array_pop($title)))).'/';
        $board = $this->model->get_board()['title'] ?? false;
        if($board) $title .= ' - '.$board;
        $this->meta['title'] = $title;
        if(!$this->meta['title']) $this->meta['title'] = title($uri->class);
        $this->meta['title'] .= ' | '.TITLE;
    }
}
