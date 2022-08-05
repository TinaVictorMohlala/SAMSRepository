<?php

session_start();

if (!isset($_SESSION["signedin"]) || $_SESSION["signedin"] !== true) {
    header("location: index.php");
    exit();
}

if(isset($_GET["reg_id"]) && !empty(trim($_GET["reg_id"]))){

  $param_id = trim($_GET["reg_id"]);
  $_SESSION["back_id"] = $param_id;

}


?>
<!DOCTYPE html>
<html>
<head>
	<title>Attendance registers</title>
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
<p><a class="button-add" href="add_register.php?reg_id=<?php echo $param_id;?>">create register</a></p>
</div><br>
<div class="main">
<h2>Attendance Register Details</h2>
<?php 

require_once("../config/connect.php");

$sql = "SELECT DISTINCT register_date, register_code FROM attendance_register WHERE register_code = ?";
if($stmt = $conn->prepare($sql)){

  $stmt->bind_param("s", $param_id);

  if($stmt->execute()){

      $result = $stmt->get_result();

      if($result->num_rows > 0){

      echo "<table>";
       echo "<thead>";
          echo "<tr>";
             echo "<th>#</th>";
             echo "<th>Subject Code</th>";
             echo "<th>Date</th>";
             echo "<th>Action</th>";
          echo "<tr>";
       echo "</thead>";
       echo "<tbody>";
       $count = 0;
       while ( $row = $result->fetch_assoc()) {
          $count++;
          echo "<tr>";
            echo "<td>".$count."</td>";
            echo "<td>".$row['register_code']."</td>";
            echo "<td>".$row['register_date']."</td>";
            echo "<td>";
               echo "<a class='button-update' href='update_register.php?upd_date=".$row['register_date']."&upd_id=".$row['register_code']."'>Update</a>";
               echo "<a class='button-delete' href='delete_register.php?del_date=".$row['register_date']."&del_id=".$row['register_code']."'>Delete</a>";
               echo "<a class='button-view' href='view_register.php?view_date=".$row['register_date']."&view_id=".$row['register_code']."'>view</a>";
            echo "</td>";
          echo "</tr>";

       }
       echo "</tbody>";
       echo "<table>";
     
  }else{

    echo "<p>No records were found</p>";
  }
  
}else{
  echo "Failed to execute ";
}
$stmt->close();
$conn->close();

}

?>
<h4><a class="button-back" href="../admin/account_profile.php">Back</a></h4> 
</div>
</body>
</html>

