<?php 
    session_start();
    include("connection.php"); 

    if (isset($_POST['login'])) {
        $username = $_POST['Admin_username'];
        $password = $_POST['Admin_password'];

        $passwordPattern="/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_])/";
       
        if (strlen($password) < 8 || strlen($password) > 20) {
            echo '<script>alert("Password must be between 8 and 20 characters.")</script>';
        }

        elseif (!preg_match($passwordPattern, $password)) {
            echo '<script>alert("Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 special character, and 1 number.")</script>';
        }
        else {
           
            $query = "SELECT * FROM `admin` WHERE username='$username' AND password='$password'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 1) {
                $_SESSION['username'] = $username;
                header("Location: adminDash.php");
            } else {
                echo '<script>alert("Incorrect username or password!")</script>';
            }
        }
    }

    include("nav.php"); 

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body class="admin-login-bg">
  
   
    <!-- Login Section -->
    <div class="login-box">
        <img src="image/AdminProfile.jpg" class="avatar">
        <h1>Admin Login</h1>
        
        <form method="POST">
            <p>Username</p>
            <input type="text" name="Admin_username" placeholder="Enter AdminName">
            <p>Password</p>
            <input type="password" name="Admin_password" placeholder="Enter Password">
            <input type="submit" name="login" value="Login">
        </form>
    </div>

</body>
</html>
