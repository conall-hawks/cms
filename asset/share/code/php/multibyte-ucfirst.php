<?php
/**----------------------------------------------------------------------------\
| PHP's missing mb_ucfirst function.                                           |
+---------+--------+-----------+-----------------------------------------------+
| @param  | string | $string   | The string being first-letter uppercased.     |
| @param  | string | $encoding | Explicitly defined character encoding.        |
|         |        |           |                                               |
| @return | string |           | Case-insensitive glob() results.              |
\---------+--------+-----------+----------------------------------------------*/
function mb_ucfirst($string, $encoding = NULL){
    if(!$encoding) $encoding = mb_internal_encoding();
    $strlen = mb_strlen($string, $encoding);
    $first  = mb_substr($string, 0, 1, $encoding);
    $string = mb_substr($string, 1, $strlen - 1, $encoding);
    return mb_strtoupper($first, $encoding).$string;
}
