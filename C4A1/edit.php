<?php
require_once "pdo.php";
session_start();
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

// Do data validation and then insert
if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){
       if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||
          strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1){
         $_SESSION['failure'] = "All fields are required";
         header('Location: edit.php?profile_id='.$_GET['profile_id']);
         return;
       }
       elseif (strpos($_POST['email'], '@') === false){
         $_SESSION['failure'] = "Email must contain '@'";
         header('Location: edit.php?profile_id='.$_GET['profile_id']);
         return;
       }
       else{
         $sql = "UPDATE profile SET user_id = :uid, first_name = :fn, last_name = :ln,
                email = :em, headline = :he, summary = :su WHERE profile_id = :pid";
         $stmt = $pdo->prepare($sql);
         $stmt->execute(array(
                ':uid' => $_SESSION['user_id'],
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':em' => $_POST['email'],
                ':he' => $_POST['headline'],
                ':su' => $_POST['summary'],
                ':pid' => $_POST['profile_id'])
              );
        $_SESSION['success'] = "Record edited";
        header('Location: index.php');
        return;
       }
     }



$first_name = htmlentities($row['first_name']);
$last_name = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
$pid = $row['profile_id'];

?>
<p>Edit Resume Database</p>
<?php
// Flash pattern
if ( isset($_SESSION['failure']) ) {
    echo '<p style="color:red">'.$_SESSION['failure']."</p>\n";
    unset($_SESSION['failure']);
}
?>

<form method="post">
<p>First Name:
<input type="text" name="first_name" value="<?= $first_name ?>" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" value="<?= $last_name ?>" size="60"/></p>
<p>Email:
<input type="text" name="email" value="<?= $email ?>" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" value="<?= $headline ?>" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"><?= $summary ?></textarea>
<input type="hidden" name="profile_id" value="<?= $pid ?>">
<p>
<input type="submit" value="Edit">
<input type="submit" name="cancel" value="Cancel">
</form>
