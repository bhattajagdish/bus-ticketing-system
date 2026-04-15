<?php

include 'connection.php';

$ID = $_GET['id'];
$sql = " DELETE FROM `route` WHERE ID = $ID " ;
$query = mysqli_query($conn,$sql);


echo ("<script >
    window.alert('Succesfully Route Deleted!!!');
    window.location.href='adminDash.php';
    </script>");

?>
