import scala.io.Source
import scala.util.control.Breaks._

class Monkey(
    val index: Int,
    var items: List[Int] = List[Int](), 
    var operator: Char = '?', 
    var operand: String | Null = null, 
    var divBy: Int | Null = null, 
    var trueTo: Int | Null = null, 
    var falseTo: Int | Null = null
):
end Monkey

val monkeyPrefix = "Monkey "
val startingItemsPrefix = "Starting items: "
val operationPrefix = "Operation: new = old "
val testPrefix = "Test: divisible by "
val ifTruePrefix = "If true: throw to monkey "
val ifFalsePrefix = "If false: throw to monkey "

// Main with breakable loop
@main def main() =
    val filename = "./questions/aoc11_input"
    var index: Int = 0
    var monkeys: List[Monkey] = List()
    var lm : Monkey | Null = null

    // Load the data
    breakable {
        for (line <- Source.fromFile(filename).getLines) {
            var line2 = line.trim()
            if (line2.contains(monkeyPrefix)) {
                var monkeyNo = line2.split(monkeyPrefix)(1).replace(":", "").toInt
                lm = Monkey(index = monkeys.length)
                monkeys = monkeys :+ lm
                println("New Monkey " + lm.index)
            } else if (line2.contains(startingItemsPrefix)) {
                var items = line2
                .replace(startingItemsPrefix, "")
                .split(", ")
                .toList
                .map((strint) => strint.toInt);
                // println("Items " + items)
                lm.items = lm.items.concat(items)
            } else if (line2.contains("Operation: ")) {
                var operation = line2.split(operationPrefix)(1)
                lm.operator = operation(0)
                var operand = operation.split("\\" + lm.operator + " ")(1)
                lm.operand = operand
                // println("Operation " + lm.operator + " " + lm.operand)
            } else if (line2.contains(testPrefix)) {
                var divBy = line2.split(testPrefix)(1).toInt
                // println("DivTest " + test)
                lm.divBy = divBy
            } else if (line2.contains(ifTruePrefix)) {
                lm.trueTo = line2.split(ifTruePrefix)(1).toInt
                // println("If true to " + lm.trueTo)
            } else if (line2.contains(ifFalsePrefix)) {
                lm.falseTo = line2.split(ifFalsePrefix)(1).toInt
                // println("If false to " + lm.falseTo)
            }
        }
    }

    println("Monkeys " + monkeys.length)
    for (m <- monkeys) {
        println(m.index + " " + m.items.length + " " + m.operator + " " + m.operand 
        + " " + m.divBy + " \n\tT" + m.trueTo + " F" + m.falseTo)
    }
