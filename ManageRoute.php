<?php 
    session_start();
    include("connection.php");
    if(!isset($_SESSION['username'])){
        header("Location: adminLogin.php");  
      }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Routes</title>
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
            <h1>Manage Routes of Buses</h1>
        </div>

        <?php
            // Fetch data from the route table, including the bus_number column
            $sqlget = "SELECT id, Source, destination, bus_name, bus_number, departure_date, 
           DATE_FORMAT(departure_time, '%h:%i %p') AS departure_time, 
           available_seats, total_seats, cost 
           FROM route";

 

            $sqldata = mysqli_query($conn, $sqlget) or die('Error getting data');

            // Display table
            echo "<table class='table'>";
            echo "<tr>
                    <th>ID</th>
                    <th>Source</th>
                    <th>Destination</th>
                    <th>Bus Name</th>
                    <th>Bus Number</th> <!-- New column -->
                    <th>Departure Date</th>
                    <th>Departure Time</th>
                    <th> Seats</th>
                    <th>Cost</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>";

            // Loop through the rows and include the bus_number column
            while ($row = mysqli_fetch_array($sqldata, MYSQLI_ASSOC)) {
                // Calculate the percentage of available seats
                $total_seats = $row['total_seats'] ?? 0;
                $available_seats = $row['available_seats'] ?? 0;
            
                if ($total_seats > 0) {
                    $percentage = ($available_seats / $total_seats) * 100;
                } else {
                    $percentage = 0; // Avoid division by zero
                }
            
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['Source']}</td>
                        <td>{$row['destination']}</td>
                        <td>{$row['bus_name']}</td>
                        <td>{$row['bus_number']}</td>
                        <td>{$row['departure_date']}</td>
                        <td>{$row['departure_time']}</td> 
                        <td>
                            <div class='seats-info'>
                                <span>{$available_seats} Seats Available</span>
                                <div class='progress-bar'>
                                    <div class='progress' style='width: {$percentage}%;'></div>
                                </div>
                                <span>Total Seats: {$total_seats}</span>
                            </div>
                        </td>
                        <td>{$row['cost']}</td>
                        <td><a class='btn btn-update' href='updateRoute.php?id={$row['id']}'>Update</a></td>
                        <td><a class='btn btn-delete' href='deleteRoute.php?id={$row['id']}'>Delete</a></td>
                    </tr>";
            }
            
            

            echo "</table>";
        ?>

        <a href="Addroute.php">
            <button class="btn-add">Add Bus</button>
        </a>
    </div>
</body>
</html>
