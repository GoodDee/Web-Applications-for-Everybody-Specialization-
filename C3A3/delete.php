<?php
require_once "pdo.php";
session_start();
// Demand a GET parameter
if ( ! isset($_GET['auto_id']) ) {
    $_SESSION['failure'] = 'Bad Value for id';
    header('Location: view.php');
    return;
}

if ( isset($_POST['delete']) && isset($_POST['auto_id']) ) {
    $sql = "DELETE FROM autos WHERE auto_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':id' => $_POST['auto_id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: view.php' ) ;
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


?>
<p>Confirm: Deleting <?= htmlentities($row['make']) ?></p>

<form method="post">
<input type="hidden" name="auto_id" value="<?= $row['auto_id'] ?>">
<input type="submit" value="Delete" name="delete">
<a href="view.php">Cancel</a>
