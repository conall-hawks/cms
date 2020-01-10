#include <iostream>
#include <ctime>

//play the game
void play();
//print the game board
void printBoard(int a[4][4], int b[4][4]);
//shuffle the game board
int shuffleBoard(int (&a)[4][4]);

int main() {
    std::cout << "Welcome to the memory game! Cards are flipped by entering a corresponding X/Y coordinate." << std::endl;
    std::cout << "First, X is requested, then Y." << std::endl;
    short startAgain;
    do {
        play();
        std::cout << "Would you like to play again?" << std::endl;
        std::cout << "1. Yes" << std::endl;
        std::cout << "2. No" << std::endl;
        std::cin >> startAgain;
    } while (startAgain == 1);
    return 0;
}

void play() {
    int cards[4][4] = { {1,2,3,4},
                {5,6,7,8},
                {1,2,3,4},
                {5,6,7,8} };

    int flipped[4][4] = { {0,0,0,0},
                  {0,0,0,0},
                  {0,0,0,0},
                  {0,0,0,0} };
    //shuffle the game board
    shuffleBoard(cards);

    int completion = 0;
    do {
        //ask for first card to flip
        printBoard(cards, flipped);
        int firstX = 0, firstY = 0;
        std::cout << "Choose the first card to flip." << std::endl;
        do {
            do {
                std::cout << "Column: ";
                std::cin >> firstY;
            } while (firstY < 1 || firstY > 4);
            do {
                std::cout << "Row: ";
                std::cin >> firstX;
            } while (firstX < 1 || firstX > 4);
            if (flipped[firstX - 1][firstY - 1] == 0) {
                std::cout << "\n\n\n\n\n\n\n\n\n\n";
                std::cout << std::endl << "Flipping " << firstY << "," << firstX << std::endl;
                flipped[firstX - 1][firstY - 1] = 1;
            } else {
                std::cout << "You cannot choose that card. (Already flipped)" << std::endl;
            }
        } while (flipped[firstX - 1][firstY - 1] != 1);

        //ask for second card to flip
        printBoard(cards, flipped);
        int secondX = 0, secondY = 0;
        std::cout << "Choose the second card to flip." << std::endl;
        do {
            do {
                std::cout << "Column: ";
                std::cin >> secondY;
            } while (firstY < 1 || secondY > 4);
            do {
                std::cout << "Row: ";
                std::cin >> secondX;
            } while (firstX < 1 || secondX > 4);
            if (firstX == secondX && firstY == secondY) {
                std::cout << "You cannot choose that card. (Same card)" << std::endl;
            } else {
                if (flipped[secondX - 1][secondY - 1] == 0) {
                std::cout << "\n\n\n\n\n\n\n\n\n\n";
                    std::cout << std::endl << "Flipping " << secondY << "," << secondX << std::endl;
                    flipped[secondX - 1][secondY - 1] = 1;
                } else {
                    std::cout << "You cannot choose that card. (Already flipped)" << std::endl;
                }
            }
        } while (firstX == secondX && firstY == secondY || flipped[secondX - 1][secondY - 1] != 1);

        //compare the cards to see if we have a win
        if (cards[firstX - 1][firstY - 1] == cards[secondX - 1][secondY - 1]) {
            flipped[firstX - 1][firstY - 1] = 2;
            flipped[secondX - 1][secondY - 1] = 2;
            std::cout << "Winner! You found a pair of: " << cards[firstX - 1][firstY - 1] << std::endl;
        } else {
            printBoard(cards, flipped);
            flipped[firstX - 1][firstY - 1] = 0;
            flipped[secondX - 1][secondY - 1] = 0;
            std::cout << "No match, please try again." << std::endl;
        }

        //calculate game completion
        completion = 0;
        for (int x = 0; x < 4; x ++) {
            for (int y = 0; y < 4; y ++) {
                completion += flipped[x][y];
            }
        }
        //completed game adds up to 32 (when the 4x4 flipped cards array is filled with 2s)
    } while (completion < 32);
    std::cout << "Congratulations, you've won!" << std::endl;
}

//prints the game board
void printBoard(int a[4][4], int b[4][4]) {
    std::cout << "-----------" << std::endl;
    for (int w = 0; w < 4; w ++) {
        std::cout << "| ";
        for (int h = 0; h < 4; h ++) {
          if (b[w][h] == 1) {
            std::cout << a[w][h] << " ";
          } else if (b[w][h] == 2) {
            std::cout << a[w][h] << " ";
          } else {
            std::cout << "* ";
          }
        }
        std::cout << "|";
        std::cout << std::endl;
    }
    std::cout << "-----------" << std::endl;
}

int shuffleBoard(int (&a)[4][4]) {
    for (int x = 0; x < 4; x ++) {
        for (int y = 0; y < 4; y ++) {
            srand(time(NULL));
            //randomly swap with another array position
            int r = y + (rand() % (4 - y));
            int temp = a[x][y]; a[x][y] = a[x][r]; a[x][r] = temp;
            //amplify randomness
            r = x + (rand() % (4 - x));
            temp = a[x][y]; a[x][y] = a[r][y]; a[r][y] = temp;
        }
    }
}
