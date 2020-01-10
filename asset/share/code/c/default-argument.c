/**----------------------------------------------------------------------------\
| An example of how to create functions with default arguments in ANSI C. This |
| is unfortunately not a very trivial task.                                    |
\-----------------------------------------------------------------------------*/
#include <stdio.h>

/* Define a struct containing arguments for my_function(). */
typedef struct {
    char   arg_1;
    double arg_2;
    int    arg_3;
    long   arg_4;
    short  arg_5;
} my_function_args;

/* Define a macro for my_function(). */
#define my_function(...) my_function((my_function_args){__VA_ARGS__})

/* Define my_function() itself. */
void (my_function)(my_function_args input){

    /* Default arguments. */
    input.arg_1 = input.arg_1 ? input.arg_1 : 127;
    input.arg_2 = input.arg_2 ? input.arg_2 : 3.14;
    input.arg_3 = input.arg_3 ? input.arg_3 : 1000000;
    input.arg_4 = input.arg_4 ? input.arg_4 : 500000000;
    input.arg_5 = input.arg_5 ? input.arg_5 : 1337;

    /* Put function stuff here. */
    printf("Argument 1 is: %10d (Default: 127)\n"       , input.arg_1);
    printf("Argument 2 is: %10.2f (Default: 3.14)\n"    , input.arg_2);
    printf("Argument 3 is: %10d (Default: 1000000)\n"   , input.arg_3);
    printf("Argument 4 is: %10ld (Default: 500000000)\n", input.arg_4);
    printf("Argument 5 is: %10hd (Default: 1337)\n\n"   , input.arg_5);
}

/**----------------------------------------------------------------------------\
| Example usage.                                                               |
\-----------------------------------------------------------------------------*/
int main(int argc, char** argv){

    /* No arguments. */
    printf("--------------------------------------------------------------------------------");
    printf("my_function():\n\n");
    my_function();

    /* One argument. */
    printf("--------------------------------------------------------------------------------");
    printf("my_function(32):\n\n");
    my_function(32);

    /* Two arguments. */
    printf("--------------------------------------------------------------------------------");
    printf("my_function(32, 6.02):\n\n");
    my_function(32, 6.02);

    /* Three arguments. */
    printf("--------------------------------------------------------------------------------");
    printf("my_function(32, 6.02, 64):\n\n");
    my_function(32, 6.02, 64);

    /* Four arguments. */
    printf("--------------------------------------------------------------------------------");
    printf("my_function(32, 6.02, 64, 999999999):\n\n");
    my_function(32, 6.02, 64, 999999999);

    /* And so on... */
    printf("--------------------------------------------------------------------------------");
    printf("my_function(32, 6.02, 64, 999999999, 32767):\n\n");
    my_function(32, 6.02, 64, 999999999, 32767);

    /* Done. */
    return 0;
}
