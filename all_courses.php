<?php

session_start();


?>
<!DOCTYPE html>
<html>
<head>
	<title>Student List</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h4>Student Details</h4>
<a class="button-add" href="student_info.php?info=save">Add student</a><br>
<div class="table">
<?php 

session_start();

if (!isset($_SESSION["signedin"]) || $_SESSION["signedin"] !== true) {
    header("location: index.php");
    exit();
}

require_once("../config/connect.php");

if(isset($_GET["reg_id"]) && !empty(trim($_GET["reg_id"]))){

  $param_id = trim($_GET["reg_id"]);

?>

<?php


$sql = "SELECT * FROM attendance_register WHERE register_code = ?";
if($stmt = $conn->prepare($sql)){

  $stmt->bind_param("s", $param_id);

  if($stmt->execute()){

      $result = $stmt->get_result();

      if($result->num_rows > 0){

      echo "<div><table>";
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
               echo "<a class='button-update' href='course_info.php?info=update&upd_id=".$row['register_code']."'>Update</a>";
               echo "<a class='button-delete' href='course_info.php?info=delete&del_id=".$row['register_code']."'>Delete</a>";
               echo "<a class='button-view' href='course_info.php?info=view&view_id=".$row['register_code']."'>view</a>";
            echo "</td>";
          echo "</tr>";

       }
       echo "</tbody>";
       echo "</table>";
     
  }else{

    echo "<p>No records were found</p>";
  }
  
}else{
  echo "Failed to execute ";
}

$conn->close();

}

}

?>
</div>
<div >
<a href="../Admin/account_profile.php">Dashbord</a>
</div>
</body>
</html>

