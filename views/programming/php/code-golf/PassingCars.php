// Link to test: https://codility.com/demo/take-sample-test/passing_cars/
// "Count the number of passing cars on the road."

// "100/100" solution. Always assumes the first car is headed east (which is not noted in the description of the problem).
function passing_cars($A){
	$east = $passing = 0;
	for($i = 0; $i < count($A); $i++){
		if($A[$i] == 0) $east++;
		if($A[$i] != 0) $passing += $east;
		if($passing > 1000000000) return -1;
	}
	return $passing;
}

// Correct solution based on description of problem given. Checks whether or not the first car is headed east or west (scores 90/100).
function passing_cars($A){
	$key_cars = $passing = 0;
	for($i = 0; $i < count($A); $i++){
		if($A[$i] == $A[0]) $key_cars++;
		if($A[$i] != $A[0]) $passing += $key_cars;
		if($passing > 1000000000) return -1;
	}
	return $passing;
}