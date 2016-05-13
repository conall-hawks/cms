// Link to test: https://codility.com/demo/take-sample-test/binary_gap/
// "Find longest sequence of zeros in binary representation of an integer."

std::string dec2bin(int dec){
    std::string bin;  
    while(dec != 0){
       bin += (dec & 1) ? '1' : '0';
       dec >>= 1;
    }
    return bin;
}

int binary_gap(int N){
    std::string bin_num = dec2bin(N);
    int gap = 0;
    int highest = 0;
    int mark = 0;
    for(int i = 0; i < bin_num.size(); i++){
        if(bin_num[i] == '1'){
            gap = i - mark;
            mark = i;
            if(gap > highest) highest = gap - 1;
        }
    }
    return highest;
}