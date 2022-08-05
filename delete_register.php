
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

  $error = "";

  if(isset($_GET["del_id"]) && !empty($_GET["del_id"])){
     $id = validate_input($_GET["del_id"]);
  }

  if(isset($_GET["del_date"]) && !empty($_GET["del_date"])){
     $date = validate_input($_GET["del_date"]);
  }

    if(!empty($id) && !empty($date)){

        $sql = "DELETE FROM attendance_register WHERE register_code = ? AND register_date = ?";
        if($stmt = $conn->prepare($sql)){

            $stmt->bind_param("ss", $param_id, $param_date);
            $param_id = $id;
            $param_date = $date;
          
            if($stmt->execute()){
               
              $error = "<h2 class='success-message'>Register Deleted.</h3><br>";
              header("Refresh: 5; all_registers.php?reg_id=$id");

            }else{
              $error = "<h2 class='error-message'>Failed to delete. Please try again later.</h2>";
            }
        }

        $stmt->close();
        $conn->close();
    }

?>

<!DOCTYPE html>
<html>
<head>
  <title>Delete attendance register</title>
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
  <div style="text-align: center;"><?php echo $error;?></div>       
</div>
</body>
</html>

