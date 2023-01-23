import 'dart:io';

bool decide(int reg, int index) {
  int col = index + 1;
  return index == reg || index == reg + 1 || index == reg - 1;
}

void printScreen(List<List<String>> screen) {
  for (var i in screen) {
    print(i.join(""));
  }
}

class Point {
  var register = 1;
  var col = 0;
  var row = 0;
  int width;
  Point(this.width);
  void moveNext() {
    if ((col + 1) % width == 0) {
      row++;
      col = 0;
      print("Row change ${col}");
    } else {
      col++;
    }
  }
}

void main() {
  String path = 'questions/aoc10_input';
  File file = File(path);
  List<String> lines = file.readAsLinesSync();
  final arr = <int>[20, 60, 100, 140, 180, 220];

  int w = 40;
  int h = 6;
  var screen = List.generate(
      h, (i) => List.generate(w, (i) => '.', growable: false),
      growable: false);

  var p = Point(w);
  var values = [p.register];
  var index = 0;
  for (var line in lines) {
    print('[${p.row},${p.col}] EXEC ${line} REG ${p.register}');
    if (line.contains('addx')) {
      // Step 1
      screen[p.row][p.col] = decide(p.register, p.col) ? '#' : '.';

      // Step 2
      values.add(values.last);
      index += 1;
      p.moveNext();
      screen[p.row][p.col] = decide(p.register, p.col) ? '#' : '.';

      // Instruction execution complete
      var count = int.parse(line.replaceFirst('addx ', ''));
      var newVal = values.last + count;
      values.add(newVal);
      p.register = newVal;
      p.moveNext();
      index += 1;
    } else if (line == 'noop') {
      values.add(values.last);
      index += 1;
      screen[p.row][p.col] = decide(p.register, p.col) ? '#' : '.';
      p.moveNext();
    }

    print('[${p.row},${p.col}] DONE ${line} REG ${p.register} I ${index}');
  }

  // Part 1 of AOC10
  var sum = 0;
  for (var i in arr) {
    var val = values[i - 1] * i;
    print('${values[i - 1]} * ${i} = ${val}');
    sum += val;
  }
  print('${values.length} ${values.last} sum: ${sum}');

  printScreen(screen);
}
