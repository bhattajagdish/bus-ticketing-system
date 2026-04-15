<?php
session_start();


// Destroy the session
session_destroy();

echo ("<script>
    window.alert('Do you need to Logout?');
    window.location.href='user-login.php';
    </script>");

?>