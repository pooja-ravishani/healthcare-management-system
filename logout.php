<?php
session_start();

// remove all session data
session_unset();
session_destroy();

// redirect to login (index)
header("Location: index.php");
exit;
?>