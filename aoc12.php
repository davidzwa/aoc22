<?php
$lines = file('./questions/aoc12_input');
$m = array();
$mc = array();

$open = array();
$closed = array();
$start = array(0,0);
$end = array(0,0);

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

// Parse
$row = 0;
foreach ($lines as $line) {
  array_push($m, array());
  array_push($mc, array());
  $col = 0;
  foreach (str_split(trim($line)) as $char) {
    $val = ord($char) - 97;
    if ($char == 'S') { // -14 == 'S' 
      $val = 'S';
      $start = array($row, $col);
    }
    if ($char == 'E') { // -28 == 'E'
      $val = 'E';
      $end = array($row, $col);
    }
    
    array_push($m[$row], $val);
    array_push($mc[$row], $char);
    $col++;
  }

  $row += 1;
}

print_r($start);
print_r($end);
// printMatrix($m);
// $q = new SplQueue();
// $q[] = 1;
// $q[] = 2;
// $q[] = 3;
// foreach ($q as $elem) {
//     echo $elem . "\n";
// }
?>