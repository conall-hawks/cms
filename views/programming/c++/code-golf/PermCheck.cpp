// Link to test: https://codility.com/demo/take-sample-test/perm_check/
// "Check whether array A is a permutation."

// Relies heavily on <algorithm>, but scores 100/100. The for loop is range-based.
#include <algorithm>
int perm_check(vector<int> &A){
    int t_num = (A.size() * (A.size() + 1)) / 2;
	std::sort(A.begin(), A.end());
	A.erase(std::unique(A.begin(), A.end()), A.end());
    int sum = 0;
    for(int n : A) sum += n;
    if(t_num - sum == 0) return 1;
    return 0;
}