<?php
/**----------------------------------------------------------------------------\
| Trims suffixes off of strings.                                               |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     echo trim_suffix('hello_world', '_world');                               |
|                                                                              |
| Result:                                                                      |
|     hello                                                                    |
+---------+--------+---------+-------------------------------------------------+
| @param  | string | $string | A string with a suffix to be trimmed.           |
| @param  | string | $suffix | The suffix to trim.                             |
|         |        |         |                                                 |
| @return | string |         | The string minus its suffix.                    |
\---------+--------+---------+------------------------------------------------*/
function trim_suffix($string, $suffix = ' '){
    $length = strlen($suffix);
    if(substr($string, -$length, $length) === $suffix){
        return substr($string, 0, -$length);
    }
    return $string;
}
