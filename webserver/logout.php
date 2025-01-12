<?php
session_start();

// Beende die Session
session_unset();
session_destroy();

// Leite den Benutzer zur Login-Seite weiter
header('Location: DeskPlanner.html?logout=logout');
exit;
?>
