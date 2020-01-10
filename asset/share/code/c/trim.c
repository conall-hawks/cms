/**----------------------------------------------------------------------------\
| Trim characters off of the ends of string literals.                          |
+------------------------------------------------------------------------------+
| Example:                                                                     |
|     printf(ltrim("hello", "h");                                              |
|     printf(rtrim("hello ");                                                  |
|     printf( trim("!hello!!", "!");                                           |
| Result:                                                                      |
|     ello                                                                     |
|     hello                                                                    |
|     hello                                                                    |
+---------+-------+--------+---------------------------------------------------+
| @param  | char* | string | String to trim.                                   |
| @param  | char* | mask   | Character to trim; defaults to space (" ").       |
| @return | char* |        | Trimmed string.                                   |
\---------+-------+--------+--------------------------------------------------*/
#include <stdarg.h>
#include <stdlib.h>
#include <string.h>

/* Trim arguments. */
typedef struct {
    char* string;
    char* mask;
} trim_args;

/* Trim left. */
#define ltrim(...) ltrim((trim_args){__VA_ARGS__})
char* (ltrim)(trim_args input){
    input.mask = input.mask ? input.mask : " ";
    char* end = input.string + strlen(input.string) - 1;
    while(end >= input.string && *input.string == *input.mask) input.string++;
    return input.string;
}

/* Trim right. */
#define rtrim(...) rtrim((trim_args){__VA_ARGS__})
char* (rtrim)(trim_args input){
    input.mask = input.mask ? input.mask : " ";
    if(strlen(input.string) < 1) return input.string;
    char* end = input.string + strlen(input.string) - 1;
    while(end >= input.string && *end == *input.mask) end--;
    char* output = malloc((end - input.string + 1) * sizeof(input.string));
    memcpy(output, input.string, end - input.string + 1);
    output[end - input.string + 1] = '\0';
    return output;
}

/* Trim both. */
#define trim(...) trim((trim_args){__VA_ARGS__})
char* (trim)(trim_args input){
    return rtrim(ltrim(input.string, input.mask), input.mask);
}

/**----------------------------------------------------------------------------\
| Utility function for example usage.                                          |
| Concatenates strings.                                                        |
\-----------------------------------------------------------------------------*/
char* concat(char count, ...){

    /* Retrieve variadic arguments. */
    va_list strings;
    va_start(strings, count);

    /* Build concatenated string. */
    char* output = "";
    while(count > 0){

        /* Get string. */
        char* original = output;

        /* Get next argument; string to append. */
        char* append = va_arg(strings, char*);

        /* Get strings' lengths. */
        size_t original_size = strlen(original);
        size_t append_size   = strlen(append);

        /* Allocate memory for output string. */
        output = malloc(original_size + append_size + 1);

        /* Write string to memory. */
        memcpy(output, original, original_size);
        memcpy(output + original_size, append, append_size + 1);

        /* Decrement counter. */
        count--;
    }

    /* Done. */
    va_end(strings);
    return output;
}

/* Automatic counting of arguments. Anonymous variadic macros introduced in C99. */
#define NARG(...) NARG_(__VA_ARGS__, RSEQ_N())
#define NARG_(...) ARG_N(__VA_ARGS__)
#define ARG_N(_1, _2, _3, _4, _5, _6, _7, _8, _9, _10, _11, _12, _13, _14, _15, _16, _17, _18, _19, _20, _21, _22, _23, _24, _25, _26, _27, _28, _29, _30, _31, _32, _33, _34, _35, _36, _37, _38, _39, _40, _41, _42, _43, _44, _45, _46, _47, _48, _49, _50, _51, _52, _53, _54, _55, _56, _57, _58, _59, _60, _61, _62, _63, _64, _65, _66, _67, _68, _69, _70, _71, _72, _73, _74, _75, _76, _77, _78, _79, _80, _81, _82, _83, _84, _85, _86, _87, _88, _89, _90, _91, _92, _93, _94, _95, _96, _97, _98, _99, _100, _101, _102, _103, _104, _105, _106, _107, _108, _109, _110, _111, _112, _113, _114, _115, _116, _117, _118, _119, _120, _121, _122, _123, _124, _125, _126, _127, N, ...) N
#define RSEQ_N() 127, 126, 125, 124, 123, 122, 121, 120, 119, 118, 117, 116, 115, 114, 113, 112, 111, 110, 109, 108, 107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 87, 86, 85, 84, 83, 82, 81, 80, 79, 78, 77, 76, 75, 74, 73, 72, 71, 70, 69, 68, 67, 66, 65, 64, 63, 62, 61, 60, 59, 58, 57, 56, 55, 54, 53, 52, 51, 50, 49, 48, 47, 46, 45, 44, 43, 42, 41, 40, 39, 38, 37, 36, 35, 34, 33, 32, 31, 30, 29, 28, 27, 26, 25, 24, 23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0
#define concat(...) concat(NARG(__VA_ARGS__), __VA_ARGS__)

/**----------------------------------------------------------------------------\
| Example usage.                                                               |
\-----------------------------------------------------------------------------*/
#include <stdio.h>
#include <unistd.h>
int main(int argc, char** argv){
    printf("/============================\\\n");
    printf("| Typical usage:             |\n");
    printf("+----------------------------+\n");
    printf("| %-26s |\n", concat("[ ", ltrim("hello", "h")   , " ]"));
    printf("| %-26s |\n", concat("[ ", rtrim("hello ")       , " ]"));
    printf("| %-26s |\n", concat("[ ",  trim("!hello!!", "!"), " ]"));
    printf("+============================+\n");
    printf("| Trim spaces:               |\n");
    printf("+----------------------------+\n");
    printf("| %-26s |\n", concat("[ ", ltrim("    Left-trim spaces.    ") , " ]"));
    printf("| %-26s |\n", concat("[ ", rtrim("    Right-trim spaces.    "), " ]"));
    printf("| %-26s |\n", concat("[ ",  trim("    Trim spaces.    ")      , " ]"));
    printf("+============================+\n");
    printf("| Trim characters (comma):   |\n");
    printf("+----------------------------+\n");
    printf("| %-26s |\n", concat("[ ", ltrim(",,,,Left-trim commas.,,,," , ","), " ]"));
    printf("| %-26s |\n", concat("[ ", rtrim(",,,,Right-trim commas.,,,,", ","), " ]"));
    printf("| %-26s |\n", concat("[ ",  trim(",,,,Trim commas.,,,,"      , ","), " ]"));
    printf("+============================+\n");
    printf("| Trim empty string:         |\n");
    printf("+----------------------------+\n");
    printf("| %-26s |\n", concat("[ ", ltrim(""), " ]"));
    printf("| %-26s |\n", concat("[ ", rtrim(""), " ]"));
    printf("| %-26s |\n", concat("[ ",  trim(""), " ]"));
    printf("+============================+\n");
    printf("| Trim everything:           |\n");
    printf("+----------------------------+\n");
    printf("| %-26s |\n", concat("[ ", ltrim("##########################", "#"), " ]"));
    printf("| %-26s |\n", concat("[ ", rtrim("##########################", "#"), " ]"));
    printf("| %-26s |\n", concat("[ ",  trim("##########################", "#"), " ]"));
    printf("+============================+\n");
    printf("| Trim nothing:              |\n");
    printf("+----------------------------+\n");
    printf("| %-26s |\n", concat("[ ", ltrim("aaaaaaaaaaaaaaaaaaaaaa", "b"), " ]"));
    printf("| %-26s |\n", concat("[ ", rtrim("aaaaaaaaaaaaaaaaaaaaaa", "b"), " ]"));
    printf("| %-26s |\n", concat("[ ",  trim("aaaaaaaaaaaaaaaaaaaaaa", "b"), " ]"));
    printf("+============================+\n");
    printf("| Trim exotic characters:    |\n");
    printf("+----------------------------+\n");
    printf("| %-26s |\n", concat("[ ", ltrim("\nNewline on my left.", "\n"), " ]"));
    printf("| %-26s |\n", concat("[ ", rtrim("Tab on my right.\t", "\t"), " ]"));
    printf("| %-26s |\n", concat("[ ",  trim("\177DEL on both sides\177", "\177"), " ]"));
    printf("\\----------------------------/\n");
    sleep(3);
    return 0;
}

