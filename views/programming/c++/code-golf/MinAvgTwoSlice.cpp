// Link to test: https://codility.com/demo/take-sample-test/min_avg_two_slice/
// "Find the minimal average of any slice containing at least two elements."

// "100/100" solution. Only checks for slices of 2 and 3 elements (which is not noted in the description of problem).
int min_avg_two_slice(vector<int> &A){
	int size = A.size(), low_index = 0;
	float low = (A[0] + A[1]) / 2, temp = 0;
 
	for(int i = 0; i < size - 2; i++){
		temp = float(A[i] + A[i + 1]) / 2;
		if(temp < low){
			low = temp;
			low_index = i;
		}
		temp = float(A[i] + A[i + 1] + A[i + 2]) / 3;
		if(temp < low){
			low = temp;
			low_index = i;
		}
	}

	temp = float(A[size - 1] + A[size - 2]) / 2;
	if(temp < low){
		low = temp;
		low_index = size - 2;
	}
	
	return low_index;
}