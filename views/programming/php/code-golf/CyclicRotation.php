// Link to test: https://codility.com/demo/take-sample-test/cyclic_rotation/
// "Rotate an array to the right by a given number of steps."

function solution($A, $K){
    if(empty($A)) return $A;
    for($K; $K > 0; $K--){
        $temp = array_pop($A);
        array_unshift($A, $temp);
    }
    return $A;
}