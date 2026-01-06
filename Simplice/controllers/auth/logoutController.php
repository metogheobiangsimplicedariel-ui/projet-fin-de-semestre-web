<?php
// On détruit la session
session_destroy();

// On redirige vers le login
header('Location: index.php?page=login');
exit();
?>