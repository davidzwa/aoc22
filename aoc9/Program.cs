﻿using System.Text.RegularExpressions;
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


var path = Directory.GetCurrentDirectory();
Console.WriteLine(path);
// https://regex101.com/c# 
var re = new Regex("^(U|D|L|R) ([0-9]{1,2})$");
var realFile = "../../../../questions/aoc9_input";
var lines2 = File.ReadAllLines(realFile);

// var testFile = "../../../../questions/aoc9_test_input";
// var lines2 = File.ReadAllLines(testFile);
var instructions = new List<Tuple<Dir, int>>();
var coords = new Dictionary<string, int>();
var moves = lines2.Length;
var subMove = 0;
var minX = 0;
var maxX = 0;
var minY = 0;
var maxY = 0;
var refPos = (0, 0);
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

coords.Add($"{startPos.Item1},{startPos.Item2}", 1);
grid[startPos.Item1, startPos.Item2] = 1;
var posList = Enumerable.Range(0, 9).Select(_ => (startPos.Item1, startPos.Item2)).ToList();
var headPos = posList[0];
var tailPos = posList[1];
Console.WriteLine("Start {0}", headPos);

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
        var previousHeadPos = (headPos.Item1, headPos.Item2);
        headPos = (headPos.Item1 + command.Item1, headPos.Item2 + command.Item2);

        // Only on second move and beyond will the tail follow
        if (subMove > 0)
        {
            // if headpos is at diagonal difference (X and Y diff 1), dont move tail
            var xDiff = int.Abs(headPos.Item1 - tailPos.Item1);
            var yDiff = int.Abs(headPos.Item2 - tailPos.Item2);
            if (xDiff > 2 || yDiff > 2)
            {
                Console.WriteLine("HeadPos {0} {2}{1}", headPos, dir, cnt);
                Console.WriteLine("TailPos {0} {2}{1}", tailPos, dir, cnt);
                throw new InvalidOperationException($"Tail lags behind more than 2 cells {xDiff} {yDiff}");
            }

            if ((xDiff == 0 && yDiff > 1) || (yDiff == 0 && xDiff > 1) || (xDiff > 1 && yDiff == 1) ||
                (xDiff == 1 && yDiff > 1))
            {
                tailPos = previousHeadPos;
                // Console.WriteLine("[{1}] New TailPos {0} {2}{3}", tailPos, move, dir, cnt);

                var key = $"{tailPos.Item1},{tailPos.Item2}";
                if (!coords.ContainsKey(key))
                    coords.Add(key, 1);
                else
                    coords[key] += +1;

                grid[tailPos.Item1, tailPos.Item2] = coords[key];
            }
        }

        subMove += 1;
    }

    // Console.WriteLine("[{1}] HeadPos {0} {2}{3}", headPos, move, dir, cnt);
}

// PrintMatrix(grid, tail: tailPos, head:headPos, start: startPos);
Console.WriteLine("HeadPos:{0}, moves:{1}, coords:{2}", headPos, moves, coords.Count);