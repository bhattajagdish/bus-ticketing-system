<?php
include("nav.php");
include("connection.php");
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>About Us</title>
    <link rel="stylesheet" href="css/slide.css">
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
        background: url(image/1.jpg) center/cover fixed;
        color: #fff;
        line-height: 1.6;
    }


        .about-sec {
            display: flex;
            padding: 1.5rem 0;
            width: 100%;
            justify-content: center;
            background: rgba(1, 2, 2, 0.5);
            margin: 100px auto 2rem;
      
        backdrop-filter: blur(10px);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        gap: 2rem;
        }

        .about-img {
            width: 300px;
            height: 250px;
            margin: 0 2rem;
            border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease;
        }

        .about-img:hover {
        transform: scale(1.02);
    }

        .about-img img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }

        .about-intro {
        flex: 2;
        padding: 1rem 2rem;
        border-left: 3px solid #00b894;
    }

    .about-intro h3 {
        font-size: 2rem;
        margin-bottom: 1.5rem;
        color: #00b894;
        position: relative;
    }

        .about-intro li {
            font-size: 14px;
            width: 100%;
        }

        .about-intro p {
           
            font-size: 14px;
            opacity: .7;
        }

        .header {
        text-align: center;
        margin: 4rem 0;
        position: relative;
    }

    .header h1 {
        font-size: 2.5rem;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 2px;
        position: relative;
        display: inline-block;
        padding-bottom: 0.3rem;
    }



    @media only screen and (max-width: 900px) {
        .about-sec {
            flex-direction: column;
            margin: 100px 1rem 2rem;
            padding: 2rem 1rem;
        }

        .about-img {
            min-width: auto;
            max-width: 500px;
            margin: 0 auto;
        }

        .about-intro {
            border-left: none;
            border-top: 3px solid #00b894;
            padding: 2rem 0 0;
        }

       
    }
    </style>
</head>
<body>

<div class="about-sec">
    <div class="about-img">
        <img src="image/left.jpg">
    </div>
    <div class="about-intro">
        <h3>About Us<span style="color: #00b894;">!</span></h3>
        <p>Welcome to Nepal Bus Ticketing System, your trusted partner for hassle-free travel planning. Our platform
            is designed to simplify the process of reserving bus tickets across Nepal, connecting cities, towns, and
            villages efficiently and conveniently.
            <br><br>
            Since our inception, we have been committed to revolutionizing the way people travel by providing a seamless
            and reliable online bus booking experience. Whether you're planning a trip to the breathtaking mountains,
            visiting family, or traveling for work, we ensure your journey starts with ease and comfort.
        </p>
    </div>
</div>

<div class="header">
    <h1>Our Buses</h1>
</div>

<?php
$sqlget = "SELECT * FROM bus";
$sqldata = mysqli_query($conn, $sqlget) or die('Error getting data');

// Display data if available
if (mysqli_num_rows($sqldata) > 0) {
    echo "<table class='table'>";
    echo "<tr>
            <th>ID</th>
            <th>Bus Name</th>
            <th>Tel Number</th>
             <th>Bus Number Plate</th>
          </tr>";

    while ($row = mysqli_fetch_assoc($sqldata)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['Bus_Name']}</td>
                <td>{$row['Tel']}</td>
                <td>{$row['Bus_Number']}</td>
                
              </tr>";
    }
    echo "</table>";
}
?>

<div class="header">
    <h1>Our Routes of Buses</h1>
</div>

<?php
 $sqlget = "SELECT id, Source, destination, bus_name, bus_number, departure_date, 
 DATE_FORMAT(departure_time, '%h:%i %p') AS departure_time, 
 available_seats, total_seats, cost 
 FROM route";
 $sqldata = mysqli_query($conn, $sqlget) or die('Error getting data');

echo "<table class='table'>";
echo "<tr>
        <th>ID</th>
        <th>Source</th>
        <th>Destination</th>
        <th>Bus Name</th>
        <th>Bus Number</th>
        <th>Departure Date</th>
        <th>Departure Time</th>
        <th>Seats</th>
        <th>Cost</th>
      </tr>";

  // Loop through the rows and include the bus_number column
  while ($row = mysqli_fetch_array($sqldata, MYSQLI_ASSOC)) {
    // Calculate the percentage of available seats
    $total_seats = $row['total_seats'] ?? 0;
    $available_seats = $row['available_seats'] ?? 0;

    if ($total_seats > 0) {
        $percentage = ($available_seats / $total_seats) * 100;
    } else {
        $percentage = 0; // Avoid division by zero
    }

    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['Source']}</td>
            <td>{$row['destination']}</td>
            <td>{$row['bus_name']}</td>
            <td>{$row['bus_number']}</td>
            <td>{$row['departure_date']}</td>
            <td>{$row['departure_time']}</td> 
            <td>
                <div class='seats-info'>
                    <span>{$available_seats} Seats Available</span>
                    <div class='progress-bar'>
                        <div class='progress' style='width: {$percentage}%;'></div>
                    </div>
                    <span>Total Seats: {$total_seats}</span>
                </div>
            </td>
            <td>{$row['cost']}</td>
          </tr>";
}

echo "</table>";
?>

<div class="about-sec">
    <div class="about-img">
        <img src="image/right.jpg">
    </div>
    <div class="about-intro">
        <h3>Why Choose Us?</h3>
        <ol type="1">
            <li>Convenience: Book tickets from the comfort of your home, anytime and anywhere.</li>
            <li>Wide Network: Access buses that connect major destinations as well as remote areas of Nepal.</li>
            <li>Transparency: No hidden charges – clear ticket pricing and instant confirmation.</li>
            <li>Real-Time Information: Stay updated with accurate departure times, routes, and seat availability.</li>
            <li>Customer Support: Our dedicated team is here to assist you with any queries or issues.</li>
        </ol>
<br><br>
        <h3>Our Mission</h3>
        <p>Our mission is to empower travelers in Nepal by offering a modern and reliable Bus Ticketing System. We aim
            to provide a platform that saves your time, eliminates long queues, and ensures a safe and comfortable
            journey for everyone.</p>
    </div>
</div>

</body>
</html>
