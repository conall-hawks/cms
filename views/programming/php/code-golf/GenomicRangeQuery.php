// Link to test: https://codility.com/demo/take-sample-test/genomic_range_query/
// "Count minimal number of jumps from position X to Y."

function genomic_range_query($S, $P, $Q) {
    for($i = 0; $i < sizeof($P);$i++){
        $temp = substr($S, $P[$i], $Q[$i] - $P[$i] + 1);
        if(strpos($temp, 'A') !== false){
            $min[$i] = 1;
        }elseif(strpos($temp, 'C') !== false){
            $min[$i] = 2;
        }elseif(strpos($temp, 'G') !== false){
            $min[$i] = 3;
        }elseif(strpos($temp, 'T') !== false){
            $min[$i] = 4;
        }
    }
    return $min;
}