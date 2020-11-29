<?php
require_once "pdo.php";
session_start();
// Demand a GET parameter
if ( ! isset($_SESSION['who']) ) {
    die('Name parameter missing');
}
// Logout: Return to index page
if ( isset($_POST['cancel']) ) {
    header('Location: view.php');
    return;
}
// Do data validation and then insert
if ( isset($_POST['make']) && isset($_POST['year'])
     && isset($_POST['mileage'])){
       if (strlen($_POST['make']) < 1){
         $_SESSION['failure'] = "Make is required";
         header('Location: autos.php');
         return;
       }
       elseif (! is_numeric($_POST['year']) || ! is_numeric($_POST['mileage'])){
         $_SESSION['failure'] = "Mileage and year must be numeric";
         header('Location: autos.php');
         return;
       }
       else{
         $sql = "INSERT INTO autos (make, year, mileage)
                   VALUES (:make, :year, :mileage)";
         $stmt = $pdo->prepare($sql);
         $stmt->execute(array(
            ':make' => $_POST['make'],
            ':year' => $_POST['year'],
            ':mileage' => $_POST['mileage']));
        $_SESSION['success'] = "Record inserted";
        header('Location: autos.php');
        return;
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
<h1><?php echo("Tracking Autos for ".$_SESSION['who']) ?></h1>
<?php require_once "bootstrap.php";
  if (isset($_SESSION['failure'])){
    echo("<p style='color: red;'>".$_SESSION['failure']."</p>");
    unset($_SESSION['failure']);
  }
  if (isset($_SESSION['success'])){
    echo("<p style='color: green;'>".$_SESSION['success']."</p>");
    unset($_SESSION['success']);
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
<input type="submit" name="cancel" value="cancel">
</form>

</body>
</html>
