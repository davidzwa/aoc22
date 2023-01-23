using System.Diagnostics;
using System.Text.RegularExpressions;
using aoc9;

void PrintMatrix(int[,] matrix, ValueTuple<int, int> start, ValueTuple<int, int> tail, ValueTuple<int, int> head)
{
    for (var j = matrix.GetLength(1) - 1; j >= 0; j--)
    {
        for (var i = 0; i < matrix.GetLength(0); i++)
        {
            var symbol = matrix[i, j] > 0 ? matrix[i, j] > 1 ? "#" : "#" : ".";
            if (head.Item1 == i && head.Item2 == j)
                symbol = "H";
            else if (tail.Item1 == i && tail.Item2 == j)
                symbol = "T";
            else if (start.Item1 == i && start.Item2 == j) symbol = "s";

            Console.Write("{0} ", symbol);
        }

        Console.WriteLine();
    }
}

(Dir, int) ParseInstruction(Regex re, string instruction)
{
    // https://learn.microsoft.com/en-us/dotnet/api/system.text.regularexpressions.match.groups?view=net-7.0
    var matches = re.Matches(instruction)[0].Groups;
    Dir dir;
    Enum.TryParse(matches[1].Value, out dir);
    int cnt;
    int.TryParse(matches[2].Value, out cnt);

    return (dir, cnt);
}

(bool, (int, int)) DetermineAbsoluteMove((int, int) posHeadNext, (int, int) posTailCurrent)
{
    var xN = posHeadNext.Item1 - posTailCurrent.Item1;
    var xDiff = int.Abs(xN);
    var yN = posHeadNext.Item2 - posTailCurrent.Item2;
    var yDiff = int.Abs(yN);
    if (xDiff > 2 || yDiff > 2)
    {
        Console.WriteLine("HeadPos {0}", posHeadNext);
        Console.WriteLine("TailPos {0}", posTailCurrent);
        throw new InvalidOperationException($"Tail part lags behind more than 2 cells {xDiff} {yDiff}");
    }

    // x1y0,x1y1,x0y1,x0y0 => NOOP
    if ((xDiff == 0 && yDiff > 1) || (xDiff > 1 && yDiff == 0))
    {
        // Simple straight move with delta of 2
        if (yDiff > 1)
        {
            var sign = yN / yDiff;
            return (true, (posTailCurrent.Item1, posTailCurrent.Item2 + sign));
        }
        else
        {
            var sign = xN / xDiff;
            return (true, (posTailCurrent.Item1 + sign, posTailCurrent.Item2));
        }
    }
    else if (xDiff > 1 && yDiff == 1)
    {
        var sign = xN / xDiff;
        return (true, (posTailCurrent.Item1 + sign, posTailCurrent.Item2 + yN));
    }
    else if (xDiff == 1 && yDiff > 1)
    {
        var sign = yN / yDiff;
        return (true, (posTailCurrent.Item1 + xN, posTailCurrent.Item2 + sign));
    }
    else if (xDiff == 2 && yDiff == 2)
    {
        var signY = yN / yDiff;
        var signX = xN / xDiff;
        return (true, (posTailCurrent.Item1 + signX, posTailCurrent.Item2 + signY));
    }
    else
    {
        return (false, posTailCurrent);
    }
}

void AssertMove((int, int) head, (int, int) tail, bool willMoveCmp, (int, int) moveCmp)
{
    (bool willMove, (int, int) move) = DetermineAbsoluteMove(head, tail);
    Console.WriteLine($"Compare {willMove} ?= {willMoveCmp}, {move} ?= {moveCmp}");
    Debug.Assert(willMove == willMoveCmp, $"{willMove} != {willMoveCmp}");
    Debug.Assert(move.CompareTo(moveCmp) == 0, $"{move} != {moveCmp}");
    Console.WriteLine($"Compare {willMove} == {willMoveCmp}, {move} (result) == {moveCmp} (wanted)");
}


/*
 * ...  ...
 * .H.  .H.
 * T..  T..
 */
AssertMove((1, 1), (0, 0), false, (0, 0));
/*
 * .H.  .H.
 * ...  .T.
 * T..  ...
 */
AssertMove((1, 2), (0, 0), true, (1, 1));
/*
 * ..H  ..H
 * ...  .T.
 * T..  ...
 */
AssertMove((2, 2), (0, 0), true, (1, 1));
/*
 * ...  ...
 * ..T  ...
 * H..  HT.
 */
AssertMove((0, 0), (2, 1), true, (1, 0));
/*
 * ...  ...
 * H.T  HT.
 * ...  ...
 */
AssertMove((0, 1), (2, 1), true, (1, 1));
/*
 * ...  ...
 * HT.  HT.
 * ...  ...
 */
AssertMove((0, 1), (1, 1), false, (1, 1));

var path = Directory.GetCurrentDirectory();
Console.WriteLine(path);
// https://regex101.com/c# 
var re = new Regex("^(U|D|L|R) ([0-9]{1,2})$");
var realFile = "../../../../questions/aoc9_input";
var lines2 = File.ReadAllLines(realFile);
var testFile = "../../../../questions/aoc9_test_input";
// var lines2 = File.ReadAllLines(testFile);

// Algorithm containers
var instructions = new List<Tuple<Dir, int>>();
var coords = new Dictionary<string, int>();
var moves = lines2.Length;

// Grid rect limit tracker variables
var minX = 0;
var maxX = 0;
var minY = 0;
var maxY = 0;
var refPos = (0, 0);

// Parse instructions, cache them and determine grid size for allocating 
foreach (var line in lines2)
{
    var (dir, cnt) = ParseInstruction(re, line);
    var command = dir switch
    {
        Dir.U => (0, cnt),
        Dir.D => (0, -cnt),
        Dir.L => (-cnt, 0),
        Dir.R => (cnt, 0),
        _ => throw new ArgumentOutOfRangeException(nameof(dir), dir, null)
    };
    refPos = (refPos.Item1 + command.Item1, refPos.Item2 + command.Item2);
    minX = int.Min(refPos.Item1, minX);
    maxX = int.Max(refPos.Item1, maxX);
    minY = int.Min(refPos.Item2, minY);
    maxY = int.Max(refPos.Item2, maxY);
    instructions.Add(new Tuple<Dir, int>(dir, cnt));
}

var width = maxX - minX + 1;
var height = maxY - minY + 1;
Console.WriteLine("Determined required matrix MinX{0} MaxX{1} MinY{2} MaxY{3}", minX, maxX, minY, maxY);

var grid = new int[width, height];
var startPos = (int.Abs(minX), int.Abs(minY));
Console.WriteLine("Grid size W{0} H{1} with start point (0, 0) offset by {2}", width, height, startPos);

// Track the start
coords.Add($"{startPos.Item1},{startPos.Item2}", 1);
grid[startPos.Item1, startPos.Item2] = 1;

// Hold the positions of the snake

const int pieces = 10;
var posList = Enumerable.Range(0, pieces).Select(_ => (startPos.Item1, startPos.Item2)).ToList();
Console.WriteLine("Start {0}", posList[0]);

foreach (var (dir, cnt) in instructions)
{
    foreach (var _ in Enumerable.Range(1, cnt))
    {
        // Make one move with the head
        var command = dir switch
        {
            Dir.U => (0, 1),
            Dir.D => (0, -1),
            Dir.L => (-1, 0),
            Dir.R => (1, 0),
            _ => throw new ArgumentOutOfRangeException(nameof(dir), dir, null)
        };

        // Save the new head position to trigger the snake to move piece by piece
        // var previousHeadPos = (posList.First().Item1, posList.First().Item2);
        posList[0] = (posList.First().Item1 + command.Item1, posList.First().Item2 + command.Item2);
        Console.WriteLine("Head Move [{0}] {1}", 0, posList[0]);

        foreach (int i in Enumerable.Range(1, pieces - 1))
        {
            // if headpos is at diagonal difference (X and Y diff 1), dont move tail
            (bool hasMoved, (int, int) newTailPos) = DetermineAbsoluteMove(posList[i - 1], posList[i]);
            if (hasMoved)
            {
                // previousHeadPos = (posList[i].Item1, posList[i].Item2);

                // Console.WriteLine("RelMove from {0} to {1}", posList[i], newTailPos);
                var relTailPos = posList[i] = newTailPos;
                if (i == pieces - 1)
                {
                    // Register the visit count in hash map form (for quick counting))
                    var key = $"{relTailPos.Item1},{relTailPos.Item2}";
                    if (!coords.ContainsKey(key))
                        coords.Add(key, 1);
                    else
                        coords[key] += +1;

                    // Mark the grid with the hashed value
                    grid[relTailPos.Item1, relTailPos.Item2] = coords[key];
                }

                Console.Write("Move [{0}] {1}", i, relTailPos);
            }
            else
            {
                // previousHeadPos = (posList[i].Item1, posList[i].Item2);
                Console.Write("No [{0}] {1}", i, posList[i]);
            }

            // Save the tail as relative head for the next iteration
            // Console.WriteLine(" PrevHead {0}", previousHeadPos);
        }
    }

    // Console.WriteLine("[{1}] HeadPos {0} {2}{3}", headPos, move, dir, cnt);
}

// PrintMatrix(grid, tail: tailPos, head:headPos, start: startPos);
Console.WriteLine("HeadPos:{0}, moves:{1}, coords:{2}", posList.First(), moves, coords.Count);