<?php
require_once('stripe_config.php');
require_once('connection.php');
require_once('stripe-php/init.php');

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// Get the webhook payload
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, STRIPE_WEBHOOK_SECRET
    );
} catch(\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit();
}

// Handle the event
switch ($event->type) {
    case 'checkout.session.completed':
        $session = $event->data->object;
        
        // Get metadata from session
        $metadata = $session->metadata;
        
        // Update database with payment confirmation
        $bus_id = $metadata->bus_id;
        $seat_numbers = explode(', ', $metadata->seat_numbers);
        $seatsCount = count($seat_numbers);
        
        foreach ($seat_numbers as $seat) {
            $stmt = $conn->prepare("
                UPDATE bookings 
                SET 
                    status = 'booked', 
                    payment_status = 'paid',
                    stripe_payment_intent = ?
                WHERE bus_id = ? AND seat_number = ? AND stripe_session_id = ?
            ");
            
            $stmt->bind_param(
                "siis", 
                $session->payment_intent,
                $bus_id, 
                $seat,
                $session->id
            );
            
            $stmt->execute();
            $stmt->close();
        }
        
        // Update the route table to decrease available_seats
        $stmt = $conn->prepare("
            UPDATE route 
            SET available_seats = available_seats - ? 
            WHERE id = ?
        ");
        
        $stmt->bind_param("ii", $seatsCount, $bus_id);
        $stmt->execute();
        $stmt->close();
        
        break;
        
    case 'checkout.session.expired':
        $session = $event->data->object;
        
        // Release seats if session expires
        $stmt = $conn->prepare("
            UPDATE bookings 
            SET 
                status = 'available', 
                payment_status = 'unpaid',
                stripe_session_id = NULL,
                boarding_point = '',
                full_name = '',
                mobile_number = '',
                email = ''
            WHERE stripe_session_id = ?
        ");
        
        $stmt->bind_param("s", $session->id);
        $stmt->execute();
        $stmt->close();
        
        break;
        
    default:
        // Unexpected event type
        http_response_code(400);
        exit();
}

http_response_code(200);
?>
