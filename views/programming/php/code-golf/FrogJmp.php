// Link to test: https://codility.com/demo/take-sample-test/frog_jmp/
// "Count minimal number of jumps from position X to Y."

function frog_jmp($X, $Y, $D){
    $temp = intval(($Y - $X) / $D);
    if(($Y - $X) % $D) $temp++;
    return $temp;
}