<?php
require_once "pdo.php";

// Demand a GET parameter
if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1  ) {
    die('Name parameter missing');
}
// Logout: Return to index page
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}
$failure = false;
$success = false;
if ( isset($_POST['make']) && isset($_POST['year'])
     && isset($_POST['mileage'])){
       if (strlen($_POST['make']) < 1){
         $failure = "Make is required";
       }
       elseif (! is_numeric($_POST['year']) || ! is_numeric($_POST['mileage'])){
         $failure = "Mileage and year must be numeric";
       }
       else{
         $sql = "INSERT INTO autos (make, year, mileage)
                   VALUES (:make, :year, :mileage)";
         $stmt = $pdo->prepare($sql);
         $stmt->execute(array(
            ':make' => $_POST['make'],
            ':year' => $_POST['year'],
            ':mileage' => $_POST['mileage']));
        $success = "Record inserted";
       }
     }
?>

<!DOCTYPE html>
<html>
<head>
<title>Kantapong Automobile Tracker</title>
</head>
<body>
<div class="container">
<h1><?php echo("Tracking Autos for ".$_GET['name']); ?></h1>
<?php require_once "bootstrap.php";
  if ($failure !== false){
    echo("<p style='color: red;'>".$failure."</p>");
  }
  if ($success !== false){
    echo("<p style='color: green;'>".$success."</p>");
  }
?>
<form method="post">
<p>Make:
<input type="text" name="make" size="60"/></p>
<p>Year:
<input type="text" name="year"/></p>
<p>Mileage:
<input type="text" name="mileage"/></p>
<input type="submit" value="Add">
<input type="submit" name="logout" value="Logout">
</form>

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
</body>
</html>
