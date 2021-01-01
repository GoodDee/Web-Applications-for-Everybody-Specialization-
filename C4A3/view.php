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

$stmt2 = $pdo->prepare("SELECT * FROM position WHERE profile_id = :pid ORDER BY rank");
$stmt2->execute(array(':pid' => $_GET['profile_id']));
$positions = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$stmt3 = $pdo->prepare("SELECT education.rank AS rank, institution.name AS name, education.year AS year
                        FROM education JOIN institution ON education.institution_id = institution.institution_id
                        WHERE education.profile_id = :pid
                        ORDER BY rank");
$stmt3->execute(array(':pid' => $_GET['profile_id']));
$educations = $stmt3->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
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
if(count($educations) > 0){
  echo "<p>Education: <ul>";
  foreach($educations as $education){
    echo "<li>".$education['year'].": ".$education["name"];
  };
  echo "</ul> </p>";
}
if(count($positions) > 0){
  echo "<p>Position: <ul>";
  foreach($positions as $position){
    echo "<li>".$position['year'].": ".$position["description"];
  };
  echo "</ul> </p>";
}


?>
<p>
<a href="index.php">Done</a>
</p>
</div>
</body>
</html>
