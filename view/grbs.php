<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| Grand Rapids Bridal Show management.                                         |
\-----------------------------------------------------------------------------*/
class Grbs_view extends View {

    /**------------------------------------------------------------------------\
    | Override construct.                                                      |
    \-------------------------------------------------------------------------*/
    public function __construct(){

        /* Load defaults. */
        parent::__construct();

        /* Override default title and favicon metadata. */
        $this->meta['title']   = 'GRBS';
        $this->meta['favicon'] = '';
    }
}
