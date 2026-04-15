<?php
session_start();
require_once('stripe_config.php');
require_once('stripe-php/init.php');
require('fpdf.php');
include("connection.php");

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// Get the session ID from URL
$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : '';

if (!$session_id) {
    die("Invalid payment session.");
}

// Verify the session with Stripe
try {
    $checkout_session = \Stripe\Checkout\Session::retrieve($session_id);
    
    // Check if payment was successful
    if ($checkout_session->payment_status !== 'paid') {
        die("Payment was not completed successfully.");
    }

    // Get session data
    if (!isset($_SESSION['ticket_info']) || !isset($_SESSION['user_details'])) {
        // Try to get data from Stripe metadata
        $metadata = $checkout_session->metadata;
        
        // Reconstruct session data from metadata
        $_SESSION['ticket_info'] = [
            'bus_id' => $metadata->bus_id,
            'bus_number' => $metadata->bus_number,
            'route' => $metadata->route,
            'trip_date' => $metadata->trip_date,
            'seat_no' => $metadata->seat_numbers,
            'passengers' => $metadata->passengers,
            'price' => $checkout_session->amount_total / 100, // Convert from paisa to rupees
            'selected_seats' => explode(', ', $metadata->seat_numbers)
        ];
        
        $_SESSION['user_details'] = [
            'full_name' => $metadata->full_name,
            'email' => $metadata->email,
            'mobile_number' => $metadata->mobile_number,
            'boarding_point' => $metadata->boarding_point
        ];
    }

    $ticket = $_SESSION['ticket_info'];
    $userDetails = $_SESSION['user_details'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update seat status to 'booked' and payment status to 'paid'
        $selectedSeats = is_array($ticket['selected_seats']) ? $ticket['selected_seats'] : explode(', ', $ticket['selected_seats']);
        $bus_id = $ticket['bus_id'];
        $seatsCount = count($selectedSeats);

        // Update the single booking record (no more looping through seats)
        $seatNumbersStr = implode(", ", $selectedSeats); // Combine seat numbers
        
        $stmt = $conn->prepare("
            UPDATE bookings 
            SET 
                status = 'booked', 
                payment_status = 'paid',
                stripe_payment_intent = ?,
                boarding_point = ?,
                full_name = ?,
                mobile_number = ?,
                email = ?
            WHERE bus_id = ? AND seat_number = ? AND stripe_session_id = ?
        ");
        
        $stmt->bind_param(
            "sssssiis", 
            $checkout_session->payment_intent,
            $userDetails['boarding_point'], 
            $userDetails['full_name'], 
            $userDetails['mobile_number'], 
            $userDetails['email'], 
            $bus_id, 
            $seatNumbersStr,
            $session_id
        );
        
        $stmt->execute();
        $stmt->close();

        // Update the route table to decrease available_seats
        $stmt = $conn->prepare("
            UPDATE route 
            SET available_seats = available_seats - ? 
            WHERE id = ?
        ");
        
        $stmt->bind_param("ii", $seatsCount, $bus_id);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();
        
        // Fetch the booking created_at time
        $stmt = $conn->prepare("SELECT created_at FROM bookings WHERE bus_id = ? AND seat_number = ? LIMIT 1");
        $stmt->bind_param("is", $bus_id, $seatNumbersStr);
        $stmt->execute();
        $result = $stmt->get_result();
        $bookingData = $result->fetch_assoc();
        $bookingCreatedAt = $bookingData['created_at'];
        $stmt->close();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        die("Error updating seat status: " . $e->getMessage());
    }

    // Ensure "tickets" folder exists
    $directory = "tickets";
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    // Function to generate QR code
    function generateQRCode($data) {
        // Generate a unique filename for the QR code
        $qrFileName = "tickets/qr_" . md5($data . time()) . ".png";
        
        // Create QR code using API (qrserver.com)
        $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($data);
        
        // Download QR code image
        $qrImageData = @file_get_contents($qrApiUrl);
        
        if ($qrImageData !== false) {
            file_put_contents($qrFileName, $qrImageData);
            return $qrFileName;
        }
        
        return null;
    }

    // Function to generate PDF
    function generatePDF($ticket, $fullName, $email, $mobileNumber, $boardingPoint, $paymentIntent, $bookingCreatedAt, $qrCodePath = null) {
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();
        // Disable automatic page breaks so we control layout and keep footer on first page
        $pdf->SetAutoPageBreak(false, 10);
        
        // Color scheme
        $headerColor = array(25, 118, 210); // Blue
        $accentColor = array(66, 165, 245); // Light Blue
        $textDark = array(33, 33, 33); // Dark gray
        $borderColor = array(189, 189, 189); // Light gray

        // ===== HEADER =====
        $pdf->SetFillColor($headerColor[0], $headerColor[1], $headerColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->Cell(0, 20, 'BUS TICKET', 0, 1, 'C', true);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, 'Official Travel Document', 0, 1, 'C', true);
        
        $pdf->SetTextColor($textDark[0], $textDark[1], $textDark[2]);
        $pdf->Ln(3);

        // ===== TICKET NUMBER AND QR CODE ON TOP RIGHT =====
        $currentY = $pdf->GetY();
        
        // Ticket Number on left
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetXY(10, $currentY);
        $pdf->Cell(90, 8, 'Ticket No: ' . substr(md5($paymentIntent), 0, 12), 0, 0);
        
        // QR Code on right
        if ($qrCodePath && file_exists($qrCodePath)) {
            $qrX = 155;
            $qrY = $currentY - 3;
            $pdf->Image($qrCodePath, $qrX, $qrY, 45, 45);
        }
        
        $pdf->SetXY(10, $currentY + 10);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(90, 6, 'Booking Date: ' . date('Y-m-d H:i', strtotime($bookingCreatedAt)), 0, 1);
        $pdf->Ln(1);

        // ===== PASSENGER DETAILS SECTION =====
        $pdf->SetFillColor($accentColor[0], $accentColor[1], $accentColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(140, 7, 'PASSENGER DETAILS', 0, 1, 'L', true);
        
        $pdf->SetTextColor($textDark[0], $textDark[1], $textDark[2]);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 6, 'Name: ' . $fullName, 0, 1);
        $pdf->Cell(0, 6, 'Email: ' . $email, 0, 1);
        $pdf->Cell(0, 6, 'Mobile: ' . $mobileNumber, 0, 1);
        $pdf->Ln(1);

        // ===== JOURNEY DETAILS SECTION =====
        $pdf->SetFillColor($accentColor[0], $accentColor[1], $accentColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 7, 'JOURNEY DETAILS', 0, 1, 'L', true);
        
        $pdf->SetTextColor($textDark[0], $textDark[1], $textDark[2]);
        $pdf->SetFont('Arial', '', 9);
        
        // Two column layout
        $pdf->Cell(95, 6, 'Bus Number: ' . $ticket['bus_number'], 1, 0, 'L');
        $pdf->Cell(95, 6, 'Route: ' . $ticket['route'], 1, 1, 'L');
        
        $pdf->Cell(95, 6, 'Trip Date: ' . $ticket['trip_date'], 1, 0, 'L');
        $pdf->Cell(95, 6, 'Boarding Point: ' . $boardingPoint, 1, 1, 'L');
        $pdf->Ln(1);

        // ===== SEAT AND FARE SECTION =====
        $pdf->SetFillColor($accentColor[0], $accentColor[1], $accentColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 7, 'SEAT & FARE INFORMATION', 0, 1, 'L', true);
        
        $pdf->SetTextColor($textDark[0], $textDark[1], $textDark[2]);
        $pdf->SetFont('Arial', '', 9);
        
        $pdf->Cell(95, 6, 'Seat Numbers: ' . $ticket['seat_no'], 1, 0, 'L');
        $pdf->Cell(95, 6, 'No. of Passengers: ' . $ticket['passengers'], 1, 1, 'L');
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(95, 8, 'TOTAL FARE: NPR ' . number_format($ticket['price'], 2), 1, 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(95, 8, 'Status: CONFIRMED', 1, 1, 'C');
        $pdf->Ln(1);

        // ===== PAYMENT INFORMATION =====
        $pdf->SetFillColor($accentColor[0], $accentColor[1], $accentColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 7, 'PAYMENT INFORMATION', 0, 1, 'L', true);
        
        $pdf->SetTextColor($textDark[0], $textDark[1], $textDark[2]);
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->Cell(0, 6, 'Payment Method: Stripe (Card Payment)', 1, 1);
        $pdf->Cell(0, 6, 'Transaction ID: ' . substr($paymentIntent, 0, 25), 1, 1);
        $pdf->Ln(1);

        // ===== IMPORTANT NOTES =====
        $pdf->SetFillColor(255, 243, 224); // Light orange background
        $pdf->SetDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
        $pdf->SetLineWidth(0.5);
        
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor($textDark[0], $textDark[1], $textDark[2]);
        $pdf->Cell(0, 6, 'IMPORTANT TERMS & CONDITIONS', 0, 1, 'L', true);
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetLeftMargin(13);
        
        // Rules as separate lines with bullet points
        $rules = array(
              "CANCELLATION POLICY: Tickets can only be cancelled up to 6 hours before the scheduled departure time.",
    "Cancellations made after the 6-hour window will NOT be processed and no refund will be issued.",
    "This ticket is non-transferable. Only the passenger mentioned above is authorized to travel.",
    "Valid only for the specified bus, date, and seat number.",
    "Please arrive at the boarding point 15 minutes before departure.",
    "Keep this ticket safe throughout your journey.",
    "Show this ticket during boarding and seat verification.",
    "Our company reserves the right to modify schedules or cancel services due to unforeseen circumstances."
        );
        
        foreach ($rules as $rule) {
            $pdf->Cell(5, 5, chr(149), 0, 0); // Bullet point
            $pdf->SetLeftMargin(16);
            $pdf->MultiCell(178, 5, $rule, 0, 'L');
            $pdf->SetLeftMargin(13);
        }
        
        $pdf->SetLeftMargin(10);

        // ===== FOOTER =====
        // Place footer at an absolute position near page bottom (first page)
        $pdf->SetY($pdf->GetPageHeight() - 30);
        $pdf->SetFillColor($headerColor[0], $headerColor[1], $headerColor[2]);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 7, 'Thank You for Choosing Our Service!', 0, 1, 'C', true);
        $pdf->SetFont('Arial', '', 7.5);
        $pdf->Cell(0, 4, 'Safe Journey!', 0, 1, 'C', true);

        // Save PDF file
        $filePath = "tickets/ticket_" . time() . ".pdf";
        $pdf->Output($filePath, 'F'); // Save to server

        return $filePath;
    }

    // Generate unique QR code data (Payment Intent ID + Seat Numbers)
    $qrData = "Payment:" . $checkout_session->payment_intent . "|Seats:" . $ticket['seat_no'] . "|Date:" . $ticket['trip_date'];
    $qrCodePath = generateQRCode($qrData);

    // Generate Ticket PDF with QR code
    $pdfFile = generatePDF($ticket, $userDetails['full_name'], $userDetails['email'], $userDetails['mobile_number'], $userDetails['boarding_point'], $checkout_session->payment_intent, $bookingCreatedAt, $qrCodePath);

    // Clear session data after successful booking
    unset($_SESSION['stripe_session_id']);
    
} catch (Exception $e) {
    die("Error verifying payment: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
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

        .success-icon {
            font-size: 80px;
            color: #4CAF50;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        h2 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #89cff0;
        }

        .success {
            color: #4CAF50;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .payment-info {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: left;
        }

        .payment-info p {
            margin: 10px 0;
            font-size: 14px;
            color: #ddd;
        }

        .payment-info strong {
            color: #89cff0;
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

        .btn-download {
            background: linear-gradient(135deg, #4CAF50, #45a049);
        }

        .btn-download:hover {
            background: linear-gradient(135deg, #45a049, #4CAF50);
        }

        a.link {
            color: #89cff0;
            text-decoration: none;
            transition: color 0.3s ease;
            display: block;
            margin-top: 20px;
        }

        a.link:hover {
            color: #1e90ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2>Payment Successful!</h2>
        <p class="success">Your ticket has been booked successfully.</p>
        
        <div class="payment-info">
            <p><strong>Transaction ID:</strong> <?= htmlspecialchars(substr($checkout_session->payment_intent, 0, 25)); ?>...</p>
            <p><strong>Amount Paid:</strong> NPR <?= number_format($ticket['price'], 2); ?></p>
            <p><strong>Payment Method:</strong> Card</p>
            <p><strong>Receipt sent to:</strong> <?= htmlspecialchars($userDetails['email']); ?></p>
        </div>

        <a class="btn btn-download" href="<?= $pdfFile; ?>" download>
            <i class="fas fa-download"></i> Download Ticket (PDF)
        </a>
        <br>
        <a class="btn" href="userHome.php">
            <i class="fas fa-home"></i> Go to Home
        </a>
    </div>
</body>
</html>
