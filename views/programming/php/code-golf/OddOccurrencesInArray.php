// Link to test: https://codility.com/demo/take-sample-test/odd_occurrences_in_array/
// "Find value that occurs in odd number of elements."

function odd_occurrences_in_array($A){
    $A = array_count_values($A);
    foreach($A as $key => $num) if($num % 2 != 0) return $key;
}