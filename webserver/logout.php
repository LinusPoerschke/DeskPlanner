<?php
session_start();

// End the session
session_unset();
session_destroy();

// Redirect the user to the login page
header('Location: DeskPlanner.html');
exit;
?>
