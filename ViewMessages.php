<?php
    session_start();
    include("connection.php");

    if(!isset($_SESSION['username'])){
        header("Location: adminLogin.php");  
        exit();
    }

    // Handle message deletion
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $msg_id = $_GET['delete'];
        $deleteQuery = "DELETE FROM contact WHERE id = $msg_id";
        if (mysqli_query($conn, $deleteQuery)) {
            echo ("<script>
                window.alert('Message deleted successfully!');
                window.location.href='ViewMessages.php';
            </script>");
        }
    }

    // Fetch all contact messages
    $query = "SELECT * FROM contact ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    $totalMessages = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Contact Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/slide.css">
    <style>
        .messages-container {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .message-card {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 4px solid #2196F3;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .message-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .sender-info h3 {
            margin: 0;
            color: #333;
            font-size: 18px;
        }

        .sender-info p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }

        .message-actions {
            display: flex;
            gap: 10px;
        }

        .delete-btn {
            background: #f44336;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .delete-btn:hover {
            background: #d32f2f;
        }

        .message-content {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            line-height: 1.6;
            color: #555;
        }

        .message-footer {
            display: flex;
            justify-content: space-between;
            color: #999;
            font-size: 12px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .no-messages {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .messages-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .messages-header h2 {
            color: #333;
            margin: 0;
        }

        .total-badge {
            background: #2196F3;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <header>
            <img src="image/AdminProfile.jpg" alt="Admin">
            <p><?php echo $_SESSION['username']; ?></p>
        </header>
        <ul>
            <li><a href="adminDash.php">Dashboard</a></li>
            <li><a href="ManageRoute.php">Manage Routes</a></li>
            <li><a href="ManageBuses.php">Manage Buses</a></li>
            <li><a href="ManageBooking.php">Manage Bookings</a></li>
            <li><a href="ViewMessages.php">Messages</a></li>
            <li><a href="adminLogout.php">Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="messages-header">
            <h1>Contact Messages</h1>
            <div class="total-badge"><?php echo $totalMessages; ?> Messages</div>
        </div>

        <div class="messages-container">
            <?php
            if ($totalMessages > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "
                    <div class='message-card'>
                        <div class='message-header'>
                            <div class='sender-info'>
                                <h3>" . htmlspecialchars($row['name']) . "</h3>
                                <p>📧 " . htmlspecialchars($row['email']) . "</p>
                                <p>📱 " . htmlspecialchars($row['phone']) . "</p>
                            </div>
                            <div class='message-actions'>
                                <a href='ViewMessages.php?delete=" . $row['id'] . "' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this message?');\">
                                    <i class='fas fa-trash'></i> Delete
                                </a>
                            </div>
                        </div>
                        <div class='message-content'>
                            " . nl2br(htmlspecialchars($row['message'])) . "
                        </div>
                        <div class='message-footer'>
                            <span>Message ID: #" . $row['id'] . "</span>
                        </div>
                    </div>
                    ";
                }
            } else {
                echo "<div class='no-messages'><h3>No messages yet</h3><p>Users haven't sent any contact messages.</p></div>";
            }
            ?>
        </div>
    </div>

</body>
</html>
