// Link to test: https://codility.com/demo/take-sample-test/max_counters/
// "Calculate the values of counters after applying all alternating operations: increase counter by 1; set value of all counters to current maximum."

// 77/100 solution. Produces correct output, but fails the last performance test. I need a time complexity of O(N+M), and the fill() inside my for-loop is kinda O(N*M)ish.
vector<int> max_counters(int N, vector<int> &A){
    vector<int> counter(N);
    int high = 0;
    for(int i = 0; i < A.size(); i++){
        if(A[i] > 0 && A[i] < N + 1){
            counter[A[i] - 1]++;
            if(counter[A[i] - 1] > high) high = counter[A[i] - 1];
        }else if(A[i] == N + 1){
            fill(counter.begin(), counter.end(), high);
        }else{
            continue;
        }
    }
    return counter;
}