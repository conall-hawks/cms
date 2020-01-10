<?php
/**----------------------------------------------------------------------------\
| Format a number representing bytes.                                          |
+---------+--------+------------+----------------------------------------------+
| @param  | int    | $path      | Number of bytes.                             |
| @param  | int    | $precision | Precision of decimal.                        |
| @param  | string | $suffix    | Suffix type.                                 |
|         |        |            |                                              |
| @return | string |            | Number of bytes formatted.                   |
\---------+--------+------------+---------------------------------------------*/
function format_bytes($bytes, $precision = 2, $suffix = 'short'){

    /* Set unit type. */
    if($suffix === 'long'){
        $units = ['Byte'    , 'Kibibyte', 'Mebibyte', 'Gibibyte', 'Tebibyte',
                  'Pebibyte', 'Exbibyte', 'Zebibyte', 'Yobibyte'];
    }else{
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
    }

    /* Handle zero/negative values. */
    if($bytes < 1) return $bytes.'&nbsp;'.$units[0].($suffix === 'long' ? 's' : '');

    /* Calculate cardinality. */
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    /* Calculate new value. */
    $value = $bytes / pow(1024, $pow);
    $value = round($value, $precision);

    /* Don't add precision to the smallest unit. */
    if(!$pow) $precision = 0;

    /* Build formatted output. */
    $output  = number_format($value, $precision).'&nbsp;'.$units[$pow];
    if($suffix === 'long' && ($value > 1 || (int)ceil($value) === 1)) $output .= 's';

    /* Return formatted output. */
    return $output;
}
