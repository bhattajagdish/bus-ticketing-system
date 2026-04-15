<?php
session_start();
include("userNav.php");
include("connection.php");

// Check if bus_id is provided
if (!isset($_GET['bus_id']) || !isset($_SESSION['search_results'])) {
    header("Location: userHome.php");
    exit();
}

$bus_id = $_GET['bus_id'];
$results = $_SESSION['search_results'];
$bus = null;

// Find the selected bus from search results
foreach ($results as $item) {
    if ($item['id'] == $bus_id) {
        $bus = $item;
        break;
    }
}

if (!$bus) {
    header("Location: userHome.php");
    exit();
}

// Fetch total_seats and cost from the route table
$total_seats = $bus['total_seats'] ?? 0;
$cost_per_seat = $bus['cost'] ?? 0;

// Delete pending bookings older than 1 minute
$query = "DELETE FROM bookings WHERE status = 'pending' AND created_at < (NOW() - INTERVAL 2 MINUTE)";
$conn->query($query);

// Fetch booked and pending seats from the bookings table
$booked_seats = [];
$pending_seats = [];
$query = "SELECT seat_number, status FROM bookings WHERE bus_id = ? AND (status = 'booked' OR status = 'pending')";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $bus_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // Split seat numbers if they're comma-separated (multiple seats per booking)
    $seats = array_map('intval', array_map('trim', explode(',', $row['seat_number'])));
    
    if ($row['status'] === 'booked') {
        $booked_seats = array_merge($booked_seats, $seats);
    } elseif ($row['status'] === 'pending') {
        $pending_seats = array_merge($pending_seats, $seats);
    }
}
$stmt->close();

// Calculate available seats
$available_seats = $total_seats - count($booked_seats) - count($pending_seats);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Seat Selection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a1f3d;
            color: white;
            margin: 0;
            padding: 60px 0 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 520px;
            margin: 10px auto;
            padding: 22px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        }

        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 18px;
            font-size: 1.9em;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }

        .bus-info {
            margin-bottom: 15px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .bus-info h3 {
            margin: 0;
            font-size: 1.3em;
            color: #89cff0;
        }

        .bus-info p {
            margin: 8px 0;
            font-size: 14px;
        }

        .legend {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            padding: 12px 10px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            gap: 10px;
        }

        .legend div {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }

        .legend div span {
            width: 14px;
            height: 14px;
            border-radius: 4px;
            display: inline-block;
        }

        .legend .selected { background-color: #4CAF50; }
        .legend .available { background-color: white; }
        .legend .booked { background-color: gray; }
        .legend .pending { background-color: #FFA500; }

        .bus-layout {
            background: linear-gradient(180deg, #1a3a52 0%, #0f2438 100%);
            padding: 20px;
            border-radius: 18px;
            border: 2px solid #ffd600;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
        }

        .bus-front {
            text-align: center;
            margin-bottom: 20px;
            padding: 12px;
            background: rgba(0, 0, 0, 0.35);
            border-radius: 14px;
            border: 1px dashed #89cff0;
        }

        .driver-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            padding: 14px;
            background: rgba(255, 165, 0, 0.08);
            border-radius: 12px;
            border: 1px solid #FFA500;
        }

        .driver-seat {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex: 1;
        }

        .driver-side {
            text-align: center;
        }

        .conductor-side {
            text-align: center;
        }

        .driver-seat .seat {
            width: 30px;
            height: 30px;
            line-height: 25px;
            font-size: 15px;
            font-weight: bold;
            background: linear-gradient(135deg, #FFA500, #FF8C00);
            color: white;
            cursor: not-allowed;
            pointer-events: none;
            box-shadow: 0 4px 10px rgba(255, 165, 0, 0.3);
            border-radius: 8px;
        }

        .driver-seat label {
            font-size: 10px;
            color: #FFA500;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .seat-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(32px, 1fr));
            gap: 8px;
            margin-bottom: 10px;
             margin-left: 34px;
              
        }

        .aisle-column {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 35px;
        }

        .aisle {
            width: 26px;
            height: 8px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            margin: 0;
        }

        .seat {
            width: 30px;
            height: 30px;
            text-align: center;
            line-height: 27px;
            border-radius: 8px;
            cursor: pointer;
            background-color: white;
            color: #0a1f3d;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s ease;
            border: 2px solid transparent;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.18);
        }

        .seat:hover.available {
            transform: scale(1.1);
            box-shadow: 0 6px 15px rgba(137, 207, 240, 0.4);
        }

        .seat.booked {
            background-color: #757575;
            color: white;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .seat.pending {
            background-color: #FFA500;
            color: white;
            cursor: not-allowed;
            opacity: 0.8;
        }

        .seat.selected {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            border: 2px solid #2e7d32;
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(76, 175, 80, 0.5);
        }

        .aisle {
            height: 20px;
            grid-column: 1 / -1;
        }

        .footer {
            margin-top: 18px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .footer .total-price,
        .footer p {
            margin: 4px 0;
            font-size: 14px;
        }

        .book-now {
            padding: 10px 20px;
            background: linear-gradient(135deg, #89cff0, #1e90ff);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .book-now:disabled {
            background: gray;
            cursor: not-allowed;
        }

        .book-now:hover:not(:disabled) {
            background: linear-gradient(135deg, #1e90ff, #89cff0);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 144, 255, 0.4);
        }

        @media (max-width: 768px) {
            .container {
                margin: 16px;
                padding: 16px;
            }

            .legend {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .bus-layout {
                padding: 16px;
            }

            .bus-front {
                margin-bottom: 18px;
            }

            .driver-section {
                flex-direction: column;
                gap: 12px;
                align-items: stretch;
            }

            .seat-grid {
                grid-template-columns: repeat(5, minmax(26px, 1fr));
                gap: 8px;
            }

            .seat {
                width: 34px;
                height: 34px;
                line-height: 34px;
                font-size: 12px;
            }

            .driver-seat .seat {
                width: 36px;
                height: 36px;
                line-height: 36px;
                font-size: 14px;
            }

            .footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-bus"></i> Select Your Seat</h2>
        <div class="bus-info">
            <h3><?= htmlspecialchars($bus['bus_name']) ?> (Bus No: <?= htmlspecialchars($bus['bus_number']) ?>)</h3>
            <p><i class="fas fa-money-bill-wave"></i> Cost per Seat: NPR <?= htmlspecialchars($cost_per_seat) ?></p>
            <p><i class="fas fa-chair"></i> Available Seats: <?= htmlspecialchars($available_seats) ?>/<?= htmlspecialchars($total_seats) ?></p>
        </div>

        <div class="legend">
            <div><span class="selected"></span> Selected</div>
            <div><span class="available"></span> Available</div>
            <div><span class="booked"></span> Booked</div>
            <div><span class="pending"></span> Pending</div>
        </div>

        <div class="bus-layout">
            <div class="bus-front">
                <h3 style="color: #ffd600; margin: 0;">🚌 FRONT OF BUS</h3>
            </div>

            <div class="driver-section">
                <div class="driver-seat conductor-side">
                    <div class="seat" style="cursor: not-allowed; background: linear-gradient(135deg, #FFA500, #FF8C00);">C</div>
                    <label>Conductor</label>
                </div>
                <div style="flex: 3;"></div>
                <div class="driver-seat driver-side">
                    <div class="seat" style="cursor: not-allowed; background: linear-gradient(135deg, #FFA500, #FF8C00);">D</div>
                    <label>Driver</label>
                </div>
            </div>

            <div class="seat-grid" id="seatContainer"></div>
        </div>

        <div class="footer">
            <div>
                <p>Selected Seat(s): <span id="selected-seats">Not selected</span></p>
                <p>Total Price: <span id="total-price">NPR 0</span></p>
            </div>
            <form action="AddBooking.php" method="POST" id="bookingForm">
                <input type="hidden" name="selected_bus" value='<?= json_encode($bus); ?>'>
                <input type="hidden" name="selected_seats" id="hidden-seats" value="">
                <input type="hidden" name="total_amount" id="hidden-amount" value="">
                <button type="submit" class="book-now" id="book-now" disabled>BOOK NOW</button>
            </form>
        </div>
    </div>

    <script>
        const seatContainer = document.getElementById('seatContainer');
        const selectedSeatsEl = document.getElementById('selected-seats');
        const totalPriceEl = document.getElementById('total-price');
        const bookNowBtn = document.getElementById('book-now');
        const hiddenSeats = document.getElementById('hidden-seats');
        const hiddenAmount = document.getElementById('hidden-amount');

        let selectedSeats = [];
        let seatPrice = <?= json_encode($cost_per_seat); ?>;
        let bookedSeats = <?= json_encode($booked_seats); ?>;
        let pendingSeats = <?= json_encode($pending_seats); ?>;

        // Auto-refresh page every 30 seconds to check for expired pending bookings
        setInterval(() => {
            location.reload();
        }, 30000);

        const totalSeats = <?= json_encode($total_seats); ?>;
        let seatNumber = 1;

        // Generate bus seat layout - 4 seats per row with middle aisle
        // Layout: [Seat] [Seat] [AISLE] [Seat] [Seat]
        const seatsPerRow = 4;
        const rows = Math.ceil(totalSeats / seatsPerRow);

        for (let row = 0; row < rows; row++) {
            // Left side - 2 seats
            for (let col = 0; col < 2; col++) {
                if (seatNumber <= totalSeats) {
                    createSeat(seatNumber);
                    seatNumber++;
                }
            }

            // Middle aisle
            const aisleDiv = document.createElement('div');
            aisleDiv.classList.add('aisle-column');
            const aisle = document.createElement('div');
            aisle.classList.add('aisle');
            aisleDiv.appendChild(aisle);
            seatContainer.appendChild(aisleDiv);

            // Right side - 2 seats
            for (let col = 0; col < 2; col++) {
                if (seatNumber <= totalSeats) {
                    createSeat(seatNumber);
                    seatNumber++;
                }
            }
        }

        function createSeat(seatNo) {
            const seatDiv = document.createElement('div');
            seatDiv.classList.add('seat');
            seatDiv.dataset.seat = seatNo;
            seatDiv.textContent = seatNo;

            if (bookedSeats.includes(seatNo)) {
                seatDiv.classList.add('booked');
                seatDiv.style.pointerEvents = "none";
            } else if (pendingSeats.includes(seatNo)) {
                seatDiv.classList.add('pending');
                seatDiv.style.pointerEvents = "none";
            } else {
                seatDiv.classList.add('available');
            }

            seatContainer.appendChild(seatDiv);
        }

        // Handle seat selection
        seatContainer.addEventListener('click', (e) => {
            const seat = e.target;

            if (seat.classList.contains('available')) {
                const seatNo = parseInt(seat.dataset.seat);

                if (seat.classList.contains('selected')) {
                    seat.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(s => s !== seatNo);
                } else {
                    seat.classList.add('selected');
                    selectedSeats.push(seatNo);
                }

                updateSelection();
            }
        });

        function updateSelection() {
            selectedSeatsEl.textContent = selectedSeats.length > 0 ? selectedSeats.sort((a, b) => a - b).join(', ') : 'Not selected';
            const totalPrice = selectedSeats.length * seatPrice;
            totalPriceEl.textContent = `NPR ${totalPrice}`;

            hiddenSeats.value = JSON.stringify(selectedSeats);
            hiddenAmount.value = totalPrice;

            bookNowBtn.disabled = selectedSeats.length === 0;
        }
    </script>
</body>
</html>