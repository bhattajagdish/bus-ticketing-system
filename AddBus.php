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
    <title>Bus Adding</title>
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
        <?php 
        if (isset($_POST['AddBus'])) {
            $nameOFbus = $_POST['bus_name'];
            $tel = $_POST['tel'];
            $bus_number = $_POST['bus_number'];

            if ($conn->connect_error) {
                die('Connection Failed: ' . $conn->connect_error);
            } else {
                // Check if the bus number already exists
                $checkQuery = "SELECT * FROM bus WHERE Bus_Number = ?";
                $stmt = $conn->prepare($checkQuery);
                $stmt->bind_param("s", $bus_number);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Bus number already exists
                    echo ("<script>
                        window.alert('Bus Number already exists! Please use a different number.');
                        window.location.href='AddBus.php';
                    </script>");
                } else {
                    // Insert new bus
                    $insertQuery = $conn->prepare("INSERT INTO bus (Bus_Name, Tel, Bus_Number) VALUES (?, ?, ?)");
                    $insertQuery->bind_param("sss", $nameOFbus, $tel, $bus_number);
                    $insertQuery->execute();

                    echo ("<script>
                        window.alert('Successfully Bus Added!!!');
                        window.location.href='ManageBuses.php';
                    </script>");

                    $insertQuery->close();
                }

                $stmt->close();
                $conn->close();
            }
        }
        ?>

        <div class="wrapper">
            <div class="registration_form">
                <div class="title">Bus Adding</div>
                <form action="#" method="POST">
                    <div class="form_wrap">
                        <div class="input_wrap">
                            <label for="bus_name">Bus Name</label>
                            <input type="text" id="bus_name" name="bus_name" placeholder="Bus Name" required>
                        </div>
                        <div class="input_wrap">
                            <label for="tel">Telephone</label>
                            <input type="text" id="tel" name="tel" placeholder="Tel" required>
                        </div>
                        <div class="input_wrap">
                            <label for="bus_number">Bus Number</label>
                            <input type="text" id="bus_number" name="bus_number" placeholder="e.g., B DE 1234" pattern="[A-Z]{1} [A-Z]{2} \d{4}" title="Format: B DE 1234" required>
                        </div>
                        <div class="input_wrap">
                            <input type="submit" value="Add Bus Now" class="submit_btn" name="AddBus">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
