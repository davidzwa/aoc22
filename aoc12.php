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

  // https://wiki.php.net/rfc/object-comparison
  public function __equals($other): bool
  {
    return $this->x == $other->x && $this->y == $other->y;
  }

  public function __toString(
  )
  {
    return "[{$this->x},{$this->y}] F:{$this->f} G:{$this->g} H:{$this->h} <br/>";
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

$lines = file('./questions/aoc12_input');
// Char values offset by -97 (to make them zero based)
$m = array();
$start = null;
$end = null;

// Parse the file
$row = 0;
foreach ($lines as $line) {
  array_push($m, array());
  $col = 0;
  foreach (str_split(trim($line)) as $char) {
    $val = ord($char) - 96;
    if ($char == 'S') {
      $val = 0;
    }
    if ($char == 'E') {
      $val = 27;
    }

    // Add height value to matrix
    array_push($m[$row], $val);

    // Save reference to start or end positions
    if ($char == 'S' || $char == 'E') {
      $new_elem = new Coord($row, $col, $val);
      $new_elem->f = 0; // f is set from INF to 0 for start/end
      $new_elem->g = 0;
      $new_elem->h = 0;
      if ($char == 'S') { // -14 == 'S' 
        $start = $new_elem;
      }
      if ($char == 'E') { // -28 == 'E'
        $end = $new_elem; // f is INF
      }
    }
    $col++;
  }
  $row += 1;
}

// https://en.wikipedia.org/wiki/A*_search_algorithm
// https://www.geeksforgeeks.org/a-search-algorithm/
// Prepare the A-star algorithm
$openSet = new SplMinHeap();
$cameFrom = [];
$rows = count($m);
$cols = count($m[0]);
echo "S:{$start}";
echo "E:{$end}";
// Add the start node with its priority f (=0)
$openSet->insert([$start->f, $start]);

$iter = 0;
while ($openSet->count() != 0 && $iter < 10) {
  echo "Iter {$iter}<br/><br/>";

  // Pop node with least f from the openSet list, call it bestNode, 
  $bestNode = $openSet->extract()[1]; // Drop the prio value, keep the elem (with f attrib.)
  // Push to closed set
  array_push($cameFrom, $bestNode);

  // Check end condition
  if ($bestNode == $end) {
    echo "End found:{$end}<br/><br/>";
    break;
  }
  $currVal = $bestNode->val;

  // generate bestNode's top/left/right/bottom successors and set their parents to bestNode
  $row = $bestNode->x;
  $col = $bestNode->y;
  $children = array();
  $x = $row;
  $y = $col + 1;
  // Determine walkable children
  if ($y < $cols && $currVal >= $m[$x][$y] - 1) {
    array_push($children, [$x, $y]);
  }
  $x = $row;
  $y = $col - 1;
  if ($y >= 0 && $currVal >= $m[$x][$y] - 1) {
    array_push($children, [$x, $y]);
  }
  $x = $row + 1;
  $y = $col;
  if ($x < $rows && $currVal >= $m[$x][$y] - 1) {
    array_push($children, [$x, $y]);
  }
  $x = $row - 1;
  $y = $col;
  if ($x >= 0 && $currVal >= $m[$x][$y] - 1) {
    array_push($children, [$x, $y]);
  }

  $valchr = chr($currVal + 96);
  $children_count = count($children);
  echo "Looping children, curr val: {$valchr} count ${children_count}<br/>";

  foreach ($children as $coords) {
    $x = $coords[0];
    $y = $coords[1];
    $val = $m[$x][$y];
    $child = new Coord($x, $y, $val);

    // End condition (probably duplicate)
    if ($child->isGoal()) {
      echo "Goal found X:{$child->x},Y:{$child->y}";
      return;
    }

    // If node is equal to one from closed, skip
    if (in_array($child, $cameFrom)) {
      echo "Skipping closed child";
      continue;
    }

    // (Re-)calculate node potential/heuristics
    $child->g = $bestNode->g + 1;
    echo "best g {$bestNode->g}<br/>";
    echo "new g {$child->g}<br/>";
    // Manhattan heuristics
    $child->h = abs($child->x - $end->x) + abs($child->y - $end->y);
    $child->f = $child->g + $child->h;

    echo "child computed <br/>";
    echo $child;

    // if child is in openList only add it when its better
    foreach ($openSet as $elem) {
      if ($child == $elem && $child->g > $elem->g) {
        echo "Skipping child <br/>";
        continue 2;
      }
    }

    $openSet->insert([$child->f, $child]);
  }

  $iter += 1;
}

// printMatrix($m);
// $bestNode = new SplQueue();
// $bestNode[] = 1;
// $bestNode[] = 2;
// $bestNode[] = 3;
// foreach ($bestNode as $elem) {
//     echo $elem . "\n";
// }
?>