// Link to test: https://codility.com/demo/take-sample-test/tape_equilibrium/
// "Minimize the value |(A[0] + ... + A[P-1]) - (A[P] + ... + A[N-1])|."

int tape_equilibrium(vector<int> &A) {
    int left = A[0];
	int right = 0;
    for(int i = 1; i < A.size(); i++) right += A[i];
	int minimum = abs(left - right);
    int difference = minimum;
    for(int i = 1; i < A.size() - 1; i++){
		left += A[i];
		right -= A[i];
        difference = abs(left - right);
        if(difference < minimum) minimum = difference;
    }
    return minimum;
}