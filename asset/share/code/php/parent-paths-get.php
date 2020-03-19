<?php
/**----------------------------------------------------------------------------\
| Parent paths resolution. Returns all the parent paths of a given path.       |
| Usage:                                                                       |
|     print_r(get_parent_paths('/usr/lib/python2.7/site-packages'));           |
|                                                                              |
| Result:                                                                      |
|     Array                                                                    |
|     (                                                                        |
|         [1] => /usr                                                          |
|         [2] => /usr/lib                                                      |
|         [3] => /usr/lib/python2.7                                            |
|         [4] => /usr/lib/python2.7/site-packages                              |
|     )                                                                        |
+---------+--------+-------+---------------------------------------------------+
| @param  | string | $path | A path to be turned into parent paths.            |
|         |        |       |                                                   |
| @return | array  |       | An array of parent paths from $path.              |
\---------+--------+-------+--------------------------------------------------*/
function get_parent_paths($path){

    /* Break path apart into segments. */
    $segments = explode('/', preg_replace('/\/+/','/', rtrim($path, '/')));

    /* Build array; iterate over each segment, appending remaining segments. */
    $count = count($segments);
    $paths = [];
    for($i = 1; $i <= $count; $i++){
        $paths[] = implode('/', array_slice($segments, 0, $i));
    }

    /* If an absolute path was given, correct the first array element. */
    if(substr($path, 0, 1) === '/' && $paths[0] === '') $paths[0] = '/';

    /* Done; filter out duplicates. */
    return array_unique($paths);
}
