<?php

  session_start();

  if (!isset($_SESSION["signedin"]) || $_SESSION["signedin"] !== true) {
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

  $register_code = $register_date = $day = $month = $year = $verify = $info = "";
  $verify_error  = "";
  $error = array();


  if(isset($_GET["upd_id"]) && !empty(trim($_GET["upd_id"]))){
     $upd_id = validate_input($_GET["upd_id"]);
     $_SESSION['update_register_id'] = $upd_id;
  }

  if(isset($_GET["upd_date"]) && !empty(trim($_GET["upd_date"]))){
     $upd_date = validate_input($_GET["upd_date"]);
     $_SESSION['update_register_date'] = $upd_date;
  }

  if($_SERVER["REQUEST_METHOD"] == "POST"){  

  $upd_id = validate_input($_POST["upd_id"]);
  $upd_date = validate_input($_POST["upd_date"]);

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
  
  $new_month = date("m",strtotime($month));
  
  $new_date = $year."-".$new_month."-".$day;

 if(!$error && empty($verify_error)){

    $sql = "UPDATE attendance_register SET register_date = ? WHERE register_code= ? AND register_date= ?";
    
    if($stmt = $conn->prepare($sql)){

      $stmt->bind_param("sss", $param_newdate, $param_code, $param_date);
      $param_date = $upd_date;
      $param_code = $upd_id;
      $param_newdate = $new_date;

      if($stmt->execute()){
               
        $info = "<span class='success-message'>Attendace Register Updated.</span><br>";

        $reload = $_SERVER['PHP_SELF'];
          
        header("Refresh: 5; $reload");

      }else{
              $info = "<span class='success-message'>Failed to delete. Please try again later.</span><br>";
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
	<title>Update attendance register</title>
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
 <h2>Update Attendace Register</h2>
 <table>
  <thead>
  <tr>
    <th>Date</th>
    <th>Subject Code</th>
  </tr>
  </thead>
  <tbody>
  <tr>
    <td><?php echo $_SESSION['update_register_date'];?></td>
    <td><?php echo $_SESSION['update_register_id'];?></td>
  </tr>
  </tbody>
  </table>
 <span class="error"><?php echo $info;?></span><br>
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
  <input type="hidden" name="upd_id" value="<?php echo $_SESSION['update_register_id'];?>">
  <input type="hidden" name="upd_date" value="<?php echo $_SESSION['update_register_date'];?>">
  <input type="checkbox" name="verify" value="verify"><span class="bold-text">Confirm</span><br>
  <div><?php echo $verify_error;?></div>
  <input type="submit" value="Save">
  <a class="button-back" href="all_registers.php?reg_id=<?php echo $_SESSION['update_register_id'];?>">Back</a>
</form> 
</div>
</div>
</body>
</html>