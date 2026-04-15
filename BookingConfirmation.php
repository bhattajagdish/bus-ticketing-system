<?php
session_start();
include("userNav.php");

// Check if the session has booking details
if (!isset($_SESSION['ticket_info'])) {
    die("No booking session found. Please go back and book again.");
}

// Retrieve stored session data
$ticket = $_SESSION['ticket_info'];

// Retrieve submitted form data
if (!isset($_POST['full_name']) || !isset($_POST['email']) || !isset($_POST['mobile_number']) || !isset($_POST['boarding_point'])) {
    die("Missing booking details!");
}

$fullName = htmlspecialchars($_POST['full_name']);
$email = htmlspecialchars($_POST['email']);
$mobileNumber = htmlspecialchars($_POST['mobile_number']);
$boardingPoint = htmlspecialchars($_POST['boarding_point']);

// Store user details in session
$_SESSION['user_details'] = [
    'full_name' => $fullName,
    'email' => $email,
    'mobile_number' => $mobileNumber,
    'boarding_point' => $boardingPoint
];
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

        h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #89cff0;
        }

        .section {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            text-align: left;
        }

        .section .title {
            font-size: 18px;
            font-weight: bold;
            color: #89cff0;
            margin-bottom: 15px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
            color: #ddd;
        }

        .row span:first-child {
            font-weight: bold;
            color: #89cff0;
        }

        .confirm-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #635BFF, #4F46E5);
            border: none;
            color: white;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .confirm-btn:hover {
            background: linear-gradient(135deg, #4F46E5, #635BFF);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99, 91, 255, 0.4);
        }

        .confirm-btn i {
            margin-right: 10px;
        }

        .stripe-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
            font-size: 12px;
            color: #aaa;
        }

        .stripe-badge img {
            height: 20px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h2 {
                font-size: 20px;
            }

            .section .title {
                font-size: 16px;
            }

            .row {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-check-circle"></i> Booking Confirmation</h2>

        <!-- Trip Details Section -->
        <div class="section">
            <div class="title"><i class="fas fa-bus"></i> Trip Details</div>
            <div class="row"><span>Route:</span> <span><?= $ticket['route']; ?></span></div>
            <div class="row"><span>Trip Date:</span> <span><?= $ticket['trip_date']; ?></span></div>
            <div class="row"><span>Ticket Price:</span> <span>NPR <?= $ticket['price']; ?></span></div>
            <div class="row"><span>Boarding Point:</span> <span><?= $boardingPoint; ?></span></div>
        </div>

        <!-- Contact Details Section -->
        <div class="section">
            <div class="title"><i class="fas fa-user"></i> Contact Details</div>
            <div class="row"><span>Full Name:</span> <span><?= $fullName; ?></span></div>
            <div class="row"><span>Email:</span> <span><?= $email; ?></span></div>
            <div class="row"><span>Mobile Number:</span> <span><?= $mobileNumber; ?></span></div>
        </div>

        <!-- Form to submit to Stripe checkout -->
        <form action="stripe_checkout.php" method="POST">
            <input type="hidden" name="full_name" value="<?= htmlspecialchars($fullName); ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email); ?>">
            <input type="hidden" name="mobile_number" value="<?= htmlspecialchars($mobileNumber); ?>">
            <input type="hidden" name="boarding_point" value="<?= htmlspecialchars($boardingPoint); ?>">
            <button type="submit" class="confirm-btn">
                <i class="fas fa-credit-card"></i> Proceed to Secure Payment
            </button>
        </form>

        <div class="stripe-badge">
            <i class="fas fa-lock"></i>
            <span>Secured by</span>
            <svg height="20" viewBox="0 0 60 25" xmlns="http://www.w3.org/2000/svg" width="60" style="vertical-align: middle;">
                <path d="m59.64 14.28h-8.06c.19 1.93 1.6 2.55 3.2 2.55 1.64 0 2.96-.37 4.05-.95v3.32a8.33 8.33 0 0 1-4.56 1.1c-4.01 0-6.83-2.5-6.83-7.48 0-4.19 2.39-7.52 6.3-7.52 3.92 0 5.96 3.28 5.96 7.5 0 .4-.04 1.26-.06 1.48zm-5.92-5.62c-1.03 0-2.17.73-2.17 2.58h4.25c0-1.85-1.07-2.58-2.08-2.58zM40.95 20.3c-1.44 0-2.32-.6-2.9-1.04l-.02 4.63-4.12.87V5.57h3.76l.08 1.02a4.7 4.7 0 0 1 3.23-1.29c2.9 0 5.62 2.6 5.62 7.4 0 5.23-2.7 7.6-5.65 7.6zM40 8.95c-.95 0-1.54.34-1.97.81l.02 6.12c.4.44.98.78 1.95.78 1.52 0 2.54-1.65 2.54-3.87 0-2.15-1.04-3.84-2.54-3.84zM28.24 5.57h4.13v14.44h-4.13V5.57zm0-4.7L32.37 0v3.36l-4.13.88V.88zm-4.32 9.35v9.79H19.8V5.57h3.7l.12 1.22c1-1.77 3.07-1.41 3.62-1.22v3.79c-.52-.17-2.29-.43-3.32.86zm-8.55 4.72c0 2.43 2.6 1.68 3.12 1.46v3.36c-.55.3-1.54.54-2.89.54a4.15 4.15 0 0 1-4.27-4.24l.01-13.17 4.02-.86v3.54h3.14V9.1h-3.13v5.85zm-4.91.7c0 2.97-2.31 4.66-5.73 4.66a11.2 11.2 0 0 1-4.46-.93v-3.93c1.38.75 3.1 1.31 4.46 1.31.92 0 1.53-.24 1.53-1C6.26 13.77 0 14.51 0 9.95 0 7.04 2.28 5.3 5.62 5.3c1.36 0 2.72.2 4.09.75v3.88a9.23 9.23 0 0 0-4.1-1.06c-.86 0-1.44.25-1.44.9 0 1.85 6.29.97 6.29 5.88z" fill="#635BFF"/>
            </svg>
        </div>
    </div>
</body>
</html>
