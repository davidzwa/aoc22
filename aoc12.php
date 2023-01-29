<?php

class Coord
{
  public int $x;
  public int $y;
  public float $f = INF;
  public float $g = INF;
  public float $h = INF;
  public int $val;

  public ?Coord $parent;
  function isStart()
  {
    return $this->val == -14;
  }
  function isGoal()
  {
    return $this->val == -28;
  }
  function __construct(int $x, int $y, int $val, ?Coord $parent = null)
  {
    $this->x = $x;
    $this->y = $y;
    $this->val = $val;
    $this->parent = $parent;
  }

  public function __toString(
  )
  {
    return "[{$this->x},{$this->y}] F{$this->f} <br/>";
  }
}

function printMatrix($matrix)
{
  $k = 0;
  foreach ($matrix as $row) {
    $rowLen = count($row);
    for ($i = 0; $i < $rowLen; $i++) {
      echo $matrix[$k][$i];
    }
    echo "\n";
    $k++;
  }
}

// https://en.wikipedia.org/wiki/A*_search_algorithm
// https://www.geeksforgeeks.org/a-search-algorithm/
$lines = file('./questions/aoc12_input');
$m = array();
// Coord element matrix with scores
$mv = array();
$start = null;
$end = null;

// Parse
$row = 0;
foreach ($lines as $line) {
  array_push($m, array());
  array_push($mv, array());
  $col = 0;
  foreach (str_split(trim($line)) as $char) {
    $val = ord($char) - 97;

    array_push($m[$row], $val);
    $new_elem = new Coord($row, $col, $val);
    array_push($mv[$row], $new_elem);
    if ($char == 'S') { // -14 == 'S' 
      $start = $new_elem;
      $start->f = 0;
    }
    if ($char == 'E') { // -28 == 'E'
      $end = $new_elem;
    }
    $col++;
  }

  $row += 1;
}

$open = new SplMinHeap();
$closed = new SplMinHeap();
$rows = count($mv);
$cols = count($mv[0]);
echo "S:{$start}\n";
echo "E:{$end}\n";
$open->insert([$start->f,$start]);
$open->insert([$end->f,$end]);

$iter = 0;
while ($open->count() != 0) {
  echo "Iter {$iter}\n";

  // Find node with least f on the open list, call it q
  $q = $open->extract()[1];
  $curr = $mv[$q->x][$q->y];
  $currH = $curr->f;

  // generate q's top/left/right/bottom successors and set their parents to q
  $row = $q->x;
  $col = $q->y;
  $succ = array();
  $x = $row;
  $y = $col + 1;
  if ($y < $cols && $currH >= $m[$x][$y]-1) {
    array_push($succ, [$x, $y]);
  }
  $x = $row;
  $y = $col - 1;
  if ($y >= 0 && $currH >= $m[$x][$y]-1) {
    array_push($succ, [$x, $y]);
  }
  $x = $row + 1;
  $y = $col;
  if ($x < $rows && $currH >= $m[$x][$y]-1) {
    array_push($succ, [$x, $y]);
  }
  $x = $row - 1;
  $y = $col;
  if ($x >= 0 && $currH >= $m[$x][$y]-1) {
    array_push($succ, [$x, $y]);
  }

  foreach ($succ as $elem) {
    print_r($succ);
    // End condition
    if ($elem->isGoal()) {
      echo "Goal found X:{$elem->x},Y:{$elem->y}";
      return;
    }
    else {
      $elem->g = $q->g + 1;
      // Manhattan heuristics
      $elem->h = abs($elem->x - $end->x) + abs($elem->y - $end->y);
      $elem->f = $elem->g + $elem->h;
    }

    // Find successor
    // $open
    // else {
    //   $elem.
    // }
  }
  break;
}

// printMatrix($m);
// $q = new SplQueue();
// $q[] = 1;
// $q[] = 2;
// $q[] = 3;
// foreach ($q as $elem) {
//     echo $elem . "\n";
// }
?>