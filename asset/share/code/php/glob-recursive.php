<?php
/**----------------------------------------------------------------------------\
| Recursive glob.                                                              |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     echo '<pre>';                                                            |
|     echo var_dump(rglob('/'));                                               |
|     echo '</pre>';                                                           |
|                                                                              |
| Result:                                                                      |
+---------+--------+--------+--------------------------------------------------+
| @param  | string | $input | A string to be turned into a title.              |
| @param  | array  | $input | An array of strings to be turned into titles.    |
|         |        |        |                                                  |
| @return | string |        | A string in "Title Case".                        |
| @return | array  |        | An array of strings in "Title Case".             |
\---------+--------+--------+-------------------------------------------------*/
function rglob($path){
    /* Query contents using an iterator. */
    $inodes = new FilesystemIterator($path);

    /* Convert to an associative array. */
    $inodes = iterator_to_array($inodes);

    /* Sort by name. */
    uasort($inodes, function($a, $b){return strnatcasecmp($a->getFilename(), $b->getFilename());});

    /* Sort by type. */
    uasort($inodes, function($a, $b){
        if($a->isDir() && !$b->isDir()) return -1;
        if(!$a->isDir() && $b->isDir()) return 1;
        return strnatcasecmp($a->getFilename(), $b->getFilename());
    });

    /* Build directory tree. */
    $tree = [];
    foreach($inodes as $inode){

        /* Normalize directory separators. */
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $inode->getPathname());

        /* Recurse into subdirectories. */
        if($inode->isDir()){
            $tree[$path] = rglob($path);
            continue;
        }
        array_push($tree, $path);
    }
    return $tree;
}
