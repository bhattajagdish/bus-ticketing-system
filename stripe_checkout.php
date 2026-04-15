<?php
session_start();
require_once('stripe_config.php');
require_once('connection.php');

// Composer autoload for Stripe PHP library
require_once('stripe-php/init.php');

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// Check if session data exists
if (!isset($_SESSION['ticket_info']) || !isset($_SESSION['user_details'])) {
    die("No booking session found. Please go back and book again.");
}

$ticket = $_SESSION['ticket_info'];
$userDetails = $_SESSION['user_details'];

// Convert NPR to smallest currency unit (paisa) - Stripe requires amounts in smallest unit
// For NPR, 1 Rupee = 100 Paisa
$amount = (int)($ticket['price'] * 100);

try {
    // Create Stripe Checkout Session
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'npr',
                'unit_amount' => $amount,
                'product_data' => [
                    'name' => 'Bus Ticket - ' . $ticket['route'],
                    'description' => 'Trip Date: ' . $ticket['trip_date'] . ' | Seats: ' . $ticket['seat_no'] . ' | Boarding: ' . $userDetails['boarding_point'],
                    'images' => [SITE_URL . '/image/logo.png'],
                ],
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'customer_email' => $userDetails['email'],
        'success_url' => SITE_URL . '/payment_success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => SITE_URL . '/payment_cancelled.php',
        'metadata' => [
            'bus_id' => $ticket['bus_id'],
            'bus_number' => $ticket['bus_number'],
            'route' => $ticket['route'],
            'trip_date' => $ticket['trip_date'],
            'seat_numbers' => $ticket['seat_no'],
            'passengers' => $ticket['passengers'],
            'full_name' => $userDetails['full_name'],
            'email' => $userDetails['email'],
            'mobile_number' => $userDetails['mobile_number'],
            'boarding_point' => $userDetails['boarding_point'],
        ],
    ]);

    // Save session ID to database for tracking
    $seatNumbers = $ticket['seat_no'];
    $bus_id = $ticket['bus_id'];
    
    // Update the single booking record (not looping through seats)
    $stmt = $conn->prepare("
        UPDATE bookings 
        SET 
            status = 'pending', 
            payment_status = 'pending',
            stripe_session_id = ?,
            payment_method = 'stripe',
            boarding_point = ?,
            full_name = ?,
            mobile_number = ?,
            email = ?
        WHERE bus_id = ? AND seat_number = ?
    ");
    
    $stmt->bind_param(
        "sssssss", 
        $checkout_session->id,
        $userDetails['boarding_point'], 
        $userDetails['full_name'], 
        $userDetails['mobile_number'], 
        $userDetails['email'], 
        $bus_id, 
        $seatNumbers
    );
    
    $stmt->execute();
    $stmt->close();

    // Store session ID in PHP session for verification
    $_SESSION['stripe_session_id'] = $checkout_session->id;

    // Redirect to Stripe Checkout
    header('Location: ' . $checkout_session->url);
    exit();

} catch (Exception $e) {
    die("Error creating checkout session: " . $e->getMessage());
}
?>
