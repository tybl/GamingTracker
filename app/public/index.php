<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <h1>Select active players</h1>
    <form action="edit_players.php" method="post">
      <input type="submit" value="Edit Players" />
    </form>
<?php
$db = new SQLite3('/var/www/html/database.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

$res = $db->query('SELECT name FROM players');

echo "<form method=\"post\" action=\"select_order.php\">";
while ($row = $res->fetchArray()) {
  echo "<input type=\"checkbox\" name=\"active_player[]\" value=\"{$row['name']}\" checked /><label>{$row['name']}</label><br/>";
}
echo "<input type=\"submit\" value=\"Next\" /></form>";
?>
  </body>
</html>
