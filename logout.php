<?php
session_start();
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to the home page or any other page after logout
header("Location: index.php"); // Change index.php to your desired home page
exit();
?>
