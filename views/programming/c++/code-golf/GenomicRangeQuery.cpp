// Link to test: https://codility.com/demo/take-sample-test/genomic_range_query/
// "Find the minimal nucleotide from a range of sequence DNA."

// 62/100 solution. Produces correct output, but fails performance tests. I need a time complexity of O(N+M).
vector<int> genomic_range_query(string &S, vector<int> &P, vector<int> &Q){
    vector<int> low(P.size());
    for(unsigned int i = 0; i < P.size(); i++){
        for(int j = P[i]; j <= Q[i]; j++){
            switch (S[j]){
                case 'A':
                    S[j] = 1;
                    break;
                case 'C':
                    S[j] = 2;
                    break;
                case 'G':
                    S[j] = 3;
                    break;
                case 'T':
                    S[j] = 4;
                    break;
            }
            if(S[j] < low[i] || low[i] == 0) low[i] = S[j];
        }
    }
    return low;
}