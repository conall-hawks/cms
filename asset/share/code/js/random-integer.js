/**----------------------------------------------------------------------------\
| Get a random integer.                                                        |
+---------+--------+-----+-----------------------------------------------------+
| @param  | number | min | Minimum possible.                                   |
| @param  | number | max | Maximum possible.                                   |
| @return | number |     | Random integer.                                     |
\---------+--------+-----+-----------------------------------------------------*/
function rand(min,max){
    if(typeof min !== "number") min = 0;
    if(typeof max !== "number") max = 1;
    return Math.floor(Math.random() * (max - min + 1) + min);
}
