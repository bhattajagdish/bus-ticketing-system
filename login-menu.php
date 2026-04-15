
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Menu</title>
    <link rel="stylesheet" href="css/login-menu.css">
   
</head>
<body class="login-menu-bg">

<?php include("nav.php");
             ?>
    <!-- Background Section -->
    <div class="login-container">
        <!-- Title -->
        <h1 class="login-title">LOGIN MENU</h1>
        
        <!-- Login Options -->
        <div class="login-options">
            <div class="login-card user-login">
                <a href="user-login.php">User Login</a>
            </div>
            <div class="login-card admin-login">
                <a href="adminLogin.php">Admin Login</a>
            </div>
        </div>
    </div>
</body>
</html>

