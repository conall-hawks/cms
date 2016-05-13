// Link to test: https://codility.com/demo/take-sample-test/tape_equilibrium/
// "Minimize the value |(A[0] + ... + A[P-1]) - (A[P] + ... + A[N-1])|."

function tape_equilibrium($A) {
    $left = $A[0];
    $right = array_sum($A) - $left;
    $minimum = abs($left - $right);
    for($i = 1; $i < sizeof($A) - 1; $i++){
        $left += $A[$i];
        $right -= $A[$i];
        $difference = abs($left - $right);
        if($difference < $minimum) $minimum = $difference;
    }
    return $minimum;
}
