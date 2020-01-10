/**----------------------------------------------------------------------------\
| Read input from the user.                                                    |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     printf("%s\n", readline());                                              |
|                                                                              |
| Result:                                                                      |
|     <Whatever the user just typed in.>                                       |
+---------+-------+--------+---------------------------------------------------+
| @return | char* | result | Whatever the user just typed in.                  |
\---------+-------+--------+--------------------------------------------------*/
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
char *readline(void){
    char *result = "";
    while(1){

        /* Get character from stdin. */
        char character = (char)fgetc(stdin);
        if(character == '\0' || character == '\n') break;

        /* Convert to mutable (C-string pointer). */
        char *append = strdup((char*)&character);

        /* Ensure only one character then NUL. */
        append[1] = '\0';

        /* Get string. */
        char *original = result;
        size_t original_size = strlen(original);

        /* Write string. */
        result = malloc(original_size + 2);
        memcpy(result, original, original_size);
        memcpy(result + original_size, append, 2);
    }
    return result;
}

/**----------------------------------------------------------------------------\
| Example usage.                                                               |
\-----------------------------------------------------------------------------*/
#include <unistd.h>
int main(int argc, char **argv){

    /* Get a string from the user. */
    printf("Enter a string, and I'll say it back to you (CTRL+C to quit).\n");
    while(1) printf("%s\n----------\n", readline());

    /* Done. */
    return 0;
}
