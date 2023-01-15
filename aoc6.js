const { readFileSync } = require("fs");
const file = readFileSync("questions/aoc6_input", { encoding: 'utf-8' }).trim();

const len = file.length;
let codeLen = 4;
for (let i of Array(len - codeLen).keys()) {
    const strSlice = file.slice(i, i + codeLen);

    let doubleChar = null;
    for (let char of strSlice) {
        if (strSlice.indexOf(char) !== strSlice.lastIndexOf(char)) {
            doubleChar = char;
        }
    }
    if (doubleChar === null) {
        console.log(i + codeLen, strSlice);
        break;
    }
}

let messageCodeLen = 14;
for (let i of Array(len - messageCodeLen).keys()) {
    const strSlice = file.slice(i, i + messageCodeLen);

    let doubleChar = null;
    for (let char of strSlice) {
        if (strSlice.indexOf(char) !== strSlice.lastIndexOf(char)) {
            doubleChar = char;
        }
    }
    if (doubleChar === null) {
        console.log(i + messageCodeLen, strSlice);
        break;
    }
}