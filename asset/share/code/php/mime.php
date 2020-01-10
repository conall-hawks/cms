<?php
/**----------------------------------------------------------------------------\
| Resolves a file's MIME type, first using it's file extension. Do not use for |
| security purposes.                                                           |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     echo mime('example.txt');                                                |
|                                                                              |
| Result:                                                                      |
|     text/plain                                                               |
+---------+--------+-------+---------------------------------------------------+
| @param  | string | $file | The path to a file.                               |
|         |        |       |                                                   |
| @return | string |       | The file's MIME type.                             |
\---------+--------+-------+--------------------------------------------------*/
function mime($file){
    static $mimes = [
        'ai'    => 'application/postscript',
        'asm'   => 'text/x-asm',
        'bat'   => 'text/x-msdos-batch',
        'bmp'   => 'image/bmp',
        'c'     => 'text/x-c',
        'cab'   => 'application/vnd.ms-cab-compressed',
        'cer'   => 'application/pkix-cert',
        'cpp'   => 'text/x-c++',
        'crl'   => 'application/pkix-crl',
        'crl'   => 'application/x-pkcs7-crl',
        'crt'   => 'application/x-x509-ca-cert',
        'crt'   => 'application/x-x509-user-cert',
        'csr'   => 'application/pkcs10',
        'css'   => 'text/css',
        'der'   => 'application/x-x509-ca-cert',
        'doc'   => 'application/msword',
        'docm'  => 'application/vnd.ms-word.document.macroEnabled.12',
        'docx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dot'   => 'application/msword',
        'dotm'  => 'application/vnd.ms-word.template.macroEnabled.12',
        'dotx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'eot'   => 'application/vnd.ms-fontobject',
        'eps'   => 'application/postscript',
        'exe'   => 'application/x-msdownload',
        'flv'   => 'video/x-flv',
        'gif'   => 'image/gif',
        'htm'   => 'text/html',
        'html'  => 'text/html',
        'ico'   => 'image/vnd.microsoft.icon',
        'jpe'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'jpg'   => 'image/jpeg',
        'js'    => 'application/javascript',
        'json'  => 'application/json',
        'key'   => 'application/pkcs8',
        'mov'   => 'video/quicktime',
        'mp3'   => 'audio/mpeg',
        'msi'   => 'application/x-msdownload',
        'ods'   => 'application/vnd.oasis.opendocument.spreadsheet',
        'odt'   => 'application/vnd.oasis.opendocument.text',
        'otf'   => 'application/x-font-opentype',
        'p10'   => 'application/pkcs10',
        'p12'   => 'application/x-pkcs12',
        'p7b'   => 'application/x-pkcs7-certificates',
        'p7c'   => 'application/pkcs7-mime',
        'p7r'   => 'application/x-pkcs7-certreqresp',
        'p8'    => 'application/pkcs8',
        'pdf'   => 'application/pdf',
        'pem'   => 'application/x-pem-file',
        'pfx'   => 'application/x-pkcs12',
        'php'   => 'text/x-php',
        'png'   => 'image/png',
        'pot'   => 'application/vnd.ms-powerpoint',
        'potm'  => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
        'potx'  => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'ppa'   => 'application/vnd.ms-powerpoint',
        'ppam'  => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'pps'   => 'application/vnd.ms-powerpoint',
        'ppsm'  => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        'ppsx'  => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'ppt'   => 'application/vnd.ms-powerpoint',
        'pptm'  => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'pptx'  => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'ps'    => 'application/postscript',
        'psd'   => 'image/vnd.adobe.photoshop',
        'qt'    => 'video/quicktime',
        'rar'   => 'application/x-rar-compressed',
        'rtf'   => 'application/rtf',
        'sfnt'  => 'application/font-sfnt',
        'sh'    => 'text/x-shellscript',
        'spc'   => 'application/x-pkcs7-certificates',
        'sql'   => 'application/sql',
        'svg'   => 'image/svg+xml',
        'svgz'  => 'image/svg+xml',
        'swf'   => 'application/x-shockwave-flash',
        'tif'   => 'image/tiff',
        'tiff'  => 'image/tiff',
        'ttf'   => 'application/x-font-truetype',
        'ttf'   => 'application/x-font-ttf',
        'txt'   => 'text/plain',
        'url'   => 'application/internet-shortcut',
        'woff'  => 'application/font-woff',
        'woff2' => 'application/font-woff2',
        'xla'   => 'application/vnd.ms-excel',
        'xlam'  => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'xls'   => 'application/vnd.ms-excel',
        'xlsb'  => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'xlsm'  => 'application/vnd.ms-excel.sheet.macroEnabled.12',
        'xlsx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xlt'   => 'application/vnd.ms-excel',
        'xltm'  => 'application/vnd.ms-excel.template.macroEnabled.12',
        'xltx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xml'   => 'application/xml',
        'zip'   => 'application/zip',
    ];

    $ext = trim(mb_strtolower(pathinfo($file, PATHINFO_EXTENSION)));
    if(!empty($mimes[$ext])){
        return $mimes[$ext];
    }elseif(file_exists($file)){
        $finfo = finfo_open(FILEINFO_MIME);
        $mime  = finfo_file($finfo, $file);
        finfo_close($finfo);
        return explode(';', $mime)[0];
    }else{
        return 'application/octet-stream';
    }
}
