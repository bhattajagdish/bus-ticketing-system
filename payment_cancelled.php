<?php
session_start();
include("connection.php");

// If there's a pending booking, release the seats
if (isset($_SESSION['ticket_info'])) {
    $ticket = $_SESSION['ticket_info'];
    $seatNumbers = $ticket['seat_no'];
    $bus_id = $ticket['bus_id'];

    // Delete the pending booking record (no longer needed, just remove it)
    $stmt = $conn->prepare("
        DELETE FROM bookings 
        WHERE bus_id = ? AND seat_number = ? AND status = 'pending'
    ");
    
    $stmt->bind_param("is", $bus_id, $seatNumbers);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a1f3d;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 500px;
            text-align: center;
        }

        .warning-icon {
            font-size: 80px;
            color: #ff9800;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #89cff0;
        }

        .message {
            color: #ddd;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn {
            padding: 14px 30px;
            background: linear-gradient(135deg, #89cff0, #1e90ff);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 10px 5px;
        }

        .btn:hover {
            background: linear-gradient(135deg, #1e90ff, #89cff0);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 144, 255, 0.4);
        }

        .btn i {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="warning-icon">
            <i class="fas fa-times-circle"></i>
        </div>
        <h2>Payment Cancelled</h2>
        <p class="message">
            Your payment was cancelled. The seats you selected have been released.<br>
            You can try booking again or explore other available routes.
        </p>
        
        <a class="btn" href="userHome.php">
            <i class="fas fa-search"></i> Search for Buses
        </a>
        <a class="btn" href="userHome.php">
            <i class="fas fa-home"></i> Go to Home
        </a>
    </div>
</body>
</html>
