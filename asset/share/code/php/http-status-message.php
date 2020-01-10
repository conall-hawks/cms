<?php
/**----------------------------------------------------------------------------\
| Convert HTTP status code into it's message.                                  |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     echo http_status_message(403);                                           |
|                                                                              |
| Result:                                                                      |
|     Forbidden                                                                |
+---------+---------+-------+--------------------------------------------------+
| @param  | integer | $code | HTTP status code.                                |
|         |         |       |                                                  |
| @return | string  |       | HTTP status code's message.                      |
\---------+---------+-------+-------------------------------------------------*/
function http_status_message($code = NULL){

    // Default HTTP code parameter.
    if(!$code) $code = http_response_code();

    // Map HTTP codes to their respective messages.
    static $http_status_message = [

        // 1xx Informational responses.
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        102 => 'Early Hints',

        // 2xx Success.
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',

        // 3xx Redirection.
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        // Client errors.
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large ',
        451 => 'Unavailable For Legal Reasons',

        // Server errors.
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    // Return HTTP code's message.
    if(!empty($http_status_message[(int)$code])) return $http_status_message[(int)$code];
    return false;
}
