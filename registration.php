<?php include("nav.php"); ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="css/register.css">
    <!-- Add Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="registration-bg">
    <?php
    session_start();
    include("connection.php");

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $fname = $_POST['first_name'];
        $lname = $_POST['last_name'];
        $email = $_POST['email'];
        $user_name = $_POST['username'];
        $password = $_POST['password'];
     

        $passwordPattern="/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_])/";
        
        if (!empty($user_name) && !empty($password) && !is_numeric($user_name)) {
            if (strlen($password) < 8 || strlen($password) > 20) {
                echo "<script>alert('Password must be between 8 and 20 characters.');</script>";
            } elseif (!preg_match($passwordPattern, $password)) {
                echo "<script>alert('Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.');</script>";
            
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

$query = "INSERT INTO users (First_Name, Last_Name,email, username, password) VALUES ('$fname', '$lname','$email', '$user_name', '$hashed_password')";
               

                if (mysqli_query($conn, $query)) {
                    echo ("<script>
                        window.alert('Successfully signed up!');
                        window.location.href='user-login.php';
                    </script>");
                    exit();
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            }
        } else {
            echo "<script>alert('Please enter valid information!');</script>";
        }
    }
    ?>

    <section class="signup-section">
        <div class="signup-container">
            <h2>SIGN UP FOR BUS TICKET</h2>
            <form action="#" method="post" class="signup-form">
                <div class="input-row">
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" placeholder="First Name" name="first_name" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" placeholder="Last Name" name="last_name" required>
                    </div>
                </div>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" placeholder="Email Address" name="email" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" placeholder="Username" name="username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" placeholder="Password" name="password" required>
                </div>
                
                <button type="submit">REGISTER NOW</button>
                <p>Already have an account? <a href="user-login.php">Login</a></p>
            </form>
        </div>
    </section>
</body>
</html>