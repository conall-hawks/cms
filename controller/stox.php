<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**--------------------------------------------------------------------------------------------------------------------\
| Stock market stuff.                                                                                                  |
\---------------------------------------------------------------------------------------------------------------------*/
class Stox extends Controller {

    /**------------------------------------------------------------------------\
    | Construct.                                                               |
    \-------------------------------------------------------------------------*/
    public function __construct(){
        parent::__construct();

        // Query ticker.
        if(!empty($_POST[$this->view->form_token('query')])){
            #$this->model->ticker = $this->model->query($_POST[$this->view->form_token('query')]);
            $this->model->ticker = $this->model->query($_POST[$this->view->form_token('query')], true);
        }

        // Delete ticker.
        if(!empty($_POST[$this->view->form_token('delete')])){
            $this->model->delete($_POST[$this->view->form_token('delete')]);
        }
    }
}
