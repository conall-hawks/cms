/**----------------------------------------------------------------------------\
| Implode an array of C-strings into a single C-string.                        |
+---------+--------+---------+-------------------------------------------------+
| @param  | char** | strings | Array of strings.                               |
| @param  | char*  | glue    | String placed between implosions.               |
| @return | char*  | result  | Imploded strings.                               |
\---------+--------+---------+-------------------------------------------------*/
#include <stdlib.h>
#include <string.h>
char *implode(char **strings, const char *glue){

    /* Build concatenated string. */
    int i = 0;
    char *output = "";
    for(; strings[i] != '\0'; i++){

        /* Get string. */
        char *original = output;

        /* Get string to append. */
        char *append = strings[i];
        if(strings[i + 1] != '\0') append = strcat(strings[i], glue);

        /* Get strings' lengths. */
        size_t original_size = strlen(original);
        size_t append_size   = strlen(append);

        /* Allocate memory for output string. */
        output = malloc(original_size + append_size + 1);

        /* Write string to memory. */
        memcpy(output, original, original_size);
        memcpy(output + original_size, append, append_size + 1);
    }

    /* Done. */
    return output;
}

/**----------------------------------------------------------------------------\
| Example usage.                                                               |
\-----------------------------------------------------------------------------*/
#include <stdio.h>
#include <unistd.h>
int main(int argc, char **argv){

    /* Create dynamic array of character pointers. */
    char **strings = malloc(2 * sizeof(char*));

    /* Create strings. */
    char *string_1 = "Implode";
    char *string_2 = "me!";

    /* Add strings to array. */
    strings[0] = strdup(string_1);
    strings[1] = strdup(string_2);

    /* Print example. */
    printf("%s \n", implode(strings, " "));

    /* Cleanup. */
    free(strings);

    /* Done. */
    sleep(3);
    return 0;
}
