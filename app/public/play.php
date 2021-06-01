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
if (isset($_POST['start_turn'])) {
  // Stop
  echo("<body onload=\"start_timer({$_POST['start_turn']})\">");
  echo("<form method=\"post\" onsubmit=\"append_remaining_time()\">");
  echo("<button name=\"stop_turn\" id=\"buttton\">Stop</button>");
  echo("</form>");
  echo("</body>");
} else if (isset($_POST['stop_turn'])) {
  // Start, Reset
  echo("<body>");
  echo("<form method=\"post\">");
  echo("<button name=\"start_turn\" value=\"{$_POST['stop_turn']}\">Start</button>");
  echo("<button name=\"reset_turn\">Reset</button>");
  echo("</form>");
  echo("</body>");
} else {
  // Start
  echo("<body>");
  echo("<form method=\"post\">");
  echo("<button name=\"start_turn\" value=\"5000\">Start</button>");
  echo("</form>");
  echo("<body>");
}
?>
</html>
