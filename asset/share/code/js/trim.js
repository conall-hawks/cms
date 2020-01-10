/**----------------------------------------------------------------------------\
| A trim function which allows a specified character mask.                     |
+---------+--------+-------+---------------------------------------------------+
| @param  | string | input | String to trim.                                   |
| @param  | string | mask  | Character mask.                                   |
|         |        |       |                                                   |
| @return | string |       | String trimmed using character mask.              |
\---------+--------+-------+--------------------------------------------------*/
function trim(string, mask){
    while(mask.indexOf(string[0]) !== -1)                 string = string.slice(1);
    while(mask.indexOf(string[string.length - 1]) !== -1) string = string.slice(0, -1);
    return string;
}
