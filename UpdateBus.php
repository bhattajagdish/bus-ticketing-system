<?php
session_start();
include("connection.php");
if(!isset($_SESSION['username'])){
    header("Location: adminLogin.php");  
  }

// Check if the `id` is provided in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the data for the selected bus
    $query = "SELECT * FROM `bus` WHERE id = $id";
    $result = mysqli_query($conn, $query);

    // Check if the query returned any data
    if (mysqli_num_rows($result) > 0) {
        $busData = mysqli_fetch_assoc($result);
    } else {
        echo ("<script>
            window.alert('Bus not found!');
            window.location.href='ManageBuses.php';
            </script>");
    }
} else {
    echo ("<script>
        window.alert('No Bus ID provided!');
        window.location.href='ManageBuses.php';
        </script>");
}

// Handle the update form submission
if (isset($_POST['BusUpdate'])) {
    $id = $_POST['id'];
    $busName = $_POST['bus_name'];
    $tel = $_POST['tel'];
    $busNumber = $_POST['bus_number'];

    $query = "UPDATE `bus` SET Bus_Name='$busName', Tel='$tel', Bus_Number='$busNumber' WHERE id=$id";

    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        echo ("<script>
            window.alert('Successfully updated the bus!');
            window.location.href='ManageBuses.php';
            </script>");
    } else {
        echo '<script>alert("Update failed!")</script>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Buses</title>
    <link rel="stylesheet" href="css/addroute.css">
    <link rel="stylesheet" href="css/slide.css">
</head>
<body>
    <div class="sidebar">
        <header>
<img src="image/AdminProfile.jpg" alt="Admin">
            <p><?php echo $_SESSION['username']; ?></p>
        </header>
        <ul>
            <li><a href="adminDash.php">Dashboard</a></li>
            <li><a href="ManageRoute.php">Manage Routes</a></li>
            <li><a href="ManageBuses.php">Manage Buses</a></li>
            <li><a href="ManageBooking.php">Manage Bookings</a></li>
             <li><a href="ViewMessages.php">Messages</a></li>
            <li><a href="adminLogout.php">Logout</a></li>
        </ul>
    </div>

    <div class="sidebar2">
        <div class="wrapper">
            <div class="registration_form">
                <div class="title">Buses Update/Edit</div>

                <form action="#" method="POST">
                    <div class="form_wrap">
                        <div class="input_wrap">
                            <label for="id">ID</label>
                            <input type="number" id="id" name="id" value="<?php echo $busData['id']; ?>" readonly>
                        </div>

                        <div class="input_wrap">
                            <label for="bus_name">Bus Name</label>
                            <input type="text" id="bus_name" name="bus_name" value="<?php echo $busData['Bus_Name']; ?>" required>
                        </div>

                        <div class="input_wrap">
                            <label for="tel">Telephone</label>
                            <input type="text" id="tel" name="tel" value="<?php echo $busData['Tel']; ?>" required>
                        </div>

                        <div class="input_wrap">
                            <label for="bus_number">Bus Number Plate</label>
                            <input type="text" id="bus_number" name="bus_number" 
                            pattern="[A-Z]{1} [A-Z]{2} \d{4}" title="Format: B DE 1234" value="<?php echo $busData['Bus_Number']; ?>" required>
                        </div>

                        <div class="input_wrap">
                            <input type="submit" value="Update Bus Now" class="submit_btn" name="BusUpdate">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
