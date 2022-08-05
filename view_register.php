<?php

session_start();

if (!isset($_SESSION["signedin"]) || $_SESSION["signedin"] !== true) {
    header("location: index.php");
    exit();
}

function validate_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

if(isset($_GET["view_id"]) && !empty(trim($_GET["view_id"]))){
     $param_id = validate_input($_GET["view_id"]);
  }

  if(isset($_GET["view_date"]) && !empty(trim($_GET["view_date"]))){
     $param_date = validate_input($_GET["view_date"]);
  }

  $total_upsent = 0;
  $total_present = 0;
  $total = 0;

  require_once("../config/connect.php");

?>
<!DOCTYPE html>
<html>
<head>
  <title>view attendance register</title>
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
<div class="main">
  <table>
  <thead>
  <tr>
    <th>Date</th>
    <th>Subject Code</th>
  </tr>
  </thead>
  <tbody>
  <tr>
    <td><?php echo $param_date;?></td>
    <td><?php echo $param_id;?></td>
  </tr>
  </tbody>
  </table>
<h2>Attendance Register</h2>
<br>
  <?php

  $sql = "SELECT * FROM attendance_register WHERE register_code = ? AND register_date = ?";

    if($stmt = $conn->prepare($sql)){

      $stmt->bind_param("ss", $param_id, $param_date);

      if($stmt->execute()){

        $result = $stmt->get_result();

        if($result->num_rows > 0){

          echo "<table>";
           echo "<thead>";
             echo "<tr>";
                echo "<th>#</td>";
                echo "<th>Student No</th>";    
                echo "<th>First Name</th>";
                echo "<th>Last Name</th>";
                echo "<th>Attendance</th>";
              echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            $count = 0;
            $status = 0;
            while ( $row = $result->fetch_assoc()) {
            $count++;
            $total = $total + 1;
            echo "<tr>";
               echo "<td>".$count."</td>";
               echo "<td>".$row['register_student_no']."</td>";
               echo "<td>".$row['register_firstname']."</td>";
               echo "<td>".$row['register_lastname']."</td>";
               echo "<td>";

               $status = $row['register_status'];

               if($status == 0){
                  $total_upsent = $total_upsent + 1;
                  echo "<span class='attendance-upsent'>Absent</span>";
               }else{
                  echo "<span class='attendance-present'>Present</span>";
               }
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
      $stmt->close();
    }?>
<h4>Attendance Register Sammary</h4>
<p><small><span class="bold-text">Students</span>&nbsp;&nbsp;&nbsp;<span><?php echo $total;?></span><br>
<span class="bold-text">Presents</span>&nbsp;&nbsp;&nbsp;<span><?php echo $total_present = $total - $total_upsent;?></span><br>
<span class="bold-text">Upsents</span>&nbsp;&nbsp;&nbsp;&nbsp;<span><?php echo $total_upsent;?></span></small></p>
<h4><a class="button-back" href="all_registers.php?reg_id=<?php echo $param_id;?>">Back</a><a class="button-add" href="generate_register.php?gen_date=<?php echo $param_date;?>&gen_code=<?php echo $param_id;?>">Generate Pdf</a></h4> 
</div>
</body>
</html>

