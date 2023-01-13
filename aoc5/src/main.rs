use std::fs::File;
extern crate queues;
use std::collections::VecDeque;
use std::io::{self, BufRead};
use std::path::Path;

fn main() {
    let path: &str = "../questions/aoc5_input";
    println!("Filepath {}", path);

    // Cols first, stack rows second
    let mut vec: Vec<VecDeque<char>> = Vec::new();
    let mut vec2: Vec<VecDeque<char>> = Vec::new();
    for _ in 1..=9 {
        vec.push(VecDeque::new());
        vec2.push(VecDeque::new());
    }

    let offset: isize = 1;
    if let Ok(lines) = read_lines(path) {
        let mut lines_vec: Vec<String> = Vec::new();
        let lines = &mut lines.into_iter();

        let mut iterator = &mut lines.take(8);
        for line in iterator.into_iter() {
            lines_vec.push(line.unwrap());
        }

        let mut instr_vec: Vec<String> = Vec::new();
        lines.skip(2).for_each(|item| {
            instr_vec.push(item.unwrap());
        });

        if lines_vec.len() == 0 {
            println!("Length of vec is 0");
            return;
        }

        for row in 0..=7 {
            let text = lines_vec.iter().nth(row).unwrap();
            for col in 0..=8 {
                let index = (offset + col * 4) as usize;
                let char = text.chars().nth(index).unwrap();
                if char != ' ' {
                    let queue = vec
                        .iter_mut()
                        .nth(col as usize)
                        .expect("Queue should be known");
                    queue.push_back(char);
                    let queue2 = vec2
                        .iter_mut()
                        .nth(col as usize)
                        .expect("Queue should be known");
                    queue2.push_back(char);
                }
            }
        }
        // for col in 0..=8 {
        //     let q = vec2.iter().nth(col).unwrap();
        //     print!("{:#?} ", &q);
        // }
        // println!("");
        // Parse instructions
        for line in instr_vec {
            let mut instr = line
                .replace("move ", "")
                .replace("from ", "")
                .replace("to ", "");
            let mut split: Vec<&str> = instr.split(' ').collect();
            let imove = split[0].parse::<usize>().unwrap();
            let ifrom = split[1].parse::<usize>().unwrap() - 1;
            let ito = split[2].parse::<usize>().unwrap() - 1;

            // let mut from = &mut vec[ifrom];
            // let mut to = &mut vec[ito];
            println!("Move {} from {} to {}", imove, ifrom + 1, ito + 1,);

            let mut stack :Vec<char>= Vec::new();
            for _ in 0..imove {
                let val = vec[ifrom].pop_front().expect("From was not poppable");
                vec[ito].push_front(val);

                stack.push(vec2[ifrom].pop_front().unwrap());
            }
            for i in 0..imove {
                vec2[ito].push_front(stack[imove-1-i]);
            }            

            println!("Completed move {} from {} to {}", imove, ifrom + 1, ito + 1,);
        }
    }

    let mut final_string = String::new();
    let mut final_string2 = String::new();
    for col in 0..=8 {
        let q = vec.iter().nth(col).unwrap();
        let q2 = vec2.iter().nth(col).unwrap();
        final_string.push(q.front().unwrap().to_ascii_uppercase());
        final_string2.push(q2.front().unwrap().to_ascii_uppercase());
    }
    println!("{} {}", final_string, final_string2);
}

// The output is wrapped in a Result to allow matching on errors
// Returns an Iterator to the Reader of the lines of the file.
fn read_lines<P>(filename: P) -> io::Result<io::Lines<io::BufReader<File>>>
where
    P: AsRef<Path>,
{
    let file = File::open(filename)?;
    Ok(io::BufReader::new(file).lines())
}
