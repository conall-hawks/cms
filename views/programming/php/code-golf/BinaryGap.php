// Link to test: https://codility.com/demo/take-sample-test/binary_gap/
// "Find longest sequence of zeros in binary representation of an integer."

function binary_gap($N){
	$N = (string)decbin($N);
	$gap = $highest = $mark = 0;
	for($i = 0; $i < strlen($N); $i++){
		if($N[$i] == 1){
			$gap = $i - $mark;
			$mark = $i;
			if($gap > $highest) $highest = $gap - 1;
		}
	}
	return (int)$highest;
}