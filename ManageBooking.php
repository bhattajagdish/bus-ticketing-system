<?php 
session_start();
include("connection.php");

if(!isset($_SESSION['username'])){
    header("Location: adminLogin.php");  
}

// SQL query to fetch data
$sql = "SELECT * FROM bookings";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Management</title>
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

<div class="content">
    <div class="header">
        <h1>Manage Bookings</h1>
    </div>
    <table class='table'>
        <tr>
            <th>ID</th>
            <th>Bus Number</th>
            <th>Route</th>
            <th>Seat Number</th>
            <th>Trip Date</th>
            <th>Passengers</th>
            <th>Price</th>
            <th>Status</th>
            <th>Payment Status</th>
            <th>Boarding Point</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Mobile</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row["id"] . "</td>
                        <td>" . $row["bus_number"] . "</td>
                        <td>" . $row["route"] . "</td>
                        <td>" . $row["seat_number"] . "</td>
                        <td>" . date("Y-m-d H:i", strtotime($row["trip_date"])) . "</td>
                        <td>" . $row["passengers"] . "</td>
                        <td>NPR " . number_format($row["price"], 2) . "</td>
                        <td>" . $row["status"] . "</td>
                        <td>" . $row["payment_status"] . "</td>
                        <td>" . ($row["boarding_point"] ?? 'N/A') . "</td>
                        <td>" . $row["full_name"] . "</td>
                        <td>" . $row["email"] . "</td>
                        <td>" . ($row["mobile_number"] ?? 'N/A') . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='13'>No records found</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</div>
</body>
</html>