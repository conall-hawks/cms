//Demonstrates pointers and dynamic variables
#include <iostream>

int main() {
	int *p1, *p2;
	
	p1 = new int(18);
	p2 = new int(42);
	std::cout << "Pointing to different addresses:" << std::endl;
	std::cout << "  *p1 == " << *p1 << " @ " << p1 << std::endl;
	std::cout << "  *p2 == " << *p2 << " @ " << p2 << std::endl;
	
	p2 = p1;
	std::cout << "p1 & p2 pointing to the same address:" << std::endl;
	std::cout << "  *p1 == " << *p1 << " @ " << p1 << std::endl;
	std::cout << "  *p2 == " << *p2 << " @ " << p2 << std::endl;
	
	*p1 = 53;
	std::cout << "Give that address a new value:" << std::endl;
	std::cout << "  *p1 == " << *p1 << " @ " << p1 << std::endl;
	std::cout << "  *p2 == " << *p2 << " @ " << p2 << std::endl;
	
	p1 = new int;
	*p1 = 88;
	std::cout << "p1 pointing to a new address:" << std::endl;
	std::cout << "  *p1 == " << *p1 << " @ " << p1 << std::endl;
	std::cout << "  *p2 == " << *p2 << " @ " << p2 << std::endl;
	
	delete p1;
	delete p2;
	std::cout << "After deletion (dangling pointers):" << std::endl;
	std::cout << "  *p1 == " << *p1 << " @ " << p1 << std::endl;
	std::cout << "  *p2 == " << *p2 << " @ " << p2 << std::endl;
	
	p1 = NULL;
	p2 = NULL;
	std::cout << "Dangling pointers set to NULL:" << std::endl;
	//the following conditional statements are a method of error-checking
	if (p1 == NULL) {
		std::cout << "  *p1 == <undefined> @ " << p1 << std::endl;
	} else {
		std::cout << "  *p1 == " << *p1 << " @ " << p1 << std::endl;
	}
	if (p2 == NULL) {
		std::cout << "  *p2 == <undefined> @ " << p2 << std::endl;
	} else {
		std::cout << "  *p2 == " << *p2 << " @ " << p2 << std::endl;
	}
	
	std::cout << "End of demonstration.";
	return 0;
}