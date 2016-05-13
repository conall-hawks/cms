// Demonstrates call-by-value parameter behavior with a pointer argument.
#include <iostream>
void sneaky(int *temp);

int main(){
	int *p = new int(101);
	std::cout << "Before function call:     *p == " << *p << std::endl;
	sneaky(p);
	std::cout << "After function call:      *p == " << *p << std::endl;
	return 0;
}

void sneaky(int *temp){
	*temp = 1337;
	std::cout << "Inside the function call: *temp == " << *temp << std::endl;
}