<?php 
session_start();

// Check if search results exist
if (!isset($_SESSION['search_results']) || empty($_SESSION['search_results'])) {
    header("Location: userHome.php");
    exit();
}
include("userNav.php");
include("connection.php");

// Clean up expired pending bookings (older than 2 minutes)
$cleanup_query = "DELETE FROM bookings WHERE status = 'pending' AND created_at < (NOW() - INTERVAL 2 MINUTE)";
$conn->query($cleanup_query);

$results = $_SESSION['search_results'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a1f3d;
            color: white;
            margin: 0;
            padding: 0;
            padding-top: 80px;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 30px;
            font-size: 2.2em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .result {
            margin-top: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .result:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .details {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .details-left {
            flex: 1;
        }

        .details-right {
            text-align: right;
        }

        .details p {
            margin: 8px 0;
            font-size: 16px;
        }

        .details strong {
            color: #89cff0;
        }

        .seats-info {
            margin-top: 15px;
        }

        .progress-bar {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            height: 10px;
            margin: 10px 0;
        }

        .progress {
            height: 10px;
            background: linear-gradient(135deg, #89cff0, #1e90ff);
            border-radius: 10px;
        }

        .view-seats {
            padding: 12px 25px;
            margin: 15px;
            background: linear-gradient(135deg, #89cff0, #1e90ff);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .view-seats:hover {
            background: linear-gradient(135deg, #1e90ff, #89cff0);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 144, 255, 0.4);
        }

        .icon {
            margin-right: 10px;
            color: #89cff0;
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            .details {
                flex-direction: column;
                align-items: flex-start;
            }

            .details-right {
                text-align: left;
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-bus icon"></i>Available Buses</h2>
    <?php foreach ($results as $bus): ?>
        <?php
            // Calculate percentage for available seats
            $total_seats = $bus['total_seats'] ?? 0;
            $available_seats = $bus['available_seats'] ?? 0;
            $percentage = ($total_seats > 0) ? ($available_seats / $total_seats) * 100 : 0;

            // Format departure time
            $departure_time = date("h:i A", strtotime("2000-01-01 " . $bus['departure_time']));
        ?>
        <div class="result">
            <div class="details">
                <div class="details-left">
                    <p><strong><i class="fas fa-bus icon"></i>Bus Name:</strong> <?= htmlspecialchars($bus['bus_name']); ?></p>
                    <p><strong><i class="fas fa-hashtag icon"></i>Bus No:</strong> <?= htmlspecialchars($bus['bus_number']); ?></p>
                    <div class="seats-info">
                        <p><strong><i class="fas fa-chair icon"></i>Seats Available:</strong> <?= htmlspecialchars($available_seats); ?></p>
                        <div class="progress-bar">
                            <div class="progress" style="width: <?= $percentage; ?>%;"></div>
                        </div>
                        <p><strong>Total Seats:</strong> <?= htmlspecialchars($total_seats); ?></p>
                    </div>
                </div>
                <div class="details-right">
                    <p><strong><i class="fas fa-calendar-alt icon"></i>Departure:</strong> <?= htmlspecialchars($bus['departure_date']); ?> at <?= htmlspecialchars($departure_time); ?></p>
                    <p><strong><i class="fas fa-money-bill-wave icon"></i>Cost:</strong> <?= htmlspecialchars($bus['cost']); ?> NPR</p>
                    <a class="view-seats" href="viewSeats.php?bus_id=<?= htmlspecialchars($bus['id']); ?>">
                        <i class="fas fa-eye"></i> View Seats
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>