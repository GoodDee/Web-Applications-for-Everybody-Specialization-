<?php // line 1 added to trigger color syntax highlight
function validatePos() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($year) ) {
      return "Position year must be numeric";
    }
  }
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['edu_school'.$i]) ) continue;

    $edu_year = $_POST['edu_year'.$i];
    $edu_school = $_POST['edu_school'.$i];

    if ( strlen($edu_year) == 0 || strlen($edu_school) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($edu_year) ) {
      return "Position year must be numeric";
    }
  }


  return true;
}

function insert_position($pdo, $profile_id){
  $rank = 1;
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;
    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year,
                          description) VALUES ( :pid, :rank, :year, :desc)');
    $stmt->execute(array(
              ':pid' => $profile_id,
              ':rank' => $rank,
              ':year' => $year,
              ':desc' => $desc)
            );
    $rank++;
  }
  return true;
}

function insert_edu($pdo, $profile_id){
  $rank = 1;
  for($i=1; $i<=9; $i++){
    if (! isset($_POST['edu_year'.$i])) continue;
    if (! isset($_POST['edu_school'.$i])) continue;

    $institution_id = false;
    $edu_year = $_POST['edu_year'.$i];
    $edu_school = $_POST['edu_school'.$i];

    $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :n');
    $stmt -> execute(array(':n' => $edu_school));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row !== false){
      $institution_id = $row['institution_id'];
    }
    else{
      $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:n)');
      $stmt->execute(array(':n' => $edu_school));
      $institution_id = $pdo->lastInsertId();
    }

    $stmt2 = $pdo->prepare('INSERT INTO Education (profile_id, institution_id, rank,
                          year) VALUES ( :pid, :iid, :r, :y)');
    $stmt2->execute(array(
                        ':pid' => $profile_id,
                        ':iid' => $institution_id,
                        ':r' => $rank,
                        ':y' => $edu_year)
                    );
    $rank++;
  }
  return true;
}
