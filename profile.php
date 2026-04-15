<?php
session_start();
include("connection.php");

if(!isset($_SESSION['user_id'])) {
    header("Location: user-login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $target_dir = "uploads/";
    $filename = time() . "_" . $_FILES["profile_image"]["name"];
$target_file = $target_dir . $filename;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is an image
    $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
    if ($check === false) {
        echo "<script>alert('File is not an image.'); window.location.href='profile.php';</script>";
        exit;
    }

    // Check file size (e.g., 5MB limit)
    if ($_FILES["profile_image"]["size"] > 50000000) {
        echo "<script>alert('File is too large.'); window.location.href='profile.php';</script>";
        exit;
    }

    // Allow only certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "<script>alert('Only JPG, JPEG, PNG, and GIF files are allowed.'); window.location.href='profile.php';</script>";
        exit;
    }

    // Upload the file
    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
        // Update the user's profile image in the database
        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->bind_param("si", $target_file, $user_id);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Profile image updated successfully.'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Error uploading image.'); window.location.href='profile.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Bus Ticket System</title>
    <style>
        body {
            background-image: url(image/profile.jpg);
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
        }

        .usern {
            font-size: 25px;
            font-family: Arial;
            margin-top: 20px;
            text-align: center;
            color: white;
        }

        .wrapper {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            width: 60%;
            box-shadow: -15px -15px 15px rgba(255, 255, 255, 0.2), 15px 15px 15px rgba(0, 0, 0, 0.1);
        }

        .left {
            width: 30%;
            background: #1a5be8f5;
            padding: 30px 25px;
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
            text-align: center;
            color: #fff;
            border-radius: 15px;
        }

        .left img {
            border-radius: 50%;
            margin-bottom: 10px;
            width: 150px; 
            height: 150px; 
        }

        .right {
            width: 70%;
            padding: 40px 30px;
            color: #fff;
        }

        hr {
            border: 1px solid black;
            width: 50%;
        }
        p{
            color:Black;
        }

        h3{
            color:Black;
        }
        

        .btn3 {
            padding: 10px;
            width: 20%;
            background-color:yellow;
            border: none;
            color: Black;
            border-radius: 7px;
            margin-top: 10px;
        }


         .btn2 {
            padding: 10px;
            width: 20%;
            background-color: #66ee0b;
            border: none;
            color: Black;
            border-radius: 7px;
            margin-top: 10px;
        }
         .btn1 {
            padding: 10px;
            width: 20%;
            background-color: #f61408;
            border: none;
            color: Black;
            border-radius: 7px;
            margin-top: 10px;
        }

        
        .btn3:hover {
            background-color: orange;
            cursor: pointer;
        }
        
        .btn2:hover {
            background-color: rgba(163, 240, 30, 0.96);
            cursor: pointer;
        }
        
        .btn1:hover {
            background-color: rgb(222, 52, 5);
            cursor: pointer;
        }
        button{
            padding: 10px;
            width: 50%;
            background-color: #F9522E;
            border: none;
            color: white;
            border-radius: 7px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="usern">Hello !!! <?php echo $user_data['username']; ?></div>
    <div class="wrapper">
        <div class="left">
            <!-- Display the current profile image -->
            <img src="<?= !empty($user_data['profile_image']) ? $user_data['profile_image'] : 'image/img.jpg'; ?>" alt="user">
            <!-- Form for uploading a new profile image -->
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_image" accept="image/*" style="display: none;" id="image-upload">
                <button type="button" onclick="document.getElementById('image-upload').click()">Upload Image</button>
                <button type="submit">Save Image</button>
            </form>
            <a href="userHome.php"><button>Home</button></a>
        </div>
        <div class="right">
            <h3>Account Information</h3>
            <p>Username: <?php echo $user_data['username']; ?></p>
            <p>Email: <?php echo $user_data['email']; ?></p>
            <p>First Name: <?php echo $user_data['First_Name']; ?></p>
            <p>Last Name: <?php echo $user_data['Last_Name']; ?></p>
            <h3>Logout & Security</h3>
            <a href="updateProfile.php?id=<?php echo $user_data['id']; ?>"><button class="btn2">Update</button></a>
            <a href="logout.php"><button class="btn3">Logout</button></a>
            <a href="deleteProfile.php?id=<?php echo $user_data['id']; ?>"><button class="btn1">Delete</button></a>
        </div>
    </div>
</body>
</html>