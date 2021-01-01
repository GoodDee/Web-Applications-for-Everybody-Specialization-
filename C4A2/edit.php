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

          $rank = 1;
          for($i=1; $i<=9; $i++) {
              if ( ! isset($_POST['year'.$i]) ) continue;
              if ( ! isset($_POST['desc'.$i]) ) continue;
              $year = $_POST['year'.$i];
              $desc = $_POST['desc'.$i];
              $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year,
                                    description) VALUES ( :pid, :rank, :year, :desc)');
              $stmt->execute(array(
                          ':pid' => $_REQUEST['profile_id'],
                          ':rank' => $rank,
                          ':year' => $year,
                          ':desc' => $desc)
                          );
              $rank++;
                }
          $_SESSION['success'] = "Record edited";
          header('Location: index.php');
          return;
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
<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
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
 ?>
</div>
</p>
<input type="submit" value="Edit">
<input type="submit" name="cancel" value="Cancel">

<script>
countPos = $("#position_fields > div").length;
$(document).ready(function(){
  console.log('Document Ready Called');
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

</form>
