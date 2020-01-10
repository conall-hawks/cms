<?php
/**----------------------------------------------------------------------------\
| Trims prefixes off of strings.                                               |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     echo trim_prefix('hello_world', 'hello_');                               |
|                                                                              |
| Result:                                                                      |
|     world                                                                    |
+---------+--------+---------+-------------------------------------------------+
| @param  | string | $string | A string with a prefix to be trimmed.           |
| @param  | string | $prefix | The prefix to trim.                             |
|         |        |         |                                                 |
| @return | string |         | The string minus its prefix.                    |
\---------+--------+---------+------------------------------------------------*/
function trim_prefix($string, $prefix = ' '){
    $length = strlen($prefix);
    if(substr($string, 0, $length) === $prefix){
        return substr($string, $length);
    }
    return $string;
}
