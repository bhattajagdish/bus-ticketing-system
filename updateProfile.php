<?php 
session_start();
include("connection.php");


// Check if the `id` is provided in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch user data from the database
    $query = "SELECT * FROM users WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result); // Get user data
    } else {
        echo "<script>alert('User not found!'); window.location.href='profile.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Invalid user ID!'); window.location.href='profile.php';</script>";
    exit;
}



if (isset($_POST['updateprofile'])) {
    $id = $_POST['id'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $username = $_POST['user_name'];
    $password = $_POST['password'];

    $query = "UPDATE `users` SET First_Name='$fname', Last_Name='$lname', email='$email', 
              username='$username', password='$password' WHERE id=$id";

    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        echo ("<script>
            window.alert('Successfully updated your profile!');
            window.location.href='profile.php';
        </script>");
    } else {
        echo '<script>alert("Profile not updated!")</script>';
    }
}
?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Update Profile</title>
    <link rel="stylesheet" href="css/addroute.css">
   

</head>
<body>

<div class="wrapper">
    <div class="registration_form">
        <div class="title">Update Your Profile</div>
        <form action="#" method="POST">
            <div class="form_wrap">
                <div class="input_grp">
                    <div class="input_wrap">
                        <label for="title">Id</label>
                        <input type="number" id="title" name="id" class="idclass" 
                               value="<?php echo $user_data['id']; ?>" readonly>
                    </div>
                    <div class="input_wrap">
                        <label for="fname">First Name</label>
                        <input type="text" id="fname" name="fname" 
                               value="<?php echo $user_data['First_Name']; ?>" placeholder="First Name" required>
                    </div>
                    <div class="input_wrap">
                        <label for="lname">Last Name</label>
                        <input type="text" id="lname" name="lname" 
                               value="<?php echo $user_data['Last_Name']; ?>" placeholder="Last Name">
                    </div>
                    <div class="input_wrap">
                        <label for="email">Email Address</label>
                        <input type="text" id="email" name="email" 
                               value="<?php echo $user_data['email']; ?>" placeholder="E-mail" required>
                    </div>
                    <div class="input_wrap">
                        <label for="user_name">Username</label>
                        <input type="text" id="user_name" name="user_name" 
                               value="<?php echo $user_data['username']; ?>" placeholder="Username" required>
                    </div>
                    <div class="input_wrap">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" 
                               value="<?php echo $user_data['password']; ?>" placeholder="Password" required>
                    </div>
                    <div class="input_wrap">
                        <input type="submit" value="Update Now" class="submit_btn" name="updateprofile">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

</body>
</html>
