<?php 
    session_start();
    include("connection.php");

    if(!isset($_SESSION['username'])){
        header("Location: adminLogin.php");  
        exit();
    }

    // Fetch total bookings
    $bookingQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings");
    $bookingData = mysqli_fetch_assoc($bookingQuery);
    $totalBookings = $bookingData['total'];

    // Fetch total buses
    $busQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM bus");
    $busData = mysqli_fetch_assoc($busQuery);
    $totalBuses = $busData['total'];

    // Fetch total routes
    $routeQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM route");
    $routeData = mysqli_fetch_assoc($routeQuery);
    $totalRoutes = $routeData['total'];

    // Fetch total seats
    $seatsQuery = mysqli_query($conn, "SELECT SUM(total_seats) AS total FROM route");
    $seatsData = mysqli_fetch_assoc($seatsQuery);
    $totalSeats = $seatsData['total'];

    // Fetch total customers
    $customerQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users ");
    $customerData = mysqli_fetch_assoc($customerQuery);
    $totalCustomers = $customerData['total'];

    // Fetch total admins
    $adminQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM admin");
    $adminData = mysqli_fetch_assoc($adminQuery);
    $totalAdmins = $adminData['total'];

    // Fetch total earnings
    $earningsQuery = mysqli_query($conn, "SELECT SUM(price) AS total FROM bookings");
    $earningsData = mysqli_fetch_assoc($earningsQuery);
    $totalEarnings = $earningsData['total'];

    // Fetch total contact messages
    $messagesQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM contact");
    $messagesData = mysqli_fetch_assoc($messagesQuery);
    $totalMessages = $messagesData['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        <h1>Welcome, <span><?php echo $_SESSION['username']; ?></span></h1>
        <div class="dashboard-cards">
            <div class="card bookings">
                <h3>Bookings</h3>
                <p>Total Bookings</p>
                <span><?php echo $totalBookings; ?></span>
                <a href="ManageBooking.php">View More</a>
            </div>
            <div class="card buses">
                <h3>Buses</h3>
                <p>Total Buses</p>
                <span><?php echo $totalBuses; ?></span>
                <a href="ManageBuses.php">View More</a>
            </div>
            <div class="card routes">
                <h3>Routes</h3>
                <p>Total Routes</p>
                <span><?php echo $totalRoutes; ?></span>
                <a href="ManageRoute.php">View More</a>
            </div>
            <div class="card seats">
                <h3>Seats</h3>
                <p>Total Seats</p>
                <span><?php echo $totalSeats; ?></span>
                <a href="#">View More</a>
            </div>
            <div class="card customers">
                <h3>Customers</h3>
                <p>Total Customers</p>
                <span><?php echo $totalCustomers; ?></span>
                <a href="#">View More</a>
            </div>
            <div class="card admins">
                <h3>Admins</h3>
                <p>Total Admins</p>
                <span><?php echo $totalAdmins; ?></span>
                <a href="#">View More</a>
            </div>
            <div class="card earnings">
                <h3>Earnings</h3>
                <p>Total Earnings</p>
                <span><?php echo $totalEarnings; ?></span>
                <a href="#">View More</a>
            </div>
            <div class="card messages">
                <h3>Messages</h3>
                <p>Contact Messages</p>
                <span><?php echo $totalMessages; ?></span>
                <a href="ViewMessages.php">View More</a>
            </div>
        </div>
    </div>


</body>
</html>
