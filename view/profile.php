<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| Profile page management.                                                     |
\-----------------------------------------------------------------------------*/
class Profile_view extends View {

    /**------------------------------------------------------------------------\
    | Override construct.                                                      |
    \-------------------------------------------------------------------------*/
    public function __construct(){

        /* Load defaults. */
        parent::__construct();

        /* Override default robots metadata. */
        $this->meta['robots'] = 'noarchive, nocache, nofollow, noimageindex, noindex, nosnippet';
    }
}
