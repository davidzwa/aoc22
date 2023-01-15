#include <stdio.h>
#include <stdbool.h>
#include <unistd.h>
#include <limits.h>

#define BUFSIZE 16000
#define LENGTH1 4
#define LENGTH2 14

int getMarker(char buf[], int maxSize, int length) {
    int foundUniqueChars = 0;
    size_t i;
    for (i = 0; i < maxSize - length; i++)
    {
        foundUniqueChars+=1;
        for (size_t j = 1; j < length; j++)
        {
            if (buf[i] == buf[i + j]) {
                foundUniqueChars = 0;
                break;
            }
            
        }

        if (foundUniqueChars == length)
        {
            return i;
            break;
        }
    }
}

// gcc aoc6.c -o aoc6.exe
void main()
{
    
   char cwd[PATH_MAX];
   getcwd(cwd, sizeof(cwd));
    printf("%s\n", cwd);
    char file[] = "C:/Users/david/Documents/Projects/aoc22/questions/aoc6_input";
    FILE *fp;
    fp = fopen(file, "r");

    if (fp == NULL) {
        perror("File opening error");
    }

    char buf[BUFSIZE];
    fgets(buf, BUFSIZE, fp);

    int count = 0;
    while (buf[count] != '\n')
    {
        count++;
    }

    printf("Count %d\n", count);

    size_t size = getMarker(buf, count, LENGTH1);
    size_t size2 = getMarker(buf, count, LENGTH2);

    printf("Result %d %d", size +1, size2+1);
    fclose(fp);
}