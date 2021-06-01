<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <form method="post">
      <label for="first_name">Add player:</label><br/>
      <input type="text" id="first_name" name="first_name" />
      <input type="submit" value="Add" />
    </form>
<?php
$db = new SQLite3('/var/www/html/database.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
$db->exec("CREATE TABLE IF NOT EXISTS players(id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, start_time TEXT, UNIQUE(name))");

if (isset($_POST['first_name'])) {
  $first_name = SQLite3::escapeString($_POST['first_name']);
  $db->exec("INSERT OR IGNORE INTO players(name) VALUES('$first_name')");
} else if (isset($_POST['remove_name'])) {
  $delete_name = SQLite3::escapeString($_POST['remove_name']);
  $db->exec("DELETE FROM players WHERE name == '$delete_name'");
}
$res = $db->query('SELECT name FROM players');
echo "<table>";
while ($row = $res->fetchArray()) {
  echo "<tr>
    <td>{$row['name']}</td>
    <td><form method=\"post\"><button name=\"edit_name\" value=\"{$row['name']}\">Edit</button></form></td>
    <td><form method=\"post\" onsubmit=\"return confirm('Are you sure you want to remove {$row['name']}?')\"><button name=\"remove_name\" value=\"{$row['name']}\">Remove</button></form></td>
  </tr>";
}
echo "</table>";
?>
    <form action="index.php" method="post">
      <input type="submit" value="Done" />
    </form>
  </body>
</html>
