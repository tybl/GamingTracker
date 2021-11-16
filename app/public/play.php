<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="style.css" />
    <script>

class Timer {
  constructor(milliseconds) {
    this.period = milliseconds;
    this.remain = milliseconds;
    this.is_running = false;
  }
  start(callback) {
    this.start_time = (new Date()).getTime();
    this.timer_id = setTimeout(callback, this.remain);
    this.is_running = true;
  }
  pause() {
    if (this.is_running) {
      this.remain = this.remaining();
      clearTimeout(this.timer_id);
      this.is_running = false;
    }
  }
  remaining() {
    return this.remain - ((new Date()).getTime() - this.start_time);
  }
  reset() {
    clearTimeout(this.timer_id);
    this.remain = this.period;
    this.is_running = false;
  }
}

function start_timer(timeout) {
  timer = new Timer(timeout);
  timer.start(timer_callback);
}
function timer_callback() {
  alert('Time\'s up');
  window.location.replace("play.php");
}
function append_remaining_time() {
  document.getElementById('buttton').value = timer.remaining();
}
    </script>
  </head>
  <body>
<?php
var_dump($_POST);
$now = date("Y-m-d H:i:s");
$db = new SQLite3('/var/www/html/database.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
$db->exec("CREATE TABLE IF NOT EXISTS timers(id INTEGER PRIMARY KEY AUTOINCREMENT, remain_time INTEGER, start_time TEXT)");
if (isset($_POST['start_turn'])) {
  $remaining = $_POST['start_turn'];
  $db->exec("INSERT OR REPLACE INTO timers (id, remain_time, start_time) VALUES (1, $remaining, $now)");
  // Stop
  echo("<body onload=\"start_timer({$remaining})\">");
  echo("<form method=\"post\" onsubmit=\"append_remaining_time()\">");
  echo("<button name=\"stop_turn\" id=\"buttton\">Stop</button>");
  echo("</form>");
  echo("</body>");
} else if (isset($_POST['stop_turn'])) {
  $remaining = $_POST['stop_turn'];
  $db->exec("INSERT OR REPLACE INTO timers (id, remain_time, start_time) VALUES (1, $remaining, NULL)");
  // Start, Reset
  echo("<body>");
  echo("<form method=\"post\">");
  echo("<button name=\"start_turn\" value=\"{$remaining}\">Start</button>");
  echo("<button name=\"reset_turn\">Reset</button>");
  echo("</form>");
  echo("</body>");
} else if (isset($_POST['reset_turn'])) {
  // Start
  echo("<body>");
  echo("<form method=\"post\">");
  echo("<button name=\"start_turn\" value=\"5000\">Start</button>");
  echo("</form>");
  echo("<body>");
}
?>
</html>
