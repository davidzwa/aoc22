import 'dart:io';

void main() {
  String path = 'questions/aoc10_input';
  File file = File(path);
  List<String> lines = file.readAsLinesSync();

  // 20, 60, 100, 140, 180, 220
  final arr = <int>[20, 60, 100, 140, 180, 220];
  var values = [1];
  // print('Hello, World! ${arr}');
  var index = 0;
  print('[${index}] Last ${values.last}');
  for (var line in lines) {
    if (line == 'noop') {
      values.add(values.last);
      index += 1;
    } else if (line.contains('addx')) {
      values.add(values.last);
      var count = int.parse(line.replaceFirst('addx ', ''));
      values.last += count;
      values.add(values.last);
      index += 2;
    }

    print('[${index}] Last ${values.last}');
  }

  var sum = 0;
  for (var i in arr) {
    var val = values[i - 2] * i;
    print('${values[i - 1]} * ${i} = ${val}');

    sum += val;
  }
  print('${values.length} ${values.last} sum: ${sum}');
}
