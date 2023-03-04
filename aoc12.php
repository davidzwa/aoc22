<?php

global $BR;
$BR = "<br/>";
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

  public function __compareTo($other): int
  {
    return $this->f - $other->f;
  }

  // https://wiki.php.net/rfc/object-comparison
  public function __equals($other): bool
  {
    return $this->x == $other->x && $this->y == $other->y;
  }

  public function __toString(
  )
  {
    return "({$this->x}, {$this->y}) <br/>&nbsp;f{$this->f} g{$this->g} h{$this->h} <br/>";
  }

  public function chr()
  {
    if ($this->val == 0) {
      return 'S';
    } else if ($this->val == 97) {
      return 'E';
    }

    return chr($this->val + 96);
  }

  public function debug(
  )
  {
    return "({$this->x}, {$this->y})";
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

function printHeap(\Ds\Deque $heap)
{
  global $BR;

  echo "HeapCnt " . $heap->count() . $BR;
  $index = 0;
  foreach ($heap as $el) {
    global $BR;
    echo "{$index}: " . $el->debug() . " {$el->f}" . $BR;

    $index++;
  }
}
function insertHeap(\Ds\Deque $heap, Coord $coord)
{
  $index = 0;
  while ($index < $heap->count()) {
    $not_found = $heap->get($index)->f < $coord->f;
    if (!$not_found)
      break;

    $index++;
  }

  $heap->insert($index, $coord);
}


$lines = file('./questions/aoc12_test_input');
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
$openSet = new \Ds\Deque();
$cameFrom = [];
$rows = count($m);
$cols = count($m[0]);
echo "Start{$start}";
echo "End{$end}";
// Add the start node with its priority f (=0)
insertHeap($openSet, $start);
printHeap($openSet);

$iter = 0;
while ($openSet->count() != 0 && $iter < 3) {
  echo "<br/>---- Iteration {$iter} (openSet {$openSet->count()}) ----<br/>";

  // Pop node with least f from the openSet list, call it bestNode, 
  $bestNode = $openSet->remove(0); // Drop the prio value, keep the elem (with f attrib.)
  // Push to closed set
  array_push($cameFrom, $bestNode);

  // Check end condition
  if ($bestNode == $end) {
    echo "End found:{$end}<br/>";
    break;
  }
  $currVal = $bestNode->val;

  // generate bestNode's top/left/right/bottom successors and set their parents to bestNode
  echo "Generating children<br/>";
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

  $bestf = $bestNode->f;
  $children_count = count($children);
  echo "> Looping ${children_count} children, current char: {$bestNode->chr()} (pos{$bestNode->debug()}, f={$bestf})<br/>";

  foreach ($children as $coords) {
    $x = $coords[0];
    $y = $coords[1];
    $val = $m[$x][$y];
    $child = new Coord($x, $y, $val);
    // echo "- Next child {$child->debug()} <br/>";

    // End condition (probably duplicate)
    if ($child->isGoal()) {
      echo "- Goal found {$child->debug()}";
      return;
    }

    // If node is equal to one from closed, skip
    if (in_array($child, $cameFrom)) {
      echo "- Skipping closed child {$child->debug()}";
      continue;
    }

    // (Re-)calculate node potential/heuristics
    $child->g = $bestNode->g + 1;
    // echo "Parent {$bestNode->debug()} g={$bestNode->g}<br/>";
    $xdiff = $child->x - $end->x;
    $ydiff = $child->y - $end->y;
    $child->h = abs($xdiff) + abs($ydiff);
    $child->f = $child->g + $child->h;

    echo "- Child {$child->debug()} (xd:{$xdiff} yd:{$ydiff}) computed f{$child->f} = g{$child->g} + h{$child->h}<br/>";
    // echo "OpenSet count {$openSet->count()} <br/>";

    // if child is in openList only add it when its better
    foreach ($openSet as $elem) {
      if ($child == $elem && $child->g > $elem->g) {
        echo "XX Skipping child <br/>";
        continue 2;
      }
    }

    echo "++ Adding child to openSet (f{$child->f}) <br/>";

    insertHeap($openSet, $child);
    printHeap($openSet);
  }

  $iter += 1;
}
?>