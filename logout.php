<?php
session_start();
session_destroy(); // Smaže všechna data uživatele
header("Location: index.php");
exit;
