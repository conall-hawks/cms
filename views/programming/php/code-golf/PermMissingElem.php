// Link to test: https://codility.com/demo/take-sample-test/perm_missing_elem/
// "Find the missing element in a given permutation."

function perm_missing_elem($A){
    return (((sizeof($A) + 1) * (sizeof($A) + 2)) / 2) - array_sum($A);
}