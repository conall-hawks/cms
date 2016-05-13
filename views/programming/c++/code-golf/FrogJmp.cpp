// Link to test: https://codility.com/demo/take-sample-test/frog_jmp/
// "Count minimal number of jumps from position X to Y."

int frog_jmp(int X, int Y, int D){
    int jumps = (Y - X) / D;
    if((Y - X) % D) jumps++;
    return jumps;
}