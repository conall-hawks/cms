/**----------------------------------------------------------------------------\
| Resolves the Windows' temporary files directory (also known as "%TEMP%").    |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     printf("%s", temp_dir());                                                |
|                                                                              |
| Result:                                                                      |
|     C:\Users\<username>\AppData\Local\Temp\                                  |
+---------+---------+--------+-------------------------------------------------+
| @return | char*   |        | Windows' %TEMP% directory path.                 |
\---------+---------+--------+------------------------------------------------*/
#include <windows.h>
char* temp_dir_windows(){
    char *path = malloc(MAX_PATH);
    GetTempPath(MAX_PATH, path);
    GetLongPathName(path, path, MAX_PATH);
    return path;
}

/**----------------------------------------------------------------------------\
| Example usage.                                                               |
\-----------------------------------------------------------------------------*/
#include <stdio.h>
int main(int argc, char** argv){
    char* path = temp_dir_windows();
    printf("Your temporary directory is at: %s", path);
    free(path);
    sleep(3);
}
