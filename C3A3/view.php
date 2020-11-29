<?php
require_once "pdo.php";
session_start();
// Demand a GET parameter
if ( ! isset($_SESSION['who']) ) {
    die('Name parameter missing');
}
// Logout: Return to index page
if ( isset($_POST['logout']) ) {
    header('Location: logout.php');
    return;
}
if ( isset($_POST['new']) ) {
    header('Location: add.php');
    return;
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Kantapong Automobile Tracker</title>
</head>
<body>
<div class="container">
<?php
echo("<h1>Tracking Autos for ".$_SESSION['who']."</h1>");
?>
<h2>Automobiles</h2>
<?php
if (isset($_SESSION['failure'])){
  echo '<p style="color:red">'.$_SESSION['failure']."</p>\n";
  unset($_SESSION['failure']);
}
if (isset($_SESSION['success'])){
  echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
  unset($_SESSION['success']);
}

$stmt = $pdo->query("SELECT auto_id, make, model, year, mileage FROM autos");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($rows) == 0){
  echo "No rows found";
}
else{
  echo('<table border="1">'."\n");
  echo '<tr> <th>Make</th> <th>Model</th> <th>Year</th> <th>Mileage</th> <th> Action </th> </tr>';
  foreach ( $rows as $row ){
      echo "<tr><td>";
      echo (htmlentities($row['make']));
      echo "</td><td>";
      echo (htmlentities($row['model']));
      echo "</td><td>";
      echo (htmlentities($row['year']));
      echo "</td><td>";
      echo (htmlentities($row['mileage']));
      echo "</td><td>";
      echo '<a href="edit.php?auto_id='.$row['auto_id'].'">Edit</a> / ';
      echo '<a href="delete.php?auto_id='.$row['auto_id'].'">Delete</a>';
      echo("</td></tr>\n");
  }
  echo "</table>";
}
?>
<p>
<form method="POST">
<input type="submit" name="new" value="Add new Entry">
<input type="submit" name="logout" value="Logout">
</form>
</p>
</div>
</body>
</html>
