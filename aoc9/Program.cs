using System;
using System.Collections;
using System.Collections.Generic;
using System.Text.RegularExpressions;
using System.IO;
using System.Threading.Tasks;
using System.Linq;
using aoc9;


string path = Directory.GetCurrentDirectory();
Console.WriteLine(path);
// https://regex101.com/c# 
Regex re = new Regex("^(U|D|L|R) ([0-9]{1,2})$");
var realFile = "../../../../questions/aoc9_test_input";
var lines = File.ReadAllLines(realFile);

var testFile = "../../../../questions/aoc9_test_input";
var lines2 = File.ReadAllLines(testFile);
var instructions = new List<Tuple<Dir, int>>();
Dictionary<string, int> coords = new Dictionary<string, int>();
var pos = new Tuple<int,int>(0,0);
var moves = lines2.Length;
var move = 0;
foreach (var line in lines2)
{
    move += 1;
    // https://learn.microsoft.com/en-us/dotnet/api/system.text.regularexpressions.match.groups?view=net-7.0
    GroupCollection matches = re.Matches(line)[0].Groups;
    Dir dir;
    Enum.TryParse(matches[1].Value, out dir);
    int cnt;
    Int32.TryParse(matches[2].Value, out cnt);
    
    var command = dir switch
    {
        Dir.U => (0,cnt),
        Dir.D => (0, -cnt),
        Dir.L => (-cnt, 0),
        Dir.R => (cnt, 0),
        _ => throw new ArgumentOutOfRangeException(nameof(dir), dir, null)
    };
    pos = new Tuple<int, int>(pos.Item1+ command.Item1, pos.Item2+ command.Item2);
    instructions.Add(new Tuple<Dir, int>(dir, cnt));
    Console.WriteLine("[{1}] HeadPos {0} {2}{3}{4}", pos, move, dir, cnt, command);

    var key = $"{pos.Item1}{pos.Item2}";
    if (!coords.ContainsKey(key)) {
        coords.Add(key, 0);
    }
    else
    {
        coords[key] += + 1;
    }
}

Console.WriteLine("HeadPos:{0}, moves:{1}, coords:{2}", pos, moves, coords.Count);