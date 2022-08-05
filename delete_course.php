
<?php

session_start();

if (!isset($_SESSION["signedin"]) || $_SESSION["signedin"] !== true) {
    header("location: index.php");
    exit();
}

$error = $course_code = $course_name = "";
   
require_once("../config/connect.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(isset($_POST["del_id"]) && !empty($_POST["del_id"])){

        $sql = "DELETE FROM courses WHERE course_id = ?";
        if($stmt = $conn->prepare($sql)){

            $stmt->bind_param("i", $param_del);
            $param_del = trim($_POST["del_id"]);
            if($stmt->execute()){
              
              $error = "<span class='success-message'>Subject Deleted Successfully.</span><br>";

              $reload = "../admin/account_profile.php";
          
              header("Refresh: 5; $reload");

            }else{
              $error = "<span class='success-message'>Failed to delete. Please try again later.</span><br>";
            }
        }

        $stmt->close();
        $conn->close();
    }

  }

?>
<!DOCTYPE html>
<html>
<head>
	<title>Delete Subject</title>
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

if(isset($_GET["del_id"]) && !empty(trim($_GET["del_id"]))){

    $sql = "SELECT * FROM courses WHERE course_id = ?";
    if($stmt = $conn->prepare($sql)){

       $stmt->bind_param("i", $param_del);
       $param_del = $_GET["del_id"];
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
<h4>Are your sure you want to delete this Subject?</h4>
<div><?php echo $error;?></div>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  Subject Code<br>
  <input type="text" name="course_code" value="<?php echo $course_code;?>" readonly><br>
  Subject Name<br>
  <input type="text" name="course_name" value="<?php echo $course_name;?>" readonly><br>
  <input type="hidden" name="info" value="delete">
  <input type="hidden" name="del_id" value="<?php echo $course_id;?>">
  <input type="submit" name="submit" value="Confirm">
  <a class="button-back" href="../admin/account_profile.php">Back</a>
</form>
</div>
</div>
</body>
</html>