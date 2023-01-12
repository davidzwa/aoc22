import java.io.FileReader;
import java.io.IOException;
import java.nio.file.Path;
import java.util.List;
import java.nio.file.Files;

public class aoc4 {
    public static void p(String str) {
        System.out.println(str);
    }

    public static void main(String[] args) {
        System.out.println("AoC4");
        Path path = Path.of("questions/aoc4_input");
        int count = 0;
        int count2 = 0;
        try {
            List<String> lines = Files.readAllLines(path);
            for (String line : lines) {
                var spl = line.split(",", 0);
                var elf1 = spl[0].split("-", 0);
                var e1S = Integer.parseInt(elf1[0]);
                var e1E = Integer.parseInt(elf1[1]);
                var elf2 = spl[1].split("-", 0);
                var e2S = Integer.parseInt(elf2[0]);
                var e2E = Integer.parseInt(elf2[1]);


                var overlap1S = e1S <= e2S;
                var overlap1E = e1E >= e2E;
                var overlap2S = e1S >= e2S;
                var overlap2E = e1E <= e2E;
                if(overlap1S &&overlap1E || overlap2S && overlap2E) {
                    count++;
                }
                
                // ...1...1
                // ....2..2
                // .....1.1
                // ....2.2.
                if (overlap1S && e1E >= e2S || overlap1E && e1S <= e2E || overlap2S && e2E >= e1S || overlap2E && e1S >= e2S) {
                    count2++;
                }
                
            }
            p(String.valueOf(count)); // 651 is correct
            p(String.valueOf(count2)); // 825 is too low, 956 is correct
        } catch (IOException e) {

        }
    }
}