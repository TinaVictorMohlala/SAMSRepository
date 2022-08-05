<?php

session_start();

if (!isset($_SESSION["signedin"]) || $_SESSION["signedin"] !== true) {
    header("location: index.php");
    exit();
}


?>
<!DOCTYPE html>
<html>
<head>
	<title>Profile</title>
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="nav-bar">
<ul>
  <li><a class="active logo" href="#home">SAMS</a></li>
  <li style="float:right"><a class="button-signout" href="../config/logout.php">Logout</a></li>
  <li style="float:right"><a href="../student_info/all_students.php">All Students</a></li>
</ul>
</div>
<div class="main">
 <h4>Personal Infomation</h4>
 <img src="../images/profile.png" alt="profile" width="50">
 <table>
  <thead>
  <tr>
    <th>First name</th>
    <th>Last name</th>
    <th>Username</th>
  </tr>
  </thead>
  <tbody>
  <tr>
    <td><?php echo htmlspecialchars($_SESSION["firstname"]);?></td>
    <td><?php echo htmlspecialchars($_SESSION["lastname"]);?></td>
    <td><?php echo htmlspecialchars($_SESSION["username"]);?></td>
  </tr>
  </tbody>
  </table>

 <h2>Subjects Details</h2>
  <p><a class="button-add" href="../course_info/add_course.php">Add Subject</a></p>
<?php

require_once("../config/connect.php");

$course_instructor = $_SESSION["username"];

$sql = "SELECT * FROM courses WHERE course_instructor = ?";

if($stmt = $conn->prepare($sql)){

    $stmt->bind_param("s", $course_instructor);

    if($stmt->execute()){

    	$result = $stmt->get_result();

        if($result->num_rows > 0){

        echo "<table>";
		   echo "<thead>";
		      echo "<tr>";
		         echo "<th>#</th>";
		         echo "<th>Subject Code</th>";
		         echo "<th>Subject Name</th>";
		         echo "<th>Action</th>";
		      echo "<tr>";
		   echo "</thead>";
		   echo "<tbody>";
		   
           $count = 0;

          while ( $row = $result->fetch_assoc()) {

          $count++;
		      echo "<tr>";
		        echo "<td>".$count."</td>";
		        echo "<td>".$row['course_code']."</td>";
		        echo "<td>".$row['course_name']."</td>";
		        echo "<td>";
		           echo "<a class='button-update' href='../course_info/update_course.php?upd_id=".$row['course_id']."'>Update</a>";
		           echo "<a class='button-delete' href='../course_info/delete_course.php?del_id=".$row['course_id']."'>Delete</a>";
		           echo "<a class='button-view' href='../register_info/all_registers.php?reg_id=".$row['course_code']."'>view</a>";
		        echo "</td>";
		      echo "</tr>";
          
          }  
        }else{
          echo "<p>No records were found</p>";
        }
      }else{
        echo "Failed to execute ";
      }
      $stmt->close();
    }

?> 
</div>
</body>
</html>

