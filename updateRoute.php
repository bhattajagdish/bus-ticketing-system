<?php 
session_start();
include("connection.php"); 
if(!isset($_SESSION['username'])){
    header("Location: adminLogin.php");  
  }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Routes Update</title>
    <link rel="stylesheet" href="css/addroute.css">
    <link rel="stylesheet" href="css/slide.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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
        <?php 
        // Fetch the route details based on the provided ID
        $id = $_GET['id'] ?? null;
        $route = null;
        if ($id) {
            $query = "SELECT * FROM `route` WHERE id = $id";
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                $route = $result->fetch_assoc();
            

            } else {
                echo '<script>alert("Route not found!"); window.location.href="ManageRoute.php";</script>';
                exit;
            }
        } else {
            echo '<script>alert("No route ID provided!"); window.location.href="ManageRoute.php";</script>';
            exit;
        }

        // Fetch all buses for the dropdown
        $buses = [];
        $query = "SELECT Bus_Name, Bus_Number FROM bus";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $buses[$row['Bus_Name']][] = $row['Bus_Number'];
        }

       if (isset($_POST['routeUpdate'])) {
    $Source = $_POST['Source'];
    $destination = $_POST['destination'];
    $bus_name = $_POST['bus_name'];
    $bus_number = $_POST['bus_number'];
    $dep_date = $_POST['departure_date'];
    $dep_time = $_POST['departure_time'];
    $total_seats = $_POST['total_seats'];
    $cost = $_POST['cost'];

    // Format departure date and time as a single datetime string
    $departure_datetime = $dep_date . ' ' . $dep_time;

    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    } else {
        // Check if the bus is already assigned to another route with overlapping times
        $stmt = $conn->prepare(
            "SELECT * FROM `route` 
             WHERE bus_number = ? 
               AND id != ? 
               AND (departure_date = ? AND departure_time = ?)"
        );
        $stmt->bind_param("siss", $bus_number, $id, $dep_date, $dep_time);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Conflict detected
            echo '<script>alert("This bus is already assigned to another route at the same time! Please choose a different bus or time.");</script>';
        } else {
            // No conflicts, proceed with updating the route
            $stmt = $conn->prepare(
                "UPDATE `route` 
                 SET Source = ?, destination = ?, bus_name = ?, bus_number = ?, departure_date = ?, departure_time = ?, total_seats = ?, cost = ? 
                 WHERE id = ?"
            );
            $stmt->bind_param("ssssssiii", $Source, $destination, $bus_name, $bus_number, $dep_date, $dep_time, $total_seats, $cost, $id);
            $stmt->execute();
            echo '<script>alert("Route updated successfully!"); window.location.href="ManageRoute.php";</script>';
        }

        $stmt->close();
    }
}

        
        // Format departure_time for display in the form
        $departure_time = isset($route['departure_time']) ? date('H:i', strtotime($route['departure_time'])) : '';


        ?>

        

        <div class="wrapper">
            <div class="registration_form">
                <div class="title">Routes Update</div>
                <form action="" method="POST" onsubmit="return validateForm()">
                    <div class="form_wrap">
                        <div class="input_wrap">
                            <label for="Source">Source</label>
                            <input type="text" id="Source" name="Source" value="<?= $route['Source'] ?>" required>
                        </div>

                        <div class="input_wrap">
                            <label for="destination">Destination</label>
                            <input type="text" id="destination" name="destination" value="<?= $route['destination'] ?>" required>
                        </div>

                        <div class="input_wrap">
                            <label for="bus_name">Bus Name</label>
                            <select id="bus_name" name="bus_name" required onchange="updateBusNumbers()">
                                <option value="">Select Bus Name</option>
                                <?php foreach (array_keys($buses) as $busName): ?>
                                    <option value="<?= $busName ?>" <?= $busName == $route['bus_name'] ? 'selected' : '' ?>><?= $busName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="input_wrap">
                            <label for="bus_number">Bus Number</label>
                            <select id="bus_number" name="bus_number" required>
                                <option value="">Select Bus Number</option>
                                <?php if ($route['bus_name'] && isset($buses[$route['bus_name']])): ?>
                                    <?php foreach ($buses[$route['bus_name']] as $busNumber): ?>
                                        <option value="<?= $busNumber ?>" <?= $busNumber == $route['bus_number'] ? 'selected' : '' ?>><?= $busNumber ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="input_wrap">
                            <label for="departure_date">Departure Date</label>
                            <input type="date" id="departure_date" name="departure_date" value="<?= $route['departure_date'] ?>" required>
                        </div>

                        <div class="input_wrap">
                            <label for="departure_time">Departure Time</label>
                            <input type="time" id="departure_time" name="departure_time" value="<?= $departure_time ?>" required>

                        </div> 
                       

                        <div class="input_wrap">
    <label for="total_seats">Total Seats</label>
    <input type="number" id="total_seats" name="total_seats" value="<?= $route['total_seats'] ?>" required>
</div>


                        <div class="input_wrap">
                            <label for="cost">Cost</label>
                            <input type="text" id="cost" name="cost" value="<?= $route['cost'] ?>" required>
                        </div>

                        <div class="input_wrap">
                            <input type="submit" value="Update Route Now" class="submit_btn" name="routeUpdate">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const buses = <?= json_encode($buses) ?>;

        function updateBusNumbers() {
            const busName = document.getElementById("bus_name").value;
            const busNumberDropdown = document.getElementById("bus_number");
            busNumberDropdown.innerHTML = '<option value="">Select Bus Number</option>';
            if (busName in buses) {
                buses[busName].forEach(busNumber => {
                    const option = document.createElement("option");
                    option.value = busNumber;
                    option.textContent = busNumber;
                    busNumberDropdown.appendChild(option);
                });
            }
        }

        function validateForm() {
            const departureDate = document.getElementById("departure_date").value;
            const departureTime = document.getElementById("departure_time").value;
            const total_seats = document.getElementById("total_seats").value;
            const cost = document.getElementById("cost").value;

            const currentDate = new Date();
            const enteredDate = new Date(departureDate + "T" + departureTime);

            if (enteredDate < currentDate) {
                alert("Departure date and time cannot be in the past!");
                return false;
            }

            if (total_seats < 0) {
                alert("Total seats cannot be negative!");
                return false;
            }

            if (cost < 0) {
                alert("Cost cannot be negative!");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
