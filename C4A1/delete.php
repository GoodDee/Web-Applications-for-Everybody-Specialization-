<?php
require_once "pdo.php";
session_start();
// Demand a GET parameter
// Demand a GET parameter
if ( ! isset($_SESSION['user_id']) ) {
    die('Not logged in');
}

// Logout: Return to index page
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['failure'] = "Missing profile id";
  header('Location: index.php');
  return;
  if (strlen($_GET['profile_id']) < 1){
    $_SESSION['failure'] = "Missing profile id";
    header('Location: index.php');
    return;
  }
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :pid");
$stmt->execute(array(":pid" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['failure'] = 'Bad value for profile id';
    header( 'Location: index.php' ) ;
    return;
}
else{
  if ($row['user_id'] != $_SESSION['user_id']){
    $_SESSION['failure'] = 'Not allowed to edit this entry';
    header( 'Location: index.php' ) ;
    return;
  }
}

if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM profile WHERE profile_id = :pid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':pid' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
}


?>
<p>Confirm: Deleting <br>
  First Name: <?= htmlentities($row['first_name']) ?> <br>
  Last Name: <?= htmlentities($row['last_name']) ?> </p>

<form method="post">
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<input type="submit" value="Delete" name="delete">
<input type="submit" name="cancel" value="Cancel">
</form>
