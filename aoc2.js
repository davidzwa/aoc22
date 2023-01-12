const { readFileSync } = require('fs');

const file = readFileSync('aoc2_input', { encoding: 'utf8' });
const lines = file
  .split(/\n/) // Split the files by newline 
  // Alternatively /\r?\n/ allows carriage return \r to happen 0 or 1 time
  // Skip the last lines which is purely empty newline
  .filter(l => !!l.trim().length);

// A, X Rock
// B, Y Paper
// C, Z Scissors 
const rock = 1;
const paper = 2;
const scissors = 3;
const baseScores = {
  'X': rock,
  'Y': paper,
  'Z': scissors
}
// win 6, draw 3, loss 0
const win = 6;
const draw = 3;
const loss = 0;
const scores = {
  'AX': draw,
  'AY': win,
  'AZ': loss,
  'BX': loss,
  'BY': draw,
  'BZ': win,
  'CX': win,
  'CY': loss,
  'CZ': draw,
};
function calcScore(i, r) {
  const key = i + r;
  return baseScores[r] + scores[key];
}

let score1 = 0;
// Splitting the line in characters
for ([i, _, r] of lines) {
  const score = calcScore(i, r);
  score1 += score;
}
console.log(score1); // 11841 is correct


// X: loss, Y: draw, Z: win
const answerMap = {
  'AX': scissors + loss, // loss against rock
  'AY': rock + draw, // Swapped around this one!
  'AZ': paper + win, // And this one!
  'BX': rock + loss,
  'BY': paper + draw,
  'BZ': scissors + win,
  'CX': paper + loss,
  'CY': scissors + draw,
  'CZ': rock + win,
}
function calcScore2(i, r) {
  const key = i + r;
  return answerMap[key];
}

let score2 = 0;
for ([i, _, r] of lines) {
  const score = calcScore2(i, r);
  score2 += score;
}

// 13066 is too high
console.log(score2);