<?php

require_once("../config/connect.php");

 function validate_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
 $error = $firstname = $lastname = $password = $confirm_password = $username = $param_username = "";
 $firstname_error = $lastname_error = $password_error = $confirm_password_error = $username_error = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){

       
       if(empty($_POST['firstname'])) {
            $firstname_error = "<span class='error-message'>First Name is required.</span><br>";
       } else {
         $firstname = validate_input($_POST["firstname"]);
         // check if firstname only contains letters and whitespace
         if (!preg_match("/^[a-zA-Z ]*$/", $firstname)) {
            $firstname_error = "<span class='error-message'>Only letters and white space allowed.</span><br>";
         }
       }

       if(empty($_POST['lastname'])) {
           $lastname_error = "<span class='error-message'>Last Name is required.</span><br>";
       } else {
         $lastname = validate_input($_POST["lastname"]);
         // check if lastname only contains letters and whitespace
         if(!preg_match("/^[a-zA-Z ]*$/", $lastname)) {
            $lastname_error = "<span class='error-message'>Only letters and white space allowed.</span><br>";
         }
       }

       if(empty($_POST['username'])) {
          $username_error = "<span class='error-message'>Username is required.</span><br>";
       } else {
          
          $sql = "SELECT account_id FROM account_details WHERE account_username = ?";

          if($stmt = $conn->prepare($sql)){


            $stmt->bind_param("s", $param_username);

            $param_username = validate_input($_POST["username"]);
            
            if($stmt->execute()){

              $stmt->store_result();
              if($stmt->num_rows == 1){
                 $username_error = "<span class='error-message'>This Username is already taken.</span><br>";
              }else{
                
                 $username = validate_input($_POST["username"]);
              }
          }else{
            echo "string";
          }
         
           $stmt->close();
          }
       }

       if(empty($_POST['password'])) {
           $password_error = "<span class='error-message'>Password is required.</span><br>";
       } else {
          $password = validate_input($_POST["password"]);
           if(strlen($password) >= 8) {  
         }else{
           $password_error = "<span class='error-message'>Password must be atleast 8 characters long.</span><br>";
         }
       }

       if(empty($_POST['confirm_password'])) {
          $confirm_password_error = "<span class='error-message'>Confirm Password is required.</span><br>";
       } else {
          $confirm_password = validate_input($_POST["confirm_password"]);
          if(strlen($confirm_password) >= 8) {
            
         }else{
           $confirm_password_error = "<span class='error-message'>Password must be atleast 8 characters long.</span><br>";
         }
       }

       if($confirm_password != $password){
          $confirm_password_error = "<span class='error-message'>Passwords do not match.</span><br>";
       }else{
          $hash_password = password_hash($password, PASSWORD_DEFAULT);
       }

       if(empty($firstname_error) && empty($lastname_error) && empty($username_error) 
        && empty($password_error) && empty($confirm_password_error)){

        $sql = "INSERT INTO account_details (account_firstname, account_lastname, account_username, account_password) VALUES (?, ?, ?, ?)"; 
  
        if($stmt = $conn->prepare($sql)){ 
          $stmt->bind_param("ssss", $firstname, $lastname, $username, $hash_password);
          if($stmt->execute()){
       
          $error = "<span class='success-message'>Account Created Successfully.</span><br>";

          $reload = $_SERVER['PHP_SELF'];
          
          header("Refresh: 5; $reload");

          
          
          }else{
          $error = "<span class='success-message'>Failed to Register Account.</span><br>";
          }
          $stmt->close();
        }
       }

       
       $conn->close();
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="signup-form">
<h2>Create New Account</h2>
<span class="error"><?php echo $error;?></span><br>
 <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <label>First Name<span style="color:red;">&nbsp*</span></label>
  <input type="text" name="firstname" value="<?php echo $firstname;?>"><br>
  <div><?php echo $firstname_error;?></div>
  <label>Last Name<span style="color:red;">&nbsp*</span></label>
  <input type="text" name="lastname" value="<?php echo $lastname;?>"><br>
  <div><?php echo $lastname_error;?></div>
  <label>Username<span style="color:red;">&nbsp*</span></label>
  <input type="text" name="username" value="<?php echo $username;?>"><br>
  <div><?php echo $username_error;?></div>
  <label>Password<span style="color:red;">&nbsp*</span></label>
  <input type="text" name="password" value="<?php echo $password;?>"><br>
  <div><?php echo $password_error;?></div>
  <label>Confirm Password<span style="color:red;">&nbsp*</span></label>
  <input type="text" name="confirm_password" value="<?php echo $confirm_password;?>"><br>
  <div><?php echo $confirm_password_error;?></div>
  <input type="submit" name="register" value="Register">
</form> 
<h4>Have an account?<a href="index.php">Login</a><h4> 
</di>
</body>
</html>