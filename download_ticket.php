<?php
session_start();
require_once('fpdf.php');
include("connection.php");

// Check if booking ID is provided
if (!isset($_GET['id'])) {
    die("Invalid request");
}

$booking_id = $_GET['id'];

// Fetch booking details
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Ticket not found");
}

$booking = $result->fetch_assoc();
$stmt->close();

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

// Generate unique QR code data (Booking ID + Payment Intent)
$qrData = "BookingID:" . $booking['id'] . "|PaymentID:" . $booking['stripe_payment_intent'] . "|Seat:" . $booking['seat_number'] . "|Date:" . $booking['trip_date'];
$qrCodePath = generateQRCode($qrData);

// Color scheme
$headerColor = array(25, 118, 210); // Blue
$accentColor = array(66, 165, 245); // Light Blue
$textDark = array(33, 33, 33); // Dark gray
$borderColor = array(189, 189, 189); // Light gray

// Create PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();
// Disable automatic page breaks so we control layout and keep footer on first page
$pdf->SetAutoPageBreak(false, 10);

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
$pdf->Cell(90, 8, 'Ticket No: ' . str_pad($booking['id'], 12, '0', STR_PAD_LEFT), 0, 0);

// QR Code on right
if ($qrCodePath && file_exists($qrCodePath)) {
    $qrX = 155;
    $qrY = $currentY - 3;
    $pdf->Image($qrCodePath, $qrX, $qrY, 45, 45);
}

$pdf->SetXY(10, $currentY + 10);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(90, 6, 'Booking Date: ' . date('Y-m-d H:i', strtotime($booking['created_at'])), 0, 1);
$pdf->Ln(1);

// ===== PASSENGER DETAILS SECTION =====
$pdf->SetFillColor($accentColor[0], $accentColor[1], $accentColor[2]);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(140, 7, 'PASSENGER DETAILS', 0, 1, 'L', true);

$pdf->SetTextColor($textDark[0], $textDark[1], $textDark[2]);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 6, 'Name: ' . htmlspecialchars($booking['full_name']), 0, 1);
$pdf->Cell(0, 6, 'Email: ' . htmlspecialchars($booking['email']), 0, 1);
$pdf->Cell(0, 6, 'Mobile: ' . htmlspecialchars($booking['mobile_number']), 0, 1);
$pdf->Ln(1);

// ===== JOURNEY DETAILS SECTION =====
$pdf->SetFillColor($accentColor[0], $accentColor[1], $accentColor[2]);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, 'JOURNEY DETAILS', 0, 1, 'L', true);

$pdf->SetTextColor($textDark[0], $textDark[1], $textDark[2]);
$pdf->SetFont('Arial', '', 9);

// Two column layout
$pdf->Cell(95, 6, 'Bus Number: ' . htmlspecialchars($booking['bus_number']), 1, 0, 'L');
$pdf->Cell(95, 6, 'Route: ' . htmlspecialchars($booking['route']), 1, 1, 'L');

$pdf->Cell(95, 6, 'Trip Date: ' . htmlspecialchars($booking['trip_date']), 1, 0, 'L');
$pdf->Cell(95, 6, 'Boarding Point: ' . htmlspecialchars($booking['boarding_point']), 1, 1, 'L');
$pdf->Ln(1);

// ===== SEAT AND FARE SECTION =====
$pdf->SetFillColor($accentColor[0], $accentColor[1], $accentColor[2]);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, 'SEAT & FARE INFORMATION', 0, 1, 'L', true);

$pdf->SetTextColor($textDark[0], $textDark[1], $textDark[2]);
$pdf->SetFont('Arial', '', 9);

$pdf->Cell(95, 6, 'Seat Number: ' . htmlspecialchars($booking['seat_number']), 1, 0, 'L');
$pdf->Cell(95, 6, 'No. of Passengers: ' . htmlspecialchars($booking['passengers']), 1, 1, 'L');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 8, 'TOTAL FARE: NPR ' . number_format($booking['price'], 2), 1, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(95, 8, 'Status: ' . strtoupper(htmlspecialchars($booking['status'])), 1, 1, 'C');
$pdf->Ln(1);

// ===== PAYMENT INFORMATION =====
$pdf->SetFillColor($accentColor[0], $accentColor[1], $accentColor[2]);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, 'PAYMENT INFORMATION', 0, 1, 'L', true);

$pdf->SetTextColor($textDark[0], $textDark[1], $textDark[2]);
$pdf->SetFont('Arial', '', 8.5);
$pdf->Cell(0, 6, 'Payment Method: Stripe (Card Payment)', 1, 1);
$pdf->Cell(0, 6, 'Transaction ID: ' . substr(htmlspecialchars($booking['stripe_payment_intent']), 0, 25), 1, 1);
$pdf->Ln(1);

// ===== IMPORTANT NOTES =====
$pdf->SetFillColor(255, 243, 224); // Light orange background
$pdf->SetTextColor($textDark[0], $textDark[1], $textDark[2]);
$pdf->SetFont('Arial', 'B', 9);
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
$pdf->SetY($pdf->GetPageHeight() - 30); // absolute bottom position on first page
$pdf->SetFillColor($headerColor[0], $headerColor[1], $headerColor[2]);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, 'Thank You for Choosing Our Service!', 0, 1, 'C', true);
$pdf->SetFont('Arial', '', 7.5);
$pdf->Cell(0, 4, 'Safe Journey!', 0, 1, 'C', true);

// Generate filename and output
$filename = 'Ticket_' . $booking['bus_number'] . '_' . $booking['seat_number'] . '_' . date('Y-m-d-H-i-s') . '.pdf';
$pdf->Output('D', $filename);
?>
