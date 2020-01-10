<?php
/**----------------------------------------------------------------------------\
| Case-insensitive glob.                                                       |
+---------+--------+--------+--------------------------------------------------+
| @param  | string | $path  | Path to search for.                              |
| @param  | int    | $flags | Options to pass to glob().                       |
|         |        |        |                                                  |
| @return | array  |        | Case-insensitive glob() results.                 |
\---------+--------+--------+-------------------------------------------------*/
function iglob($path, $flags = 0){

    /* Start RegEx. */
    $regex = '';

    /* Iterate over each character. */
    for($i = 0, $length = mb_strlen($path); $i < $length; $i++){

        /* Grab character. */
        $char = mb_substr($path, $i, 1);
        $char_lc = mb_strtolower($char);
        $char_uc = mb_strtoupper($char);


        /* Append RegEx with case insensitive character. */
        $regex .= $char_lc === $char_uc ? $char : '['.$char_lc.$char_uc.']';
    }

    /* Locate file. */
    return glob($regex, $flags);
}
