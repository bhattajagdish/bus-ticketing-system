<?php
session_start();
include("userNav.php");
include("connection.php");
include("email_config.php"); // Include email configuration



// Check authentication
if(!isset($_SESSION['user_id'])) {
    header("Location: user-login.php");
    exit();
}


// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    
    // Get booking and route details
    $stmt = $conn->prepare("SELECT b.*, r.departure_date, r.departure_time FROM bookings b 
                            JOIN route r ON b.bus_id = r.id 
                            WHERE b.id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        
        // Clean the time - remove microseconds if present
        $departure_time = $booking['departure_time'];
        if (strpos($departure_time, '.') !== false) {
            $departure_time = substr($departure_time, 0, 8); // Get only HH:MM:SS
        }
        
        // Combine departure date and time
        $departure_datetime = new DateTime($booking['departure_date'] . ' ' . $departure_time);
        $current_time = new DateTime();
        
        // Debug: Show times for verification
        $departure_formatted = $departure_datetime->format('Y-m-d H:i:s (g:i A)');
        $current_formatted = $current_time->format('Y-m-d H:i:s (g:i A)');
        
        // Calculate time difference using timestamps (ACCURATE calculation)
        $current_timestamp = $current_time->getTimestamp();
        $departure_timestamp = $departure_datetime->getTimestamp();
        $seconds_remaining = $departure_timestamp - $current_timestamp;
        $hours_remaining = $seconds_remaining / 3600; // Convert seconds to hours
        
        // Validation: Check if ticket is in the past
        if ($seconds_remaining < 0) {
            echo ("<script>
    window.alert('❌ Cannot cancel ticket after departure time!\\n\\nBus Departure: " . $departure_formatted . "\\nCurrent Time: " . $current_formatted . "');
    window.location.href='TicketCancel.php';
    </script>");
        } 
        // Validation: Check if cancellation is within 6 hours of departure
        else if ($hours_remaining < 6) {
            $hours_str = number_format($hours_remaining, 2);
            echo ("<script>
    window.alert('⚠️ Cannot cancel ticket within 6 hours of departure!\\n\\nTime remaining: " . $hours_str . " hours\\nMinimum required: 6 hours\\n\\nBus Departure: " . $departure_formatted . "\\nCurrent Time: " . $current_formatted . "');
    window.location.href='TicketCancel.php';
    </script>");
        } 
        // Cancellation allowed: More than 6 hours remaining
        else {
            // Get the number of passengers to restore correct seats
            $passengerCount = $booking['passengers'];
            
            // Get user email for notification
            $user_id = $_SESSION['user_id'];
            $user_stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
            $user_stmt->bind_param("i", $user_id);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            $user_data = $user_result->fetch_assoc();
            $user_email = $user_data['email'] ?? 'noemail@example.com';
            
            // Delete booking
            $delete_stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
            $delete_stmt->bind_param("i", $booking_id);
            $delete_stmt->execute();
            $delete_stmt->close();
            
            // Restore available seats in route table
            $update_stmt = $conn->prepare("
                UPDATE route 
                SET available_seats = available_seats + ? 
                WHERE id = ?
            ");
            $update_stmt->bind_param("ii", $passengerCount, $booking['bus_id']);
            $update_stmt->execute();
            $update_stmt->close();
            
            // Prepare data for email notification
            // Compute refund: user receives 90% of the total booking price
            $total_amount = isset($booking['price']) ? floatval($booking['price']) : 0.0;
            $refund_amount = $total_amount * 0.90;

            // Prepare data for email notification
            $email_data = array(
                'booking_id' => $booking_id,
                'bus_number' => $booking['bus_number'],
                'route' => $booking['route'],
                'seat_number' => $booking['seat_number'],
                'passengers' => $booking['passengers'],
                'trip_date' => $booking['trip_date'],
                'departure_time' => $departure_formatted,
                'cancel_time' => $current_formatted,
                'total_amount' => $total_amount,
                'refund_amount' => $refund_amount
            );
            
            // Send cancellation email
            $email_sent = sendCancellationEmail($user_email, $email_data);
            
            $hours_str = number_format($hours_remaining, 2);
            $email_status = $email_sent ? "✓ Confirmation email sent to " . substr($user_email, 0, 5) . "***" : "";
            echo ("<script>
    window.alert('✓ Ticket Cancelled Successfully!!!\\n\\nTime remaining: " . $hours_str . " hours\\nBus Departure: " . $departure_formatted . "\\n\\n" . $email_status . "');
    window.location.href='TicketCancel.php';
    </script>");
        }
    } else {
        echo ("<script>
    window.alert('❌ Ticket not found!');
    window.location.href='TicketCancel.php';
    </script>");
    }
}


// Fetch all bookings for the current user
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM bookings";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Ticket</title>
    <link rel="stylesheet" href="css/slide.css">

  
   
</head>
<body class="ticketcancel-bg">
    <div class="container">

    <div class="header"style="margin-top:60px;">
            <h1>Ticket cancellation</h1>
        </div>
        
        
        <?php if (isset($message)): ?>
            <div class="message success"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <table class="table">
            <thead>
                <tr>
                    <th>Bus Number</th>
                    <th>Route</th>
                    <th>Seat Number</th>
                    <th>Trip Date</th>
                    <th>Passengers</th>
                    <th>Status</th>
                    <th>Download</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['bus_number']) ?></td>
                        <td><?= htmlspecialchars($row['route']) ?></td>
                        <td><?= htmlspecialchars($row['seat_number']) ?></td>
                        <td><?= htmlspecialchars($row['trip_date']) ?></td>
                        <td><?= htmlspecialchars($row['passengers']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <?php if ($row['status'] === 'booked' && $row['payment_status'] === 'paid'): ?>
                                <a href="download_ticket.php?id=<?= $row['id'] ?>" class="btn btn-download" title="Download Ticket">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            <?php else: ?>
                                <span style="color: #25cc08ff; font-size: 12px;">Not Available</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-delete">Cancel</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>