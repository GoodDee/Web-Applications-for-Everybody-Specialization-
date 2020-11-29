<?php
require_once "pdo.php";
session_start();
// Demand a GET parameter
if ( ! isset($_SESSION['who']) ) {
    die('ACCESS DENIED');
}
// Logout: Return to index page
if ( isset($_POST['cancel']) ) {
    header('Location: view.php');
    return;
}
// Do data validation and then insert
if ( isset($_POST['make']) && isset($_POST['year'])
     && isset($_POST['mileage']) && isset($_POST['model'])){
       if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 ||
          strlen($_POST['mileage']) < 1 || strlen($_POST['year']) < 1){
         $_SESSION['failure'] = "All fields are required";
         header('Location: add.php');
         return;
       }
       elseif (! is_numeric($_POST['year']) || ! is_numeric($_POST['mileage'])){
         $_SESSION['failure'] = "Mileage and year must be numeric";
         header('Location: add.php');
         return;
       }
       else{
         $sql = "INSERT INTO autos (make, model, year, mileage)
                   VALUES (:make, :model, :year, :mileage)";
         $stmt = $pdo->prepare($sql);
         $stmt->execute(array(
            ':make' => $_POST['make'],
            ':model' => $_POST['model'],
            ':year' => $_POST['year'],
            ':mileage' => $_POST['mileage']));
        $_SESSION['success'] = "Record added";
        header('Location: view.php');
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
<?php
if (isset($_SESSION['failure'])){
    echo("<p style='color: red;'>".$_SESSION['failure']."</p>");
    unset($_SESSION['failure']);
  }
?>
<form method="post">
<p>Make:
<input type="text" name="make" size="60"/></p>
<p>Model:
<input type="text" name="model" size="60"/></p>
<p>Year:
<input type="text" name="year"/></p>
<p>Mileage:
<input type="text" name="mileage"/></p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="cancel">
</form>

</body>
</html>
