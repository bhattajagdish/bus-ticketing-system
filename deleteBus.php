<?php

include 'connection.php';

$ID = $_GET['id'];
$sql = " DELETE FROM `bus` WHERE ID = $ID " ;
$query = mysqli_query($conn,$sql);

//header("location:adminDash.php");

echo ("<script>
    window.alert('Succesfully Bus Deleted!!!');
    window.location.href='ManageBuses.php';
    </script>");

?>