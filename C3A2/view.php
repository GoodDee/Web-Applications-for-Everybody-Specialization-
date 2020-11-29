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
    header('Location: autos.php');
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
$stmt = $pdo->query("SELECT make, year, mileage FROM autos");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<ul>";
foreach ( $rows as $row ){
    echo("<p>");
    echo("<li>");
    echo(htmlentities($row['year']). " ".htmlentities($row['make']). " / ".htmlentities($row['mileage']));
    echo("</li>");
    echo("</p>\n");
}
echo "</ul>";
?>
<p>
<form method="POST">
<input type="submit" name="new" value="Add new">
<input type="submit" name="logout" value="Logout">
</form>
</p>
</div>
</body>
</html>
