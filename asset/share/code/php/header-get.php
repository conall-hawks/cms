<?php
/**----------------------------------------------------------------------------\
| PHP's missing header_get(). Get response header by key.                      |
+---------+--------+-------+---------------------------------------------------+
| @param  | string | $path | Key of response header.                           |
|         |        |       |                                                   |
| @return | mixed  |       | Header's value or false if not found.             |
\---------+--------+-------+--------------------------------------------------*/
function header_get($query){
    $query = mb_strtolower($query);
    foreach(headers_list() as $header){
        $header = explode(':', $header);
        if(mb_strtolower(trim($header[0])) === $query) return trim($header[1]);
    }
    return false;
}
