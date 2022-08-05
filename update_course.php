<?php

session_start();

if (!isset($_SESSION["signedin"]) || $_SESSION["signedin"] !== true) {
    header("location: index.php");
    exit();
}
  
  if(isset($_POST['info']) && !empty($_POST["info"])){
    $info_type = $_POST['info'];
  } 

   if(isset($_GET['info']) && !empty($_GET["info"])){
    $info_type = $_GET['info'];
  } 
  
  require_once("../config/connect.php");



  $course_code = $course_name = $course_id= "";
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
  }

  if(empty($_POST['course_name'])) {

    $course_name_error = "<span class='error-message'>Subject name is required.</span><br>";
  } else {
    
    $course_name = validate_input($_POST["course_name"]);
  }

  if(isset($_POST["upd_id"]) && !empty($_POST["upd_id"])){

      if(empty($course_code_error) && empty($course_name_error)){

        $sql = "UPDATE courses SET course_code = ?, course_name = ? WHERE course_id = ?";
        if($stmt = $conn->prepare($sql)){

            $stmt->bind_param("ssi", $param_code, $param_name, $param_id);
            $param_id = trim($_POST["upd_id"]);
            $param_code = $course_code;
            $param_name = $course_name;

            if($stmt->execute()){

              $error = "<span class='success-message'>Course Updated Successfully.</span><br>";

              $reload = "../admin/account_profile.php";
          
              header("Refresh: 5; $reload");
              

            }else{
              $error = "<span class='error-message'>Failed to delete. Please try again later.</span><br>";
            }
        }

        $stmt->close();
        $conn->close();
      }
    }

  }

?>
<!DOCTYPE html>
<html>
<head>
	<title>Update Subject</title>
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
<?php 

  if(isset($_GET["upd_id"]) && !empty(trim($_GET["upd_id"]))){

    $sql = "SELECT * FROM courses WHERE course_id = ?";
    if($stmt = $conn->prepare($sql)){

       $stmt->bind_param("i", $param_id);
       $param_id = trim($_GET["upd_id"]);
       if($stmt->execute()){

          $result = $stmt->get_result();
          if($result->num_rows == 1){

             $row = $result->fetch_array(MYSQLI_ASSOC);
             $course_id = $row["course_id"];
             $course_name = $row["course_name"];
             $course_code = $row["course_code"];

          }else{
             
             echo "Failed. Try again later.";
          }
       }

       $stmt->close();
       $conn->close();

    }
  }

?>
<div class="add-form">
<h2>Update Subject</h2>
<div><?php echo $error;?></div>
 <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <label>Subject Code<span style="color:red;">&nbsp*</span></label>
  <input type="text" name="course_code" value="<?php echo $course_code;?>"><br>
  <div><?php echo $course_code_error;?></div>
  <label>Subject Name<span style="color:red;">&nbsp*</span></label>
  <input type="text" name="course_name" value="<?php echo $course_name;?>"><br>
  <div><?php echo $course_name_error;?></div>
  <input type="hidden" name="info" value="update">
  <input type="hidden" name="upd_id" value="<?php echo $course_id;?>">
  <input type="submit" name="submit" value="Update">
  <a class="button-back" href="../admin/account_profile.php">Back</a>
</form>
</div>
</div>
</body>
</html>