// An example of how to split strings without regex.
#include <iostream>
#include <string>
#include <cctype>

int main(){
    std::string a = "", s = "", c = "", p = "", z = "";
    std::cout << "Enter an address (or just hit enter): (format: 313 Blah Street, New York NY 90210)\n";
    getline(std::cin, a);
    if(a.empty()) a = "313 Blah Street, New York NY 90210";
    do{
        // Find the street.
        for(int i = 0; i < a.size(); i++){
            if(a[i] == ',' && s.empty()){
            s = a.substr(0, i);
            a.erase(0, i + 2);
            }
        }
        // Find the city.
        for(int i = 0; i < a.size(); i++){
            if(isupper(a[i]) && isupper(a[i + 1]) && isspace(a[i + 2])){
                c = a.substr(0, i - 1);
                a.erase(0, i);
            }
        }
        // Find the state.
        p = a.substr(0, 2);
        a.erase(0, 3);

        // Find the zip.
        z = a;
        a = "";
    }while(!a.empty());
    std::cout << "Street: " << s << std::endl;
    std::cout << "City:   " << c << std::endl;
    std::cout << "State:  " << p << std::endl;
    std::cout << "Zip:    " << z << std::endl;
}
