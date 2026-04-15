<?php

session_start();

session_destroy();

echo ("<script>
    window.alert('Do you need to Logout?');
    window.location.href='adminLogin.php';
    </script>");

?>
