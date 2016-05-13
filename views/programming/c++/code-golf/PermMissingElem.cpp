// Link to test: https://codility.com/demo/take-sample-test/perm_missing_elem/
// "Find the missing element in a given permutation."

int perm_missing_elem(vector<int> &A){
	int total = 0;
	int max = 0;
	for(int i = 0; i < A.size(); i++) total = total + A[i];
	for(int i = 1; i <= A.size() + 1; i++) max = max + i;
	return max - total;
}