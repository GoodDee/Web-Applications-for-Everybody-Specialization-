<?php // Do not put any HTML above this line

if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}

session_start(); //Start Session
require_once "bootstrap.php";
require_once "pdo.php";

$salt = 'XyZzy12*_';

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['failure'] = "Email and password are required";
        header("Location: login.php");
        return;
    } else {
      if (strpos($_POST['email'], '@') === false){
        $_SESSION['failure'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        return;
      }
      else{
        $check = hash('md5', $salt.$_POST['pass']);
        $sql = "SELECT * from users WHERE email = :e AND password = :p";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
               ':e' => $_POST['email'],
               ':p' => $check)
             );
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ( $row !== false ) {
          $_SESSION['name'] = $row['name'];
          $_SESSION['user_id'] = $row['user_id'];
          header("Location: index.php");
          return;
        }
        else{
          $_SESSION['failure'] = "Either username or password does not match";
          header("Location: login.php");
          return;
        }
      }
    }
}
// Fall through into the View
?>
<script>
function doValidate() {
    console.log('Validating...');
    var email = document.getElementById('id_1711').value;
    var pw = document.getElementById('id_1723').value;
    console.log("Validating email="+email);
    console.log("Validating pw="+pw);
    if (email == null || pw == null || email == "" || pw == ""){
        alert("Both fields must be filled out");
        return false;
    }
    else{
      if (!email.includes('@')){
        alert("Invalid email address");
        return false;
      }
      return true;
    }
}
</script>
<!DOCTYPE html>
<html>
<head>
<title>Kantapong's Login Page</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
// Note triple not equals and think how badly double
// not equals would work here...
if ( isset($_SESSION['failure'])) {
    // Look closely at the use of single and double quotes
    echo('<p style="color: red;">'.htmlentities($_SESSION['failure'])."</p>\n");
    unset($_SESSION['failure']);
}
?>
<form method="POST">
User Name <input type="text" name="email" id = "id_1711"><br/>
Password <input type="text" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>
