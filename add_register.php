<?php

session_start();

if (!isset($_SESSION["signedin"]) || $_SESSION["signedin"] !== true) {
    header("location: index.php");
    exit();
}

if(isset($_GET["reg_id"]) && !empty(trim($_GET["reg_id"]))){

  $course_id = trim($_GET["reg_id"]);
  $_SESSION["course_id"] = $course_id;

}
    
  require_once("../config/connect.php");

  function validate_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
  }
  $register_code = $register_date = $day = $month = $year = $verify = $msg = $info = "";
  $register_lastname = $register_student_no = $register_firstname = array();
  $verify_error = $register_date_error = "";
  $error = array();

  if($_SERVER["REQUEST_METHOD"] == "POST"){  

  if(isset($_POST["year"]) && !empty(trim($_POST["year"]))){
    $year = $_POST["year"];
  }else{
    $error[] = "Year is required.";
  }

  if(isset($_POST["month"]) && !empty(trim($_POST["month"]))){
    $month = $_POST["month"];
  }else{
    $error[] = "Month is required.";
  }

  if(isset($_POST["day"]) && !empty(trim($_POST["day"]))){
    $day = $_POST["day"];
  }else{
    $error[] = "Day is required.";
  }

  if(isset($_POST["verify"]) && !empty(trim($_POST["verify"]))){
    $verify = $_POST["verify"];
  }else{
    $verify_error = "<span class='error-message'>Please Confirm to create register.</span><br>";
  }

  if(isset($_POST["register_code"]) && !empty(trim($_POST["register_code"]))){

    $register_code = $_POST["register_code"];

    $sql = "SELECT * FROM registered_student WHERE student_course = ? ";

    if($stmt = $conn->prepare($sql)){

      $stmt->bind_param("s", $register_code);

      if($stmt->execute()){

        $result = $stmt->get_result();

        if($result->num_rows > 0){

          while ( $row = $result->fetch_assoc()) {

          $register_firstname[] = $row['student_firstname'];
          $register_lastname[] = $row['student_lastname'];
          $register_student_no[] = $row['student_no'];
          
          }  
        }else{
          $info = "<span class='error-message'>No students record were found in Attendence register.</span><br>";
        }
      }else{
        $info = "<span class='error-message'>Failed to execute.</span><br>";
      }
      $stmt->close();
    }

  }else{
    $error[] = "Register code is required.";
  }

  
  
  $new_month = date("m",strtotime($month));
  
  $new_date = $year."-".$new_month."-".$day;

 if(!$error && empty($verify_error)){

  foreach($register_firstname as $x => $x_value) {

    $sql = "INSERT INTO attendance_register (register_code, register_student_no, register_firstname, register_lastname, register_date) VALUES (?, ?, ?, ?, ?)";
     
    if($stmt = $conn->prepare($sql)){ 

      $stmt->bind_param("sssss", $register_code, $register_student_no[$x], $x_value, $register_lastname[$x], $new_date);
      if($stmt->execute()){

        $info = "<span class='success-message'>Attendence register created.</span><br>";

        $reload = $_SERVER['PHP_SELF'];
          
        header("Refresh: 5; $reload");
           
      }else{

        $info = "<span class='error-message'>Failed to add student into register.</span><br>";

      }
      
      $stmt->close();

    }
      
  } 
  }    
  }

?>
<!DOCTYPE html>
<html>
<head>
	<title>Register | Information</title>
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
 <h2>Create Attendace Register</h2>
 <div><?php echo $info;?></div>
 <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
 <?php
  echo"<label class='bold-text'>Date</label><br>";
  echo"<select name='day'>";
    for($i=1; $i<=31; $i++){
      if($i<9){
           echo "<option value=0$i>0$i</option>";
      }else{
         echo "<option value=$i>$i</option>";
      }    
    }     
  echo"</select>";
  echo"<select name='month'>";
    for($i=0; $i<=12; $i++){
      $month = date('F', strtotime("first day of -$i month"));
      echo "<option value=$month>$month</option>";    
    }     
  echo"</select>";
  echo"<select name='year'>";
    for($i=0; $i<=5; $i++){
      $year = date('Y', strtotime("first day of +$i year"));
      echo "<option value=$year>$year</option>";   
    }     
  echo"</select><br>";
  
?>
  <label class='bold-text'>Subject Code</label><br>
  <input type="text" name="register_code" value="<?php echo $_SESSION["course_id"];?>" readonly>
  <input type="checkbox" name="verify" value="verify"> <span class="bold-text">Confirm</span><br>
  <div><?php echo $verify_error;?></div>
  <input type="submit" name="submit" value="Save">
  <a class="button-back" href="all_registers.php?reg_id=<?php echo $_SESSION["back_id"];?>">Back</a>
</form> 
</div>
</div>
</body>
</html>