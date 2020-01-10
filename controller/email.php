<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| Email controller.                                                            |
\-----------------------------------------------------------------------------*/
class Email extends Controller {
    public function __construct(){
        parent::__construct();

        //
        $mailbox = imap_open('{127.0.0.1:143/novalidate-cert}', 'root', 'toor');
        print_r($mailbox);
        $list = imap_list($mailbox, '{127.0.0.1:143/novalidate-cert}', '*');
        $folders = imap_listmailbox($mailbox, '{127.0.0.1:143/novalidate-cert}', '*');
        print_r($list);
        print_r($folders);
        print_r(imap_headers($mailbox));

        $info = imap_mailboxmsginfo($mailbox);
        #imap_mail($to,$subject,$message,$additional_headers = NULL [, string $cc = NULL [, string $bcc = NULL [, string $rpath = NULL ]]]] ) : bool

        print_r($info);
        imap_close($mailbox);
    }
}
