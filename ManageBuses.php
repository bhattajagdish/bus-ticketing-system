<?php 
session_start();
include("connection.php"); 
if(!isset($_SESSION['username'])){
    header("Location: adminLogin.php");  
  }
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Panel of Bus</title>
  <link rel="stylesheet" href="css/slide.css">
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
    <div class="header">
         <h1>Manage Buses</h1>
    </div>

      <?php
          $sqlget = "SELECT * FROM bus";
          $sqldata = mysqli_query($conn, $sqlget);

          // Check if the query executed successfully
          if (!$sqldata) {
              die("Query failed: " . mysqli_error($conn));
          }

          // Display data if available
          if (mysqli_num_rows($sqldata) > 0) {
              echo "<table class='table'>";
              echo "<tr>
                      <th>ID</th>
                      <th>Bus Name</th>
                      <th>Tel Number</th>
                      <th>Bus Number Plate</th>
                      <th>Update</th>
                      <th>Delete</th>
                    </tr>";

              while ($row = mysqli_fetch_assoc($sqldata)) {
                  echo "<tr>
                          <td>{$row['id']}</td>
                          <td>{$row['Bus_Name']}</td>
                          <td>{$row['Tel']}</td>
                          <td>{$row['Bus_Number']}</td>
                          <td>
                              <a class='btn btn-update' href='UpdateBus.php?id={$row['id']}'>Update</a>
                          </td>
                          <td>
                              <a class='btn btn-delete' href='deleteBus.php?id={$row['id']}'>Delete</a>
                          </td>
                        </tr>";
              }
              echo "</table>";
          } else {
              echo "<p style='text-align: center; color: white;'>No buses available in the database.</p>";
          }
      ?>

      <br>
      <a href="AddBus.php">
          <button class="btn-add">Add Bus</button>
      </a>
  </div>
</body>
</html>
