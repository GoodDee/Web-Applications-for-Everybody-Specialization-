<!DOCTYPE html>
<html>
<head>
<title>Kantapong V's Resume Registry</title>
<?php
session_start();
require_once "bootstrap.php";
require_once "pdo.php";
?>
</head>
<body>
<div class="container">

<?php
if (!isset($_SESSION['user_id'])){
  echo "<h1> Kantapong V Resume Registry </h1>";
  if (isset($_SESSION['failure'])){
    echo '<p style="color:red">'.$_SESSION['failure']."</p>\n";
    unset($_SESSION['failure']);
  }
  if (isset($_SESSION['success'])){
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
  }
  $stmt = $pdo->query("SELECT * FROM profile");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo '<a href="login.php">Please log in</a><br>';
  if (count($rows) == 0){
    echo "";
  }
  else{
    echo('<table border="1">'."\n");
    echo '<tr> <th>Name</th> <th>Headline</th> </tr>';
    foreach ( $rows as $row ){
        $profile_id = $row['profile_id'];
        $first_name = htmlentities($row['first_name']);
        $last_name = htmlentities($row['last_name']);
        $headline = htmlentities($row['headline']);
        echo "<tr><td>";
        echo '<a href="view.php?profile_id='.$profile_id.'">'. $first_name.' '.$last_name. '</a>';
        echo "</td><td>";
        echo $headline;
        echo("</td></tr>\n");
    }
    echo "</table>";
  }
}
else{
  echo '<h1>'.$_SESSION['name']."'s Resume Registry </h1>";
  if (isset($_SESSION['failure'])){
    echo '<p style="color:red">'.$_SESSION['failure']."</p>\n";
    unset($_SESSION['failure']);
  }
  if (isset($_SESSION['success'])){
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
  }
  $stmt = $pdo->prepare("SELECT * FROM profile WHERE user_id = :uid");
  $stmt->execute(array(':uid' => $_SESSION['user_id']));
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo '<a href="logout.php">Logout</a><br>';
  if (count($rows) == 0){
    echo "";
  }
  else{
    echo('<table border="1">'."\n");
    echo '<tr> <th>Name</th> <th>Headline</th> <th>Action</th></tr>';
    foreach ( $rows as $row ){
        $profile_id = $row['profile_id'];
        $first_name = htmlentities($row['first_name']);
        $last_name = htmlentities($row['last_name']);
        $headline = htmlentities($row['headline']);
        echo "<tr><td>";
        echo '<a href="view.php?profile_id='.$profile_id.'">'. $first_name.' '.$last_name. '</a>';
        echo "</td><td>";
        echo $headline;
        echo "</td><td>";
        echo '<a href="edit.php?profile_id='.$profile_id.'">'. 'Edit'. '</a>';
        echo '  /  ';
        echo '<a href="delete.php?profile_id='.$profile_id.'">'. 'Delete'. '</a>';
        echo("</td></tr>\n");
    }
    echo "</table>";
  }
  echo '<a href="add.php">Add New Entry</a>';
}

?>
</div>
</body>
