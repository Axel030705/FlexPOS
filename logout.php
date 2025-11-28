<?php
// Start the session if it hasn't been started already
session_start();

// Destroy the session to log the user out
session_destroy();

// Optionally, you can redirect the user to a login page or homepage after logging out
header("Location: index.php");
exit;
?>
