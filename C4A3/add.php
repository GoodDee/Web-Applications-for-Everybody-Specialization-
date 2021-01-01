<?php
require_once "pdo.php";
require_once "helper.php";
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
         $val = validatePos();
         if ($val !== true){
           $_SESSION['failure'] = $val;
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
          $profile_id = $pdo->lastInsertId();

          if (insert_position($pdo, $profile_id) && insert_edu($pdo, $profile_id)){
            $_SESSION['success'] = "Record added";
            header('Location: index.php');
            return;
          }
         }
       }
     }
?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
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
Education: <input type = "submit" id = "addEdu" value = "+">
<div id = "education_fields">
</div>
</p>
<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
</div>
</p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</form>

<script>
countSchool = 0;
countPos = 0;
$(document).ready(function(){
  console.log('Document Ready Called');
  $('#addEdu').click(function(event){
    event.preventDefault();
    if(countSchool >= 9){
      alert("Maximum of nine education entries exceeded");
      return;
    }
    countSchool++;
    var source = $('#edu-template').html();
    $('#education_fields').append(source.replace(/@num@/g, countSchool));
    $('.school').autocomplete({
      source: "school.php"
    });
  });
  $('#addPos').click(function(event){
    event.preventDefault();
    if (countPos >= 9){
      alert("Maximum of nine position entries exceeded");
      return;
    }
    countPos++;
    $('#position_fields').append('<div id = "position'+countPos+'">'+
                        '<p>Year: <input type="text" name = "year'+countPos+'" value = "">'+
                        '<input type = "button" value = "-" onclick = "$(\'#position'+countPos+'\').remove(); return false;"></p>'+
                      '<textarea name = "desc'+countPos+'" rows = "8" cols = "80"></textarea> </div>');
  });
});
</script>

<script id = "edu-template" type = "text">
<div id = "education@num@">
  <p>Year: <input type="text" name="edu_year@num@" value = "">
  <input type = "button" value = "-" onclick = "$('#education@num@').remove(); return false;"></p>
  <p>School: <input type="text" size="80" name="edu_school@num@" class="school" value="" /></p>
</div>
</script>

</body>
</html>
