<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**--------------------------------------------------------------------------------------------------------------------\
| Security class.                                                                                                      |
\---------------------------------------------------------------------------------------------------------------------*/
class Security {

    /** @var string: Nonce for CSP (Content Security Policy). */
    private $nonce = '';

    /**------------------------------------------------------------------------\
    | Startup; set default security configuration.                             |
    \-------------------------------------------------------------------------*/
    public function __construct(){

        // Error reporting.
        if(ENVIRONMENT !== 'development'){
            ini_set('display_errors'        , 0);
            ini_set('display_startup_errors', 0);
            ini_set('error_reporting'       , 0);
            error_reporting(0);
        }else{
            ini_set('display_errors'        , 1);
            ini_set('display_startup_errors', 1);
            ini_set('error_reporting'       , E_ALL);
            error_reporting(E_ALL);
        }

        // Unset file permissions mask.
        umask(0);

        // Remove all HTTP headers.
        header_remove();

        // Start a session if one has not already started.
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();

        // Configure HTTP headers.
        header('Cache-Control: must-revalidate, no-cache, no-store, private');
        header('Expect-CT: enforce, max-age=31557600, report-uri="https://'.$_SERVER['SERVER_NAME'].'/ct"');
#        header(trim(preg_replace('/\s+/', ' ', "
#            Content-Security-Policy:
#                connect-src 'self'
#                            https://cdn.jsdelivr.net
#                            https://cdnjs.cloudflare.com
#                            https://fonts.googleapis.com;
#
#                default-src 'nonce-".$this->nonce()."'
#                            'self'
#                            data:;
#
#                font-src    'self'
#                            https://cdn.jsdelivr.net
#                            https://fonts.gstatic.com;
#
#                frame-src   'self'
#                            https://googleads.g.doubleclick.net;
#
#                img-src     'self'
#                            https://pagead2.googlesyndication.com
#                            https://storage.googleapis.com
#                            https://www.google-analytics.com
#                            data:;
#
#                style-src   'self'
#                            'unsafe-inline'
#                            https://cdn.jsdelivr.net
#                            https://fonts.googleapis.com;
#
#                script-src  'self'
#                            'unsafe-eval'
#                            'unsafe-inline'
#                            https://adservice.google.ch
#                            https://adservice.google.com
#                            https://adservice.google.nl
#                            https://cdn.jsdelivr.net
#                            https://cdn.polyfill.io
#                            https://cdnjs.cloudflare.com
#                            https://www.googletagservices.com
#                            https://pagead2.googlesyndication.com
#                            https://www.google-analytics.com
#                            data:;
#
#                report-uri  https://".$_SERVER['SERVER_NAME']."/csp;
#        ")));
        $adsense = ['ac', 'ad', 'ae', 'al', 'am', 'as', 'at', 'az', 'ba', 'be', 'bf', 'bg', 'bi', 'bj', 'bs', 'bt', 'by', 'ca', 'cat', 'cc', 'cd', 'cf', 'cg', 'ch', 'ci', 'cl', 'cm', 'co.ao', 'co.bw', 'co.ck', 'co.cr', 'co.id', 'co.il', 'co.in', 'co.jp', 'co.ke', 'co.kr', 'co.ls', 'com', 'co.ma', 'com.af', 'com.ag', 'com.ai', 'com.ar', 'com.au', 'com.bd', 'com.bh', 'com.bn', 'com.bo', 'com.br', 'com.bz', 'com.co', 'com.cu', 'com.cy', 'com.do', 'com.ec', 'com.eg', 'com.fj', 'com.gh', 'com.gi', 'com.gt', 'com.hk', 'com.jm', 'com.kh', 'com.kw', 'com.lb', 'com.ly', 'com.mm', 'com.mt', 'com.mx', 'com.my', 'com.na', 'com.nf', 'com.ng', 'com.ni', 'com.np', 'com.om', 'com.pa', 'com.pe', 'com.pg', 'com.ph', 'com.pk', 'com.pr', 'com.py', 'com.qa', 'com.sa', 'com.sb', 'com.sg', 'com.sl', 'com.sv', 'com.tj', 'com.tr', 'com.tw', 'com.ua', 'com.uy', 'com.vc', 'com.vn', 'co.mz', 'co.nz', 'co.th', 'co.tz', 'co.ug', 'co.uk', 'co.uz', 'co.ve', 'co.vi', 'co.za', 'co.zm', 'co.zw', 'cv', 'cz', 'de', 'dj', 'dk', 'dm', 'dz', 'ee', 'es', 'fi', 'fm', 'fr', 'ga', 'ge', 'gf', 'gg', 'gl', 'gm', 'gp', 'gr', 'gy', 'hn', 'hr', 'ht', 'hu', 'ie', 'im', 'io', 'iq', 'is', 'it', 'je', 'jo', 'kg', 'ki', 'kz', 'la', 'li', 'lk', 'lt', 'lu', 'lv', 'md', 'me', 'mg', 'mk', 'ml', 'mn', 'ms', 'mu', 'mv', 'mw', 'ne', 'nl', 'no', 'nr', 'nu', 'pl', 'pn', 'ps', 'pt', 'ro', 'rs', 'ru', 'rw', 'sc', 'se', 'sh', 'si', 'sk', 'sm', 'sn', 'so', 'sr', 'st', 'td', 'tg', 'tk', 'tl', 'tm', 'tn', 'to', 'tt', 'vg', 'vu', 'ws'];
        foreach($adsense as &$tld) $tld = 'https://adservice.google.'.$tld;
        $adsense = implode(PHP_EOL.' ', $adsense);
        $csp = trim(preg_replace('/\s+/', ' ', "
            Content-Security-Policy:
                base-uri        'self';

                block-all-mixed-content;

                child-src       'none';

                connect-src     'self'
                                https://cdn.jsdelivr.net
                                https://cdnjs.cloudflare.com
                                https://csi.gstatic.com
                                https://fonts.googleapis.com
                                https://pagead2.googlesyndication.com
                                wss://".$_SERVER['SERVER_NAME'].":8080;

                default-src     'nonce-".$this->nonce()."'
                                'self'
                                data:;

                font-src        'self'
                                https://cdn.jsdelivr.net
                                https://fonts.gstatic.com;

                form-action     'self';

                frame-ancestors 'self';

                frame-src       'self'
                                https://googleads.g.doubleclick.net;

                img-src         'self'
                                data:
                                https://pagead2.googlesyndication.com
                                https://storage.googleapis.com
                                https://www.google-analytics.com;

                manifest-src    'none';

                style-src       'self'
                                'unsafe-inline'
                                https://cdn.jsdelivr.net
                                https://fonts.googleapis.com;

                script-src      'self'
                                'unsafe-eval'
                                'unsafe-inline'
                                data:
                                ".$adsense."
                                https://cdn.jsdelivr.net
                                https://cdn.polyfill.io
                                https://cdnjs.cloudflare.com
                                https://pagead2.googlesyndication.com
                                https://www.gitcdn.xyz
                                https://www.google-analytics.com
                                https://www.googletagservices.com;

                report-uri      https://".$_SERVER['SERVER_NAME']."/csp;
        "));
        header($csp);
        header('Pragma: no-cache');
       #header(trim(preg_replace('/\s+/', ' ', "
       #    Public-Key-Pins:
       #        pin-sha256=\"8Cp108a44hM9MEkBkeXe0yntVWNloabQT0GsIu1B3YI=\";
       #        pin-sha256=\"sRHdihwgkaib1P1gxX8HFszlD+7/gTfNvuAybgLPNis=\";
       #        pin-sha256=\"YLh1dUR9y6Kja30RrAn7JKnbQG/uEtLMkBgFF2Fuihg=\";
       #        pin-sha256=\"C5+lpZ7tcVwmwQIMcRtPbsQtWLABXhQzejna0wHFr8M=\";
       #        max-age=31557600;
       #        includeSubDomains;
       #        report-uri=\"https://".$_SERVER['SERVER_NAME']."/hpkp\"
       #")));
       header(trim(preg_replace('/\s+/', ' ', "
           Public-Key-Pins:
               pin-sha256=\"fqH8VKVyborOwnufL77SJnUjHOBFkRn0NauLwvWWc+o=\";
               pin-sha256=\"YLh1dUR9y6Kja30RrAn7JKnbQG/uEtLMkBgFF2Fuihg=\";
               pin-sha256=\"Vjs8r4z+80wjNcr1YKepWQboSIRi63WsWXhIMN+eWys=\";
               pin-sha256=\"8Cp108a44hM9MEkBkeXe0yntVWNloabQT0GsIu1B3YI=\";
               max-age=31557600;
               includeSubDomains;
               report-uri=\"https://".$_SERVER['SERVER_NAME']."/hpkp\"
       ")));
        header('Referrer-Policy: no-referrer');
        #header('Set-Cookie: PHPSESSID='.session_id().'; Expires='.(new DateTime())->modify('+14 day')->format('r').'; Domain='.$_SERVER['SERVER_NAME'].'; HttpOnly; Max-Age=1209600; Path=/; Secure; SameSite=Strict');
        header('Set-Cookie: PHPSESSID='.session_id().'; Expires='.(new DateTime())->modify('+14 day')->format('r').'; Domain='.$_SERVER['SERVER_NAME'].'; HttpOnly; Max-Age=1209600; Path=/; Secure');
        header('Strict-Transport-Security: max-age=31557600; includeSubdomains; preload');
        header(str_replace('Content-Security-Policy', 'X-Content-Security-Policy', $csp));
        #header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: sameorigin');
        header('X-Robots-Tag: none');
        header('X-XSS-Protection: 1; mode=block');

        // Check for banned IP addresses.
        $this->model = new Security_model();
        if($this->model->banned_ip($_SERVER['REMOTE_ADDR'], true)){

            // Respond with ambiguous error message.
            while(ob_get_level()) ob_end_clean();
            http_response_code(403);
            die(ENVIRONMENT === 'development' ? 'Banned IP: '.$_SERVER['REMOTE_ADDR'] : '');
        }
    }

    public function nonce(){
        if(empty($this->nonce)) $this->nonce = bin2hex(random_bytes(16));
        return $this->nonce;
    }


    // Validate and verify CAPTCHA.
    public function validate_captcha($captcha = NULL){
        $captcha = $captcha ?? $_POST['captcha'];
        if(empty($_SESSION['captcha'])){
            logger('Missing CAPTCHA session variable.');
            return false;
        }else if(empty($captcha)){
            logger('Missing CAPTCHA in POST.');
            return false;
        }else if($_SESSION['captcha'] !== $captcha){
            logger('Wrong CAPTCHA.');
            if(ENVIRONMENT === 'development'){
                logger('Expected CAPTCHA: "'.$_SESSION['captcha'].'", but got: "'.$captcha.'".');
            }
            return false;
        }
        return true;
    }
}

/**----------------------------------------------------------------------------\
| Security database.                                                           |
\-----------------------------------------------------------------------------*/
class Security_model extends Model {

    /**------------------------------------------------------------------------\
    | Construct.                                                               |
    \-------------------------------------------------------------------------*/
    public function __construct(){

        // Connect to the database.
        parent::__construct();

        // Use to rebuild and reset the database.
        #$this->reset();
    }

    /**------------------------------------------------------------------------\
    | Check if an IP is banned.                                                |
    \-------------------------------------------------------------------------*/
    public function banned_ip($ip, $filter_martians = true){

        // Martian filtering.
        if($filter_martians === true){
            $martians = [
                ['  0.  0.  0.  0', '  0.255.255.255'],
                [' 10.  0.  0.  0', ' 10.255.255.255'],
                ['100. 64.  0.  0', '100.127.255.255'],
                ['169.254.  0.  0', '169.254.255.255'],
                ['172. 16.  0.  0', '172. 31.255.255'],
                ['192.  0.  0.  0', '192.  0.  0.255'],
                ['192.  0.  2.  0', '192.  0.  2.255'],
                ['192. 88. 99.  0', '192. 88. 99.255'],
                ['192.168.  0.  0', '192.168.255.255'],
                ['198. 18.  0.  0', '198. 19.255.255'],
                ['198. 51.100.  0', '198. 51.100.255'],
                ['203.  0.113.  0', '203.  0.113.255'],
                ['224.  0.  0.  0', '239.255.255.255'],
                ['240.  0.  0.  0', '255.255.255.254'],
                ['255.255.255.255', '255.255.255.255']
            ];
            if(ENVIRONMENT !== 'development'){
                $martians[] = ['127.0.0.0', '127.255.255.255'];
            }
            $ip = preg_replace('/\s+/', '', $ip);
            if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                $ipl = ip2long($ip);
                foreach($martians as $martian){
                    $martian[0] = preg_replace('/\s+/', '', $martian[0]);
                    $martian[1] = preg_replace('/\s+/', '', $martian[1]);
                    if($ipl >= ip2long($martian[0]) && $ipl <= ip2long($martian[1])) return true;
                }
            }
        }

        // Database blacklist filtering.
        try{
            $sql = $this->db->prepare("SELECT * FROM `banned_ip` WHERE `ip` = :ip LIMIT 1;");
            $sql->execute([':ip' => $ip]);
            if($sql->fetchAll()) return true;
        }catch(Exception $e){
            if(strpos($e, 'Base table or view not found')){
                $this->reset();
                $this->banned_ip($ip);
            }
        }

        // Done!
        return false;
    }

    /**--------------------------------------------------------------------\
    | Reset the databases.                                                 |
    \---------------------------------------------------------------------*/
    private function reset(){
        logger('Rebuilding database: banned_ip.');

        // Erase upload table.
        $this->db->exec("DROP TABLE IF EXISTS `banned_ip`;");

        // Create banned_ip table.
        $this->db->exec("
            CREATE TABLE `banned_ip` (
            `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Auto-incrementing ID of each IP, unique index.',
            `ip`          CHAR(45)  COMMENT 'IP address.',
            `description` CHAR(255) COMMENT 'Details regarding this record.'
            ) AUTO_INCREMENT=".random_int(32768, 65536)." CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ROW_FORMAT=COMPRESSED COMMENT 'IP banned list.';
        ");

        // Add constraints.
        #$this->db->exec("SET GLOBAL innodb_file_format    = `BARRACUDA`;");
        #$this->db->exec("SET GLOBAL innodb_large_prefix   = `ON`;");
        $this->db->exec("SET GLOBAL innodb_file_per_table = `ON`;");
        $this->db->exec("ALTER TABLE `banned_ip` ADD UNIQUE (`ip`); ");

        // Create a default entry.
        $this->db->exec("
            INSERT INTO `banned_ip` (
                `id`,
                `ip`,
                `description`
            ) VALUES(
                '1',
                '255.255.255.255',
                'Example; bogus IP.'
            );
        ");

        // Reset AUTO_INCREMENT.
        $this->db->exec("ALTER TABLE `banned_ip` AUTO_INCREMENT=".random_int(32768, 65536).";");

        // Create some default entries of known abusive IPs.
        $ips = [];
        foreach($ips as $ip) $this->db->exec("INSERT INTO `banned_ip` (`ip`,`description`) VALUES('".$ip."', 'Abusive web scrapers.');");
    }
}
