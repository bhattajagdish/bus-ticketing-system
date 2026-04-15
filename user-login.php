<?php 
    session_start();
    
    include("connection.php"); 


    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        // Capture form data
        $user_name = $_POST['user_name'];
        $password = $_POST['password'];

        if (!empty($user_name) && !empty($password) && !is_numeric($user_name)) {
           
            $query = "SELECT * FROM users WHERE username = '$user_name' LIMIT 1";
            $result = mysqli_query($conn, $query);

            if ($result) {
                if ($result && mysqli_num_rows($result) > 0) {
                    $user_data = mysqli_fetch_assoc($result);

                    if (password_verify($password, $user_data['password'])) {
                        $_SESSION['user_id'] = $user_data['id']; 
                        header("Location: userHome.php");
                        die;
                    }
                    
                }
            }
            echo "Wrong username or password!";
        } else {
            echo "Wrong username or password!";
        }
    }

      
   include("nav.php");
 ?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>User Login</title>
    <link rel="stylesheet" href="css/login.css">


</head>
<body class="user-login-bg">
  
    <div class="login-box">
        
        <h1> User Login </h1>
        <form method="post">
            <p>Username</p>
            <input  type="text" name="user_name" placeholder="Enter Username" required >
            <p>Password</p>
            <input type="password" name="password" placeholder="Enter Password" required>
            <input type="submit" name="login" value="Login">
            <a href="registration.php" class="sign_up">Sign Up</a>&nbsp;&nbsp;&nbsp;
            <a href="forget-password.php">Forget Password?</a>
        </form>
    </div>
</body>
</html> 