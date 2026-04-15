<?php 
session_start();
include("connection.php");



// Check authentication
if(!isset($_SESSION['user_id'])) {
    header("Location: user-login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_bus'])) {
    $from = $_POST['from'];
    $to = $_POST['to'];
    $date = $_POST['date'];

    // Validate date
    if (strtotime($date) >= strtotime(date('Y-m-d'))) {
       $query = "SELECT * FROM route
          WHERE Source='$from'
          AND destination='$to'
          AND departure_date='$date'
          AND CONCAT(departure_date, ' ', departure_time) > NOW()";

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $_SESSION['search_results'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
            header("Location: results.php");
            exit();
        }elseif ($from === $to) {
            echo ("<script>
                window.alert('Source and destination cannot be the same. Please select different locations.');
                window.location.href='userHome.php';
            </script>");
    }
        echo ("<script >
        window.alert('Sorry, no buses found for the selected date. Please choose a different date.');
        window.location.href='userHome.php';
    </script>");

    } 
}
include("userNav.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Ticket Search</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('image/userhome.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: white;
            padding-top: 80px;
            min-height: 100vh;
        }

        .container {
            max-width: 500px;
            margin: 70px auto;
            padding: 35px;
            background: rgba(235, 213, 213, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            color: black;
            margin-bottom: 20px;
            font-size: 2em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .input-group {
            margin-bottom: 15px;
            position: relative;
            color:black;

        }

        label {
            display: block;
            margin-bottom: 6px;
            color: rgb(16, 146, 232);
            font-weight: 500;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgb(6, 148, 243);
            z-index: 1;
        }

        select, input[type="date"] {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 2px solid rgba(18, 159, 225, 0.92);
            border-radius: 8px;
            font-size: 16px;
            background: rgba(245, 231, 231, 0.05);
            color:black;
            transition: all 0.3s ease;
        }

        select:focus, input[type="date"]:focus {
            outline: none;
            color: rgb(6, 148, 243);
            box-shadow: 0 0 15px rgba(137, 207, 240, 0.2);
        }

        /* Style dropdown option text for the specific select elements */
        select#from, select#to {
            color: black; /* selected value color when closed */
        }

        select#from option,
        select#to option {
            color: #000; /* option text color in dropdown */
            background-color: white; /* option background for readability */
        }
        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #6ac3ec 0%, #1e90ff 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
        }

        button:hover {
            background: linear-gradient(135deg, #1e90ff 0%, #89cff0 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 144, 255, 0.4);
        }

        button i {
            margin-right: 10px;
        }

      

        /* Date picker custom icon */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .container {
                margin: 15px;
                padding: 25px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-bus"></i> Find Your Journey</h2>
    <form method="POST">
        <div class="input-group">
            <label>From</label>
            <div class="input-icon">
                <i class="fas fa-map-marker-alt"></i>
                <select name="from" id="from" required>
                    <option value="">Select Source</option>
                    <option value="Kathmandu">Kathmandu</option>
                    <option value="Pokhara">Pokhara</option>
                    <option value="Attariya">Attariya</option>
                    <option value="Dhangadhi">Dhangadhi</option>
                </select>
            </div>
        </div>

        <div class="input-group">
            <label>To</label>
            <div class="input-icon">
                <i class="fas fa-map-marker-alt"></i>
                <select name="to" id="to" required>
                    <option value="">Select Destination</option>
                    <option value="Kathmandu">Kathmandu</option>
                    <option value="Pokhara">Pokhara</option>
                    <option value="Attariya">Attariya</option>
                    <option value="Dhangadhi">Dhangadhi</option>
                </select>
            </div>
        </div>

        <div class="input-group">
            <label>Departure Date</label>
            <div class="input-icon">
                <i class="fas fa-calendar-alt"></i>
                <input type="date" name="date" id="date" min="<?= date('Y-m-d'); ?>" required>
            </div>
        </div>

        <button type="submit" name="search_bus">
            <i class="fas fa-search"></i> Search Buses
        </button>
    </form>
</div>
</body>
</html>