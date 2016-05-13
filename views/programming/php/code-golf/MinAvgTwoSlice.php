// Link to test: https://codility.com/demo/take-sample-test/min_avg_two_slice/
// "Find the minimal average of any slice containing at least two elements."

// "100/100" solution. Only checks for slices of 2 and 3 elements (which is not noted in the description of problem).
function min_avg_two_slice($A){
    $avg_2 = $avg_3 = $min_avg = $min_index = NULL;
    for($i = 0; $i < sizeof($A); $i++){
		if(!isset($A[$i + 1])) break;
        $avg_2 = ($A[$i] + $A[$i + 1]) / 2;
		if($avg_2 < $min_avg || $min_avg === NULL){
            $min_avg = $avg_2;
            $min_index = $i;
        }
		if(!isset($A[$i + 2])) break;
        $avg_3 = ($A[$i] + $A[$i + 1] + $A[$i + 2]) / 3;
		if($avg_3 < $min_avg || $min_avg === NULL){
            $min_avg = $avg_3;
            $min_index = $i;
        }
	}
    return $min_index;
}

// Correct solution based on description of problem given (fails performance tests though). This will check for 2 or more elements (all possibilities).
function min_avg_two_slice($A){
    $size = sizeof($A);
    $min_avg = $min_index = NULL;
    for($i = 0; $i < $size; $i++){
		for($j = 1; $j < $size - $i; $j++){
			$avg = array_sum(array_slice($A, $i, $j + 1)) / ($j + 1);
			if($avg < $min_avg || $min_avg === NULL){
                $min_avg = $avg;
                $min_index = $i;
            }
		}
	}
    return $min_index;
}