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
// Do data validation and then insert
if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){
       if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||
          strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1){
         $_SESSION['failure'] = "All fields are required";
         header('Location: add.php');
         return;
       }
       elseif (strpos($_POST['email'], '@') === false){
         $_SESSION['failure'] = "Email must contain '@'";
         header('Location: add.php');
         return;
       }
       else{
         $sql = "INSERT INTO profile (user_id, first_name, last_name, email, headline, summary)
                VALUES (:uid, :fn, :ln, :em, :he, :su)";
         $stmt = $pdo->prepare($sql);

         $stmt->execute(array(
                ':uid' => $_SESSION['user_id'],
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':em' => $_POST['email'],
                ':he' => $_POST['headline'],
                ':su' => $_POST['summary'])
              );
        $_SESSION['success'] = "Record added";
        header('Location: index.php');
        return;
       }
     }
?>

<!DOCTYPE html>
<html>
<head>
<title>Adding Profile</title>
</head>
<body>
<div class="container">
<h1><?php echo("Adding Profile for ".$_SESSION['name']) ?></h1>
<?php
if (isset($_SESSION['failure'])){
    echo("<p style='color: red;'>".$_SESSION['failure']."</p>");
    unset($_SESSION['failure']);
  }
?>
<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" size="60"/></p>
<p>Email:
<input type="text" name="email" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"></textarea>
<p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">

</body>
</html>
