// Link to test: https://codility.com/demo/take-sample-test/count_div/
// "Compute number of integers divisible by K in the range A to B."

int count_div(int A, int B, int K){
    return (B / K) - (A / K) + !(A % K);
}