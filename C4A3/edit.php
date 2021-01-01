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

$stmt2 = $pdo->prepare("SELECT * FROM position WHERE profile_id = :pid ORDER BY rank");
$stmt2->execute(array(':pid' => $_GET['profile_id']));
$positions = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$stmt3 = $pdo->prepare("SELECT education.rank AS rank, institution.name AS name, education.year AS year
                        FROM education JOIN institution ON education.institution_id = institution.institution_id
                        WHERE education.profile_id = :pid
                        ORDER BY rank");
$stmt3->execute(array(':pid' => $_GET['profile_id']));
$educations = $stmt3->fetchAll(PDO::FETCH_ASSOC);

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
         $val = validatePos();
         if ($val !== true){
           $_SESSION['failure'] = $val;
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

          $stmt2 = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
          $stmt2->execute(array( ':pid' => $_REQUEST['profile_id']));

          $stmt3 = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
          $stmt3->execute(array( ':pid' => $_REQUEST['profile_id']));


          if (insert_position($pdo, $_POST['profile_id']) && insert_edu($pdo, $_POST['profile_id'])){
            $_SESSION['success'] = "Record edited";
            header('Location: index.php');
            return;
          }
         }
       }
     }



$first_name = htmlentities($row['first_name']);
$last_name = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
$pid = $row['profile_id'];

?>
<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
</head>
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
<p>
Education: <input type = "submit" id = "addEdu" value = "+">
<div id = "education_fields">
<?php
if(count($educations) > 0){
  foreach($educations as $education){
    echo('<div id = "education'.$education['rank'].'">'.
        '<p>Year: <input type="text" name="edu_year'.$education['rank'].'" value = '.htmlentities($education['year']).'>'.
        '<input type = "button" value = "-" onclick = "$(\'#education'.$education['rank'].'\').remove(); return false;"></p>'.
        '<p>School: <input type="text" size="80" name="edu_school'.$education['rank'].'" class="school" value="'.htmlentities($education['name']).'"/></p></div>');
  };
}
echo "</div>";
?>

Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
<?php
if(count($positions) > 0){
  foreach($positions as $position){
    echo('<div id = "position'.$position['rank'].'">'.
        '<p>Year: <input type="text" name = "year'.$position['rank'].'" value ='.htmlentities($position['year']).'>'.
        '<input type = "button" value = "-" onclick = "$(\'#position'.$position['rank'].'\').remove(); return false;"></p>'.
        '<textarea name = "desc'.$position['rank'].'" rows = "8" cols = "80">'.htmlentities($position['description']).'</textarea> </div>');
  };
}
echo "</div>";
 ?>
</p>
<input type="submit" value="Edit">
<input type="submit" name="cancel" value="Cancel">
</form>

<script>
countPos = <?= count($positions) ?>;
countSchool = <?= count($educations) ?>;
$('.school').autocomplete({
  source: "school.php"
});
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
