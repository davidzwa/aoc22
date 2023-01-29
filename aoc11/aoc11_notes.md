Monkey0 inspects level 79
worry level multiplied by 19 = 1501
Divisable check 23 => Prime number (both test and input are prime numbers)

With the operation the outcome will exceed Int64 most likely, but divisable check will be able to work with reduced value.

N2 = N1 * 19
N2 % 23 == 0

QUESTION
(N1 * 19) % 23 == 0 


# How to reduce
(15 * 19) % 23 == 0
5 * 9 = 45 => 22 => no?

(79 * 19) % 23 = 1501 % 23
1501 % 23 = 6
- last digit is 1
9 * 19 = 171
- last digit is 1
9 * 9 = 81
- last digit is 1

Test: 17 * 13 * 19 * 23
Real: 5 * 17 * 2 * 7 * 3 * 11 * 13 * 19
Real: 2 * 3 * 5 * 7 * 11 * 13 * 17 * 19 = 9699690

# Reddit
I have two different ideas for hints and don't know which is better, but looking at both will probably give it away. For whichever you go for, read one at a time, and only continue if you're still very stuck.

Here's the path that's a bit trickier:

    For any integer n which is divisible by P, n-kP is also divisible by P (and same for if it's not). (Also, if n+S is divisible by P, so is (n-kP)+S.)

    Consider a particular value of k. For instance, maybe you pick some Q that you want to know whether n is divisible by or not.

    So, now you know that if n is divisible by 2, so is n-6k. Also, if n is divisible by 3, so is n-6k.

    Surely, if you have it working for two numbers, it's not that hard to figure out how to extend it to eight.

    You can do this whole repeated subtraction by a number thing by just taking the modulo.

Here's the path that's a (lot) easier:

    What is the last digit of (87*19*17+45)^2*7)+19? (no calculators allowed). Is this number divisible by 2? How about 5? (The other friend I sent this to couldn't even do that much, so here's a hint for this hint: If A is a number with 3 as its last digit and B is a number with 7 as its last digit, what is the last digit of A*B? How about for A*B*5635353456364574?)

    Obviously, there's nothing special about taking the last digit. You can go modulo of any other number, and a similar property will hold.

    If your modulo of choice is 210, then you'll be able to easily tell whether a number is divisible by any of 2, 3, 5, or 7.
