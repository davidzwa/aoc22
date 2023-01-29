import scala.io.Source
import scala.util.control.Breaks._
import scala.collection.mutable._

class Monkey(
    val index: Int,
    var items: List[Int] = List[Int](), 
    var operator: Char = '?', 
    var operand: String = null, 
    var divBy: Int = 0, 
    var trueTo: Int = 0, 
    var falseTo: Int = 0,
    var activity: Int = 0
):
end Monkey

val monkeyPrefix = "Monkey "
val startingItemsPrefix = "Starting items: "
val operationPrefix = "Operation: new = old "
val testPrefix = "Test: divisible by "
val ifTruePrefix = "If true: throw to monkey "
val ifFalsePrefix = "If false: throw to monkey "

def operate(operator: Char, lvalue: Int, rvalue_temp: String): Int = {
    var rvalue = 0
    if (rvalue_temp == "old") {
        rvalue = lvalue
    }
    else {
        rvalue = rvalue_temp.toInt
    }

    if (operator == '*'){
        return lvalue * rvalue
    }
    else if (operator == '+') {
        return lvalue + rvalue
    }
    else {
        throw Error("Error")
    }
}

def isDivBy(value: Int, by: Int): Boolean = {
    return value % by == 0
}

// Main with breakable loop
@main def main() =
    var filename = "./questions/aoc11_input"
    // filename = "./questions/aoc11_test_input"
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
                // println("New Monkey " + lm.index)
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
    breakable {
        for (i <- 1 to 20) {
            println("Round " + i )
            for (m <- monkeys) {
                println("I" + m.index  + " Len" + m.items.length)
                // println("I" + m.index + " Len" + m.items.length + " \n  old = old " + m.operator + " " + m.operand 
                // + " \n  D" + m.divBy + " T" + m.trueTo + " F" + m.falseTo)

                m.items.zipWithIndex.foreach{
                    case (item, index) => {
                        var res = operate(m.operator, item, m.operand)
                        var worry = Math.floor(res / 3).toInt
                        if (isDivBy(worry, m.divBy)) {
                            println("Item " + item + " DivBy " + worry + " by " + m.divBy + " = False")
                            var trueM = monkeys(m.trueTo)
                            trueM.items = trueM.items :+ worry
                            println("DivBy True " + trueM.index + " Items: " + trueM.items)
                        }
                        else {
                            println("Item " + item + " DivBy " + worry + " by " + m.divBy + " = False")
                            var falseM = monkeys(m.falseTo)
                            falseM.items = falseM.items :+ worry
                            println("DivBy False " + falseM.index + " Items: " + falseM.items)
                        }

                        m.activity += 1
                    }
                }
                m.items = List[Int]()
            }
        }

        println("Result")
        for (m <- monkeys) {
            println("M" + m.index + " A " + m.activity + " Items" + m.items)
        }

        var maxIndex = monkeys.map(m => m.activity).zipWithIndex.maxBy(_._1)
        var max1 = maxIndex._1
        monkeys = monkeys.filterNot(m => m.index == maxIndex._2)
        maxIndex = monkeys.map(m => m.activity).zipWithIndex.maxBy(_._1)
        var max2 = maxIndex._1
        println(max1 * max2)
    }
