//Searches a list of numbers entered at the keyboard
#include <iostream>
using namespace std;

//Some prefer to use an alias. However, I do not.
typedef int* IntPtr;

void fillArray(int a[], int size);
//Precondition: size is the size of the array a.
//Postcondition: a[0] through a[size-1] have been filled with values read from the keyboard.

int search(int a[], int size, int target);
//Precondition: size is the size of the array a.
//Postcondition: The array elements a[0] through a[size-1] have values.
//If target is not in the array, returns the first index of target.
//If target is not in the array, returns -1

int main() {
	cout << "This program searches a list of numbers.\n";

	int arraySize;
	cout << "How many numbers will be on the list? ";
	cin >> arraySize;
	int *a;
	a = new int[arraySize];
	
	fillArray(a, arraySize);
	
	int target;
	cout << "Enter a value to search for: ";
	cin >> target;
	int location = search(a, arraySize, target);
	if (location == -1)
		cout << target << " is not in the array.\n";
	else
		cout << target << " is element " << location << " in the array.\n";
	
	delete [] a;
	
	return 0;
}

void fillArray(int a[], int size) {
	cout << "Enter " << size << " integers (space delimited): ";
	for (int i = 0; i < size; i++)
		cin >> a[i];
}

int search(int a[], int size, int target) {
	int i = 0;
	while ((a[i] != target) && (i < size))
		i++;
	if (i == size)	//if target is not in a.
		i = -1;
	return i;
}