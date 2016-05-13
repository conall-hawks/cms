// Link to test: https://codility.com/demo/take-sample-test/passing_cars/
// "Count the number of passing cars on the road."

// "100/100" solution. Always assumes the first car is headed east (which is not noted in the description of the problem).
int passing_cars(vector<int> &A){
    int east = 0, passing = 0;
    for(int i = 0; i < A.size(); i++){
        if(A[i] == 0) east++;
		if(A[i] != 0) passing += east;
        if(passing > 1000000000) return -1;
    }
    return passing;
}

// Correct solution based on description of problem given. Checks whether or not the first car is headed east or west (scores 90/100).
int passing_cars(vector<int> &A){
    int key_cars = 0, passing = 0;
    for(int i = 0; i < A.size(); i++){
        if(A[i] == A[0]) key_cars++;
		if(A[i] != A[0]) passing += key_cars;
        if(passing > 1000000000) return -1;
    }
    return passing;
}