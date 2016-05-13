// Link to test: https://codility.com/demo/take-sample-test/frog_river_one/
// "Find the earliest time when a frog can jump to the other side of a river."

// 72/100 solution. Produces correct output, but fails the last few performance tests. I need a time complexity of O(N+M).
int frog_river_one(int X, vector<int> &A){
	int total = X * (X + 1) / 2;
    for(int i = 0; i < A.size(); i++){
        for(int j = 0; j < i; j++){
            if(A[i] == A[j] && i != 0){
                A[i] = 0;
                break;
            }
        }
        if(A[i] == 0) continue;
        total = total - A[i];
        if(total == 0) return i;
    }
    return -1;
}