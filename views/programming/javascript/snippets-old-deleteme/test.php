<code><?php $code = 
'// This program swaps values of two variables using a function containing call-by-reference arguments.
#include <iostream>
void swap(int &iNum1, int &iNum2);
int main()
{
	int iVar1, iVar2;
	std::cout << "Enter two numbers " << std::endl;
	std::cin >> iVar1;
	std::cin >> iVar2;
	swap(iVar1, iVar2);
	std::cout << "In main: " << iVar1 << " " << iVar2 << std::endl;
	return 0;
}

void swap(int &iNum1, int &iNum2)
{
	int iTemp;
	iTemp = iNum1;
	iNum1 = iNum2;
	iNum2 = iTemp;
	std::cout << "In swap function: " << iNum1 << " " << iNum2 << std::endl;
}
';
echo htmlspecialchars($code);
?></code>