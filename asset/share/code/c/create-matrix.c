/**----------------------------------------------------------------------------\
| Creates a two dimensional number matrix.                                     |
+---------+-----+--------+-----------------------------------------------------+
| @param  | int | rows   | Number of rows.                                     |
| @param  | int | cols   | Number of columns.                                  |
| @return | int | matrix | 2D array of integers.                               |
\---------+-----+--------+----------------------------------------------------*/
#include <stdlib.h>
int **matrix(int rows, int cols){

    /* Allocate rows. */
    int **matrix = malloc(rows * sizeof(int*));

    /* Check for failure. */
    if(matrix == NULL) return NULL;

    /*  Allocate columns. */
    int i = 0;
    for(; i < rows; i++){
        matrix[i] = malloc(cols * sizeof(int));

        /* Check for failure. */
        if(matrix[i] == NULL) return NULL;
    }

    /* Done. */
    return matrix;
}

/**----------------------------------------------------------------------------\
| Example usage.                                                               |
\-----------------------------------------------------------------------------*/
#include <stdio.h>
#include <unistd.h>
int main(int argc, char **argv){

    /* Create a 10x10 number matrix. */
    int **int_matrix = matrix(10, 10);

    /* Fill matrix. */
    int i = 0, j;
    for(; i < sizeof(int_matrix) + 2; i++){
        for(j = 0; j < sizeof(int_matrix[i]) + 2; j++){
            int_matrix[i][j] = (i + 1) * (j + 1);
        }
    }

    /* Print matrix. */
    printf("/------+------+------+------+------+------+------+------+------+------\\\n");
    for(i = 0; i < sizeof(int_matrix) + 2; i++){
        printf("| ");
        for(j = 0; j < sizeof(int_matrix[i]) + 2; j++){
            printf("%4i | ", int_matrix[i][j]);
        }

        if(i < sizeof(int_matrix) + 1){
            printf("\n+------+------+------+------+------+------+------+------+------+------+\n");
        }
    }
    printf("\n\\------+------+------+------+------+------+------+------+------+------/\n");

    /* Cleanup. */
    for(i = 0; i < sizeof(int_matrix) + 2; i++) free(int_matrix[i]);
    free(int_matrix);

    /* Done. */
    sleep(3);
    return 0;
}
