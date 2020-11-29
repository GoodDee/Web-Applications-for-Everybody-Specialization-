<html>
<head>
<title>Guessing Game for Kantapong V.</title>
</head>
<body>
<h1>Welcome to my guessing game</h1>
<p>
<?php
  if (!array_key_exists('guess', $_GET)) {
    echo("Missing guess parameter");
  } elseif (empty($_GET['guess']) && !is_numeric($_GET['guess'])) {
    echo("Your guess is too short");
  } elseif (!is_numeric($_GET['guess'])) {
    echo("Your guess is not a number");
  } elseif ( $_GET['guess'] < 42 ) {
    echo("Your guess is too low");
  } elseif ( $_GET['guess'] > 42 ) {
    echo("Your guess is too high");
  } else {
    echo("Congratulations - You are right");
  }
?>
</p>
</body>
</html>
