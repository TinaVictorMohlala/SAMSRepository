<?php

  session_start();

  if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}
    
  require_once("../config/connect.php");

  
  function validate_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
  }

  $mark_id = $mark_date = $mark_course = "";
  $mark = 1;

  if(isset($_GET["mark_id"]) && !empty(trim($_GET["mark_id"]))){
     $mark_id= validate_input($_GET["mark_id"]);
  }

  if(isset($_GET["mark_date"]) && !empty(trim($_GET["mark_date"]))){
     $mark_date = validate_input($_GET["mark_date"]);
  }

  if(isset($_GET["mark_course"]) && !empty(trim($_GET["mark_course"]))){
     $mark_course = validate_input($_GET["mark_course"]);
  }

    $sql = "UPDATE attendance_register SET register_status = ? WHERE register_code= ? AND register_date= ? AND    register_student_no=?";
    
    if($stmt = $conn->prepare($sql)){

      $stmt->bind_param("isss", $param_mark, $param_code, $param_date, $param_id);
      $param_mark = $mark;
      $param_date = $mark_date;
      $param_code = $mark_course;
      $param_id = $mark_id;

      echo $param_mark;
      echo $param_date;
      echo $param_code;
      echo $param_id;

      if($stmt->execute()){
               
        header("location: ../student_register.php?success_state=Attendace Register Marked&registered_course=$mark_course&registered_student=$mark_id");
        exit();

      }else{
              echo "Failed to delete. Please try again later.";
      }
    }

    $stmt->close();
    $conn->close(); 
   

?>
