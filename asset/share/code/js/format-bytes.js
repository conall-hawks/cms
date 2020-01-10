/**----------------------------------------------------------------------------\
| Format a number representing bytes.                                          |
+---------+--------+------------+----------------------------------------------+
| @param  | number | path      | Number of bytes.                              |
| @param  | number | precision | Precision of decimal.                         |
| @param  | string | suffix    | Suffix type.                                  |
|         |        |           |                                               |
| @return | string |           | Formatted bytes value.                        |
\---------+--------+-----------+----------------------------------------------*/
function formatBytes(bytes, precision, suffix){

    /* Coerce arguments. */
    bytes = Number(bytes || 0);

    /* Default arguments. */
    if(typeof precision !== "number") precision = 2;
    if(typeof suffix    !== "string") suffix    = "short"

    /* Set unit type. */
    if(suffix === "long"){
        var units = ["Byte"    , "Kibibyte", "Mebibyte", "Gibibyte", "Tebibyte",
                     "Pebibyte", "Exbibyte", "Zebibyte", "Yobibyte"];
    }else{
        var units = ["B", "KiB", "MiB", "GiB", "TiB", "PiB", "EiB", "ZiB", "YiB"];
    }

    /* Handle zero/negative values. */
    if(bytes < 1) return bytes + " " + units[0] + (suffix === "long" ? "s" : "");

    /* Calculate cardinality. */
    var pow = Math.floor((bytes ? Math.log(bytes) : 0) / Math.log(1024));
    pow = Math.min(pow, units.length - 1);

    /* Calculate new value. */
    var value = bytes / Math.pow(1024, pow);
    value = value.toFixed(precision);

    /* Build formatted output. */
    var output = value + " " + units[pow];
    if(suffix === "long" && (value > 1 || Math.ceil(value) === 1)) output += "s";

    /* Return formatted output. */
    return output;
}
