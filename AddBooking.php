<?php
session_start();
include("userNav.php");
include("connection.php");

// Check if booking details are provided
if (!isset($_POST['selected_bus']) || !isset($_POST['selected_seats']) || !isset($_POST['total_amount'])) {
    die("No booking details found!");
}

// Decode JSON data
$bus = json_decode($_POST['selected_bus'], true);
$selectedSeats = json_decode($_POST['selected_seats'], true);
$totalAmount = $_POST['total_amount'];

if (!$bus || !$selectedSeats) {
    die("Error decoding booking details!");
}

// Start transaction
$conn->begin_transaction();

try {
    // Calculate values
    $route = $bus['Source'] . " - " . $bus['destination'];
    $tripDate = $bus['departure_date'];
    $passengerCount = count($selectedSeats);
    $seatNumbers = implode(", ", $selectedSeats); // Combine all seat numbers
    
    // Create a single booking for all seats
    $stmt = $conn->prepare("
        INSERT INTO bookings (
            bus_id, 
            seat_number, 
            status, 
            payment_status, 
            bus_number, 
            route, 
            trip_date, 
            passengers, 
            price
        ) VALUES (?, ?, 'pending', 'unpaid', ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param(
        "issssid",  // Types: i=int, s=string, s=string, s=string, s=string, i=int, d=decimal
        $bus['id'], 
        $seatNumbers,
        $bus['bus_number'], 
        $route,
        $tripDate,
        $passengerCount,
        $totalAmount
    );
    
    $stmt->execute();
    $bookingId = $stmt->insert_id; // Get the booking ID
    $stmt->close();

    // Commit transaction
    $conn->commit();
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    die("Error updating seat status: " . $e->getMessage());
}

// Store booking details in session
$_SESSION['ticket_info'] = [
    'route'      => $bus['Source'] . " - " . $bus['destination'],
    'trip_date'  => $bus['departure_date']. " - " . date("g:i A", strtotime("2000-01-01 " . $bus['departure_time'])),
    'price'      => $totalAmount,
    'seat_no'    => implode(", ", $selectedSeats),
    'passengers' => count($selectedSeats),
    'bus_id'     => $bus['id'],
    'bus_number' => $bus['bus_number'],
    'selected_seats' => $selectedSeats
];

$boardingPoints = [
    "Attariya" => ["Attariya Buspark"],
    "Dhangadhi" => ["Dhangadhi Buspark", "Attariya Buspark"],
    "Mahendranagar" => ["Mahendranagar Buspark", "Attariya Buspark"],
    "Kathmandu" => ["Gongabu Buspark", "Kalanki Dhungeadda"],
    "pokhara" => ["Hari chowk", "pokhara (baglung Buspark)", "Zero kilo", "Tourist Bus Park"]
];

$source = $bus['Source'];
$availableBoardingPoints = isset($boardingPoints[$source]) ? $boardingPoints[$source] : [];

// Create DateTime object from bus departure time
$departureTime = new DateTime($bus['departure_time']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Ensure full-page centering */
        body {
            background: #0a1f3d;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            padding-top: 80px;
            color: white;
        }

        /* Centering the booking container */
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 500px;
            text-align: center;
        }

        h3 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #89cff0;
        }

        .details {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            text-align: left;
        }

        .details p {
            font-size: 14px;
            color: #ddd;
            margin: 8px 0;
        }

        .details strong {
            color: #89cff0;
        }

        .amount {
            font-size: 18px;
            font-weight: bold;
            color: #2ECC71;
            margin-top: 10px;
        }

        form {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 15px;
            text-align: left;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
            color: #89cff0;
        }

        select, input {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        select:focus, input:focus {
            outline: none;
            border-color: #89cff0;
            box-shadow: 0 0 15px rgba(137, 207, 240, 0.2);
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #89cff0, #1e90ff);
            border: none;
            color: white;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        button:hover {
            background: linear-gradient(135deg, #1e90ff, #89cff0);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 144, 255, 0.4);
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h3 {
                font-size: 20px;
            }

            .details p {
                font-size: 12px;
            }

            .amount {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h3><i class="fas fa-check-circle"></i> Add Booking</h3>
        <div class="details">
            <p><strong><i class="fas fa-bus"></i> Bus:</strong> <?= htmlspecialchars($bus['bus_name']) ?> (Bus No: <?= htmlspecialchars($bus['bus_number']) ?>)</p>
            <p><strong><i class="fas fa-route"></i> Route:</strong> <?= htmlspecialchars($bus['Source']) ?> - <?= htmlspecialchars($bus['destination']) ?></p>
            <p><strong><i class="fas fa-calendar-alt"></i> Date:</strong> <?= htmlspecialchars($bus['departure_date']) ?> - <?= date("g:i A", strtotime("2000-01-01 " . $bus['departure_time'])) ?></p>
            <p><strong><i class="fas fa-users"></i> Passengers:</strong> <?= count($selectedSeats) ?></p>
            <p><strong><i class="fas fa-chair"></i> Seat No:</strong> <?= implode(', ', $selectedSeats) ?></p>
        </div>

        <p class="amount"><i class="fas fa-money-bill-wave"></i> Total Amount: NPR <?= htmlspecialchars($totalAmount) ?></p>

        <form action="BookingConfirmation.php" method="POST">
            <h3><i class="fas fa-user"></i> Contact Person Details</h3>
            

            <label for="boarding_point"><i class="fas fa-map-marker-alt"></i> Boarding Point:</label>
            <select name="boarding_point" id="boarding_point" required>
                <?php foreach ($availableBoardingPoints as $index => $point): 
                    $pointTime = clone $departureTime;
                    $pointTime->modify("+" . (30 * $index) . " minutes");
                    $timeString = $pointTime->format("g:i A");
                    $fullPoint = $point . " (" . $timeString . ")";
                ?>
                    <option value="<?= htmlspecialchars($fullPoint) ?>">
                        <?= htmlspecialchars($fullPoint) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="full_name"><i class="fas fa-user"></i> Full Name:</label>
            <input type="text" name="full_name" required>

            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
            <input type="email" name="email" required>

            <label for="mobile_number"><i class="fas fa-phone"></i> Mobile Number:</label>
            <input type="text" name="mobile_number" required pattern="98\97\d{8}" title="Mobile number must start with 98 and be 10 digits long">

            <button type="submit"><i class="fas fa-check"></i> Confirm Booking</button>
        </form>
    </div>
</body>
</html>