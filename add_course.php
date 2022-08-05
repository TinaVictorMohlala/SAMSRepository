<?php

  session_start();

  if (!isset($_SESSION["signedin"]) || $_SESSION["signedin"] !== true) {
    header("location: index.php");
    exit();
  }
    
  require_once("../config/connect.php");

  $course_code = $course_name = $course_id= $course_instructor = "";
  $course_code_error = $course_name_error = $error = "";

  function validate_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
  }

  if($_SERVER["REQUEST_METHOD"] == "POST"){

  if(empty($_POST['course_code'])) {

    $course_code_error = "<span class='error-message'>Subject code is required.</span><br>";
  } else {
    
    $course_code = validate_input($_POST["course_code"]);

    $sql = "SELECT course_id FROM courses WHERE course_code = ?";

    if($stmt = $conn->prepare($sql)){

      $stmt->bind_param("s", $param_code);
      $param_code = $course_code;
            
      if($stmt->execute()){

        $stmt->store_result();
        if($stmt->num_rows == 1){

          $course_code_error = "<span class='error-message'>This Subject already exist.</span><br>";

        }

      }else{
          
          $course_code_error = "<span class='error-message'>Failed to check if Subject exist.</span><br>";
      }

      $stmt->close();

    }
  }

  if(empty($_POST['course_name'])) {

    $course_name_error = "<span class='error-message'>Subject name is required.</span><br>";
  } else {
    
    $course_name = validate_input($_POST["course_name"]);
   
  }

  $course_instructor = $_SESSION["username"];

  if(empty($course_code_error) && empty($course_name_error)){

      $sql = "INSERT INTO courses (course_code, course_name, course_instructor) VALUES (?, ?, ?)";

      if($stmt = $conn->prepare($sql)){ 

          $stmt->bind_param("sss", $course_code, $course_name, $course_instructor);
          if($stmt->execute()){

          $error = "<span class='success-message'>Subject Added Successfully.</span><br>";

          $reload = $_SERVER['PHP_SELF'];
          
          header("Refresh: 5; $reload");
      
          
          }else{

          $error = "<span class='error-message'>Failed to Add Subject.</span><br>";

          }

          $stmt->close();
        }

    }

  $conn->close ();

  }

?>
<!DOCTYPE html>
<html>
<head>
	<title>Add Subject</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="nav-bar">
<ul>
  <li><a class="active logo" href="#home">SAMS</a></li>
  <li style="float:right"><a class="button-signout" href="../config/logout.php">Logout</a></li>
  <li style="float:right"><a href="../admin/account_profile.php">Dashbord</a></li>
</ul>
</div>
<div class="header">
<div class="add-form">
<h2>Add New Subject</h2>
<div><?php echo $error;?></div>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <label>Subject Code<span style="color:red;">&nbsp*</span></label>
  <input type="text" name="course_code" ><br>
  <div><?php echo $course_code_error;?></div>
  <label>Subject Name<span style="color:red;">&nbsp*</span></label>
  <input type="text" name="course_name"><br>
  <div><?php echo $course_name_error;?></div>
  <input type="submit" name="submit" value="Save">
  <a class="button-back" href="../admin/account_profile.php">Back</a>
</form>
</div>
</div>
</body>
</html>