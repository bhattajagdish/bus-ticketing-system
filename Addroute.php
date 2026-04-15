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
    <title>Routes Adding</title>
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
        // Fetch all buses for the dropdown
        $buses = [];
        $query = "SELECT Bus_Name, Bus_Number FROM bus";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $buses[$row['Bus_Name']][] = $row['Bus_Number'];
        }

        if (isset($_POST['routeAdd'])) {
            $Source = $_POST['Source'];
            $destination = $_POST['destination'];
            $bus_name = $_POST['bus_name'];
            $bus_number = $_POST['bus_number'];
            $dep_date = $_POST['departure_date'];
            $dep_time = $_POST['departure_time'];
            $total_seats = $_POST['total_seats'];
            $cost = $_POST['cost'];
        
            if ($conn->connect_error) {
                die('Connection Failed: ' . $conn->connect_error);
            } else {
                // Query to check for conflicts
                $conflict_query = $conn->prepare(
                    "SELECT * FROM route 
                    WHERE bus_number = ? 
                    AND departure_date = ? 
                    AND departure_time = ?"
                );
                $conflict_query->bind_param("sss", $bus_number, $dep_date, $dep_time);
                $conflict_query->execute();
                $conflict_result = $conflict_query->get_result();
        
                if ($conflict_result->num_rows > 0) {
                    // Conflict detected
                    echo '<script>alert("Conflict detected: This bus is already assigned to a route at the selected date and time.");</script>';
                } else {
                    // No conflict, proceed to add the route
                    $stmt = $conn->prepare(
                        "INSERT INTO route(Source, destination, bus_name, bus_number, departure_date, departure_time, available_seats, total_seats, cost) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                    );
                    $stmt->bind_param("ssssssiii", $Source, $destination, $bus_name, $bus_number, $dep_date, $dep_time, $total_seats, $total_seats, $cost);
                    $stmt->execute();
        
                    echo '<script>alert("Route added successfully.");</script>';
                    $stmt->close();
                }
                $conflict_query->close();
                $conn->close();
            }
        }
        
          
        ?>

        <div class="wrapper">
            <div class="registration_form">
                <div class="title">Routes Adding</div>
                <form action="#" method="POST">
                    <div class="form_wrap">
                        <div class="input_wrap">
                            <label for="Source">Source</label>
                            <input type="text" id="Source" name="Source" placeholder="Source" required>
                        </div>

                        <div class="input_wrap">
                            <label for="destination">Destination</label>
                            <input type="text" id="destination" name="destination" placeholder="Destination" required>
                        </div>

                        <div class="input_wrap">
                            <label for="bus_name">Bus Name</label>
                            <select id="bus_name" name="bus_name" required onchange="updateBusNumbers()">
                                <option value="">Select Bus Name</option>
                                <?php foreach (array_keys($buses) as $busName): ?>
                                    <option value="<?= $busName ?>"><?= $busName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="input_wrap">
                            <label for="bus_number">Bus Number</label>
                            <select id="bus_number" name="bus_number" required>
                                <option value="">Select Bus Number</option>
                            </select>
                        </div>

                        <div class="input_wrap">
                            <label for="departure_date">Departure Date</label>
                            <input type="date" id="departure_date" name="departure_date" class="idclass" required>
                        </div>

                        <div class="input_wrap">
                            <label for="departure_time">Departure Time</label>
                            <input type="time" id="departure_time" name="departure_time" class="idclass" required>
                        </div>

                        <div class="input_wrap">
    <label for="total_seats">Total Seats</label>
    <input type="number" id="total_seats" name="total_seats" placeholder="Number of Total Seats" class="idclass" required>
</div>


                        <div class="input_wrap">
                            <label for="cost">Cost</label>
                            <input type="text" id="cost" name="cost" placeholder="Cost" class="idclass" required>
                        </div>

                        <div class="input_wrap">
                            <input type="submit" value="Add Route Now" class="submit_btn" name="routeAdd">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    const buses = <?= json_encode($buses) ?>; // PHP array to JavaScript object

    function updateBusNumbers() {
        const busName = document.getElementById("bus_name").value;
        const busNumberDropdown = document.getElementById("bus_number");

        // Clear existing options
        busNumberDropdown.innerHTML = '<option value="">Select Bus Number</option>';

        // Populate new options based on the selected bus name
        if (busName in buses) {
            buses[busName].forEach(busNumber => {
                const option = document.createElement("option");
                option.value = busNumber;
                option.textContent = busNumber;
                busNumberDropdown.appendChild(option);
            });
        }
    }

    document.querySelector("form").addEventListener("submit", function(event) {
    // Get form field values
    const departureDate = document.getElementById("departure_date").value;
    const departureTime = document.getElementById("departure_time").value;
    const totalSeats = document.getElementById("total_seats").value;
    const cost = document.getElementById("cost").value;

    // Get current date and time
    const now = new Date();
    const departureDateTime = new Date(`${departureDate}T${departureTime}`);

    // Validation logic
    if (departureDate === "" || departureTime === "") {
        alert("Please select both departure date and time.");
        event.preventDefault();
        return;
    }

    if (departureDateTime < now) {
        alert("Departure date and time cannot be in the past.");
        event.preventDefault();
        return;
    }

    if (totalSeats <= 0) {
        alert("Total seats must be greater than zero.");
        event.preventDefault();
        return;
    }

    if (cost <= 0) {
        alert("Cost must be greater than zero.");
        event.preventDefault();
        return;
    }
});


</script>

</body>
</html>
