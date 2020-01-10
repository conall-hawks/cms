<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| Test extended extended controller.                                           |
\-----------------------------------------------------------------------------*/
class Test_email extends Email {
    public function __construct(){
        echo "test";
    }
}
