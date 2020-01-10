<?php
/**----------------------------------------------------------------------------\
| Delete a file or directories recursively.                                    |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     mkdir('/tmp/example_dir');                                               |
|     mkdir('/tmp/example_dir/nested_dir');                                    |
|     file_put_contents('/tmp/example_dir/nested_dir/test.txt', 'Hello world');|
|     runlink('/tmp/example_dir');                                             |
|     print_r(array_diff(scandir('/tmp'), ['.', '..']));                       |
|                                                                              |
| Result:                                                                      |
|     Array()                                                                  |
+---------+---------+--------+-------------------------------------------------+
| @param  | string  | $inode | Path to a file or directory.                    |
\---------+--------+--------+-------------------------------------------------*/
function runlink($inode){
    if(is_dir($inode)){
        $inodes = new FilesystemIterator($inode);
        foreach($inodes as $inode) runlink($inode->getPathname());
        if(is_dir($inode)) rmdir($inode);
    }else{
        unlink($inode);
    }
}
