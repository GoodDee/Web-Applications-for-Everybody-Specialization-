<?php // Do not put any HTML above this line

if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123

session_start(); //Start Session

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['error'] = "Email and password are required";
        header("Location: login.php");
        return;
    } else {
      if (strpos($_POST['email'], '@') === false){
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        return;
      }
      else{
        $check = hash('md5', $salt.$_POST['pass']);
        if ( $check == $stored_hash ) {
          // Redirect the browser to game.php
          $_SESSION['who'] = $_POST['email'];
          error_log("Login success ".$_POST['email']);
          header("Location: view.php");
          return;
        }
        else{
          error_log("Login fail ".$_POST['email']." $check");
          $_SESSION['error'] = "Incorrect password";
          header("Location: login.php");
          return;
        }
      }
    }
}
// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Kantapong's Login Page</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
// Note triple not equals and think how badly double
// not equals would work here...
if ( isset($_SESSION['error'])) {
    // Look closely at the use of single and double quotes
    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}
?>
<form method="POST">
User Name <input type="text" name="email"><br/>
Password <input type="text" name="pass"><br/>
<input type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is the four character sound a cat
makes (all lower case) followed by 123. -->
</p>
</div>
</body>
