<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <h1>Player order</h1>
<?php

class Permutation {
  public $order;

  public function __construct($order) {
    $this->order = $order;
  }

  public function weight($weights) {
    $length = count($this->order);
    $result = 0;
    for ($i = 0; $i < $length; $i++) {
      $result += $weights[$this->order[$i]][$i];
    }
    return $result;
  }
}

function get_db_handle() {
  $db = new SQLite3('/var/www/html/database.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
  $db->exec("CREATE TABLE IF NOT EXISTS \"playorder\" (\"player_id\" INTEGER NOT NULL, \"position\" INTEGER NOT NULL, \"count\" INTEGER NOT NULL, UNIQUE(\"player_id\", \"position\"))");
  return $db;
}

// TODO: This function operates on the new order and the database to update the database. However there is no need to query the database since the weights variable already contains the 
// contents of the database. The database query should be eliminated in favor or using the weights variable.
// TODO: Investigate Sqlite's UPSERT functionality instead of separate UPDATE and INSERT statements
function save_order_to_database($db, $player_order) {
  for ($i = 0; $i < count($player_order); $i++) {
    $name = $player_order[$i];
    $res = $db->query("SELECT player_id, count FROM \"playorder\" INNER JOIN players ON playorder.player_id = players.id WHERE players.name = \"$name\" AND \"position\" = $i");
    if ($row = $res->fetchArray()) {
      $new_count = $row['count'] + 1;
      $player_id = $row['player_id'];
      $db->exec("UPDATE \"playorder\" SET \"count\" = $new_count WHERE player_id = $player_id AND position = $i");
    } else {
      $db->exec("INSERT INTO \"playorder\" (\"player_id\", \"position\", \"count\") SELECT id, $i, 1 FROM players WHERE name = \"$name\"");
    }
  }
}

function get_weights($db, $player_list) {
  $length = count($player_list);
  $weights = array();
  foreach ($player_list as $player) {
    $weights[$player] = array();
    for ($i = 0; $i < $length; $i++) {
      $weights[$player][] = 0;
    }
  }
  $res = $db->query("SELECT name, position, count FROM \"playorder\" INNER JOIN \"players\" ON playorder.player_id = players.id WHERE \"position\" <= $length");
  while ($row = $res->fetchArray()) {
    $weights[$row['name']][$row['position']] = $row['count'];
  }
  return $weights;
}

function get_permutations($order) {
  $length = count($order);
  $permutations = array();
  permute($permutations, $order, 0, $length - 1);
  return $permutations;
}

function permute(&$result, $order, $min_index, $max_index) {
  if ($min_index == $max_index) {
    $result[] = new Permutation($order);
  } else {
    permute($result, $order, $min_index + 1, $max_index);
    for ($i = $min_index + 1; $i <= $max_index; $i++) {
      swap($order[$min_index], $order[$i]);
      permute($result, $order, $min_index + 1, $max_index);
      swap($order[$min_index], $order[$i]);
    }
  }
}

function swap(&$a, &$b) {
  $temp = $a;
  $a = $b;
  $b = $temp;
}

function min_weight($permutations, $weights) {
  $min_weight = PHP_INT_MAX;
  foreach ($permutations as $row) {
    $weight = $row->weight($weights);
    if ($weight < $min_weight) {
      $min_weight = $weight;
    }
  }
  return $min_weight;
}

function print_weights($weights) {
  echo "<br/><table>";
  foreach ($weights as $row) {
    echo "<tr>";
    foreach ($row as $col) {
      echo "<td>{$col}</td>";
    }
    echo "</tr>";
  }
  echo "</table>";
}

function filter_permutations($permutations, $weights) {
  $available_orders = array();
  $min_weight = min_weight($permutations, $weights);
  foreach ($permutations as $row) {
    if ($row->weight($weights) == $min_weight) {
      $available_orders[] = $row->order;
    }
  }
  return $available_orders;
}

$players = $_POST['active_player'];
$db = get_db_handle();
$weights = get_weights($db, $players);
$permutations = filter_permutations(get_permutations($players), $weights);

print_weights($weights);

$index = random_int(0, count($permutations) - 1);
$player_order = $permutations[$index];



echo "<br/>";
echo "<table>";
$min_weight = PHP_INT_MAX;
$min_order = $players;
foreach ($permutations as $row) {
  /*$weight = $row->weight($weights);
  if ($weight <= $min_weight) {
    $min_weight = $weight;
    $min_order = $row;
  }
   */
  echo "<tr>";
  foreach ($row as $col) {
    echo "<td>{$col}</td>";
  }
  //echo "<td>{$weight}</td>";
  echo "</tr>";
}
echo "</table>";

echo "<p>Selected order:</p>";
var_dump($player_order);

save_order_to_database($db, $player_order);
?>
  </body>
</html>
