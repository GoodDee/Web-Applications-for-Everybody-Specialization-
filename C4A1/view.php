<?php
require_once "pdo.php";
session_start();
// Demand a GET parameter
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
$stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :pid");
$stmt->execute(array(':pid' => $_GET['profile_id']));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($rows) == 0){
  header('Location: index.php');
  return;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Profile Information</title>
</head>
<body>
<div class="container">
<?php
echo "<p>First Name: ".$rows[0]['first_name']."</p>";
echo "<p>Last Name: ".$rows[0]['last_name']."</p>";
echo "<p>Email: ".$rows[0]['email']."</p>";
echo "<p>Headline: ".$rows[0]['headline']."</p>";
echo "<p>Summary: ".$rows[0]['summary']."</p>";
?>
<p>
<a href="index.php">Done</a>
</p>
</div>
</body>
</html>
