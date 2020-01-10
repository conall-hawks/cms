/**----------------------------------------------------------------------------\
| Split a string into an array of strings.                                     |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     char **strings = explode("Explode me!");                                 |
|     while(*strings) printf("%s\n", *strings++);                              |
|                                                                              |
| Result:                                                                      |
|     Explode                                                                  |
|     me!                                                                      |
+---------+---------+--------+-------------------------------------------------+
| @param  | char*   | input  | string to be turned into an array.              |
| @param  | char*   | delim  | Delimiter which will split the string.          |
| @return | char**  | result | Array of strings.                               |
\---------+---------+--------+------------------------------------------------*/
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

/* explode() arguments. */
typedef struct {
    const char *input;
    const char *delim;
} explode_args;

/* explode() macro. */
#define explode(...) explode((explode_args){__VA_ARGS__})

/* explode() function. */
char** (explode)(explode_args arg){

    /* Required argument. */
    if(!sizeof arg.input) return NULL;

    /* Default delimiter argument. */
    arg.delim = (arg.delim && arg.delim[0] != '\0') ? arg.delim : " ";

    /* Input string is the same as the delimiter. */
    if(arg.input == arg.delim) return NULL;

    /* Tokenize string. */
    char *token = strtok(strdup(arg.input), arg.delim);

    /* Build array. */
    char **result = malloc(sizeof(*result));
    int count = 0;
    while(1){

        /* Append array with next word. */
        result[count++] = token ? strdup(token) : token;

        if(!token) break;

        /* Get next word. */
        token = strtok(NULL, arg.delim);

        /* Enlarge array. */
        size_t size_new = ((size_t)count + 1) * sizeof(*result);
        char **result_new = realloc(result, size_new);
        if(result_new == NULL){
            printf("Unable to allocate %u bytes at: %p.\n", (int)size_new, (void*)&result);
            free(result);
            continue;
        }
        result = result_new;
    }

    /* Done. */
    return result;
}

/**----------------------------------------------------------------------------\
| Example usage.                                                               |
\-----------------------------------------------------------------------------*/
#include <unistd.h>
char *readline();
int main(int argc, char **argv){

    /* Get a string from the user. */
    printf("Please enter a string (or leave blank to show example):\n");
    char *input = readline();

    /* Remove trailing LF/CRLF. */
    size_t length = strlen(input);
    while(length && ((input[length - 1] == '\n') || (input[length - 1] == '\r'))){
        input[length - 1] = '\0';
        length = strlen(input);
    }

    /* Default string if nothing is entered. */
    if(input[0] == '\0'){
        input = strdup("Explode me; this is an example of an array of strings made from a single exploded string!");
        if(input == NULL){
            printf("Unable to allocate memory at: %p.\n", (void*)&input);
            return 1;
        }
    }

    /* Get a delimiter from the user. */
    printf("Please enter a delimiter (or leave blank to use space):\n");
    char *delim = readline();

    /* Remove trailing LF/CRLF. */
    length = strlen(delim);
    while(length && ((delim[length - 1] == '\n') || (delim[length - 1] == '\r'))){
        delim[length - 1] = '\0';
        length = strlen(delim);
    }

    /* Explode string. */
    char **strings = explode(input, delim);

    /* Cleanup. */
    if(input[0] != '\0') free(input);
    if(delim[0] != '\0') free(delim);

    /* Print array of strings. */
    int i = 0;
    printf("[\n");
    do{
        /* Output. */
        printf("    \"%s\"", strings[i]);
        if(strings[i] != '\0') printf(", ");
        printf("\n");

        /* Cleanup. */
        free(strings[i]);

    }while(strings[i++] != '\0');
    printf("]");


    /* Cleanup. */
    free(strings);

    /* Done. */
    sleep(10);
    return 0;
}

/**----------------------------------------------------------------------------\
| Utility function for example usage.                                          |
| Read user input character-by-character dynamically.                          |
\-----------------------------------------------------------------------------*/
char *readline(){
    char *output = "";
    while(1){

        /* Get character. */
        char character = fgetc(stdin);
        if(character == '\n') break;

        /* Get string. */
        char *original = output;

        /* Convert into mutable C-string pointer. */
        char *append = strdup((char*)&character);

        /* Get strings' lengths. */
        size_t original_size = strlen(original);
        size_t append_size   = strlen(append);

        /* Ensure we captured only one character; append NUL byte. */
        if(append_size > 1){
            append[1] = '\0';
            append_size = 1;
        }

        /* Allocate memory for output string. */
        output = malloc(original_size + append_size + 1);

        /* Write string to memory. */
        memcpy(output, original, original_size);
        memcpy(output + original_size, append, append_size + 1);
    }
    return output;
}
