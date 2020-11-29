<?php
require_once "pdo.php";
session_start();
// Demand a GET parameter
if ( ! isset($_GET['auto_id']) ) {
    $_SESSION['failure'] = 'Bad Value for id';
    header('Location: view.php');
    return;
}
// Logout: Return to index page
if ( isset($_POST['cancel']) ) {
    header('Location: view.php');
    return;
}
// Select a row
$stmt = $pdo->prepare("SELECT * FROM autos where auto_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['auto_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['failure'] = 'Bad value for id';
    header( 'Location: view.php' ) ;
    return;
}

// Do data validation and then insert
if ( isset($_POST['make']) && isset($_POST['year'])
     && isset($_POST['mileage']) && isset($_POST['model'])){
       if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 ||
          strlen($_POST['mileage']) < 1 || strlen($_POST['year']) < 1){
         $_SESSION['failure'] = "All fields are required";
         header("Location: edit.php?auto_id=".$_GET['auto_id']);
         return;
       }
       elseif (! is_numeric($_POST['year']) || ! is_numeric($_POST['mileage'])){
         $_SESSION['failure'] = "Mileage and year must be numeric";
         header("Location: edit.php?auto_id=".$_GET['auto_id']);
         return;
       }
       else{
         $sql = "UPDATE autos SET make = :make, model = :model,
                year = :year, mileage = :mileage WHERE auto_id = :auto_id";
         $stmt = $pdo->prepare($sql);
         $stmt->execute(array(
            ':make' => $_POST['make'],
            ':model' => $_POST['model'],
            ':year' => $_POST['year'],
            ':mileage' => $_POST['mileage'],
            ':auto_id' => $_POST['auto_id']));
        $_SESSION['success'] = "Record edited";
        header('Location: view.php');
        return;
       }
     }

// Flash pattern
if ( isset($_SESSION['failure']) ) {
    echo '<p style="color:red">'.$_SESSION['failure']."</p>\n";
    unset($_SESSION['failure']);
}

$make = htmlentities($row['make']);
$model = htmlentities($row['model']);
$year = htmlentities($row['year']);
$mileage = htmlentities($row['mileage']);
$auto_id = $row['auto_id'];
?>
<p>Edit Auto Database</p>
<form method="post">
<p>Make:
<input type="text" name="make" value="<?= $make ?>"></p>
<p>Model:
<input type="text" name="model" value="<?= $model ?>"></p>
<p>Year:
<input type="text" name="year" value="<?= $year ?>"></p>
<p>Mileage:
<input type="text" name="mileage" value="<?= $mileage ?>"></p>
<input type="hidden" name="auto_id" value="<?= $auto_id ?>">
<p><input type="submit" value="Update"/>
<a href="view.php">Cancel</a></p>
</form>
