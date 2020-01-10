#include <stdio.h>

/* Named function parameters. */
struct range {
    int from;
    int to;
    int step;
};

#define range(...) range((struct range){.from=1,.to=10,.step=1, __VA_ARGS__})
void (range)(struct range r) {
    int i = r.from;
    for(; i <= r.to; i += r.step) printf("%d ", i);
    puts("");
}

int main() {
    range();
    range(.from=2, .to=4);
    range(.step=2);
    return 0;
}
