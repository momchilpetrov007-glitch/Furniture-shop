<?php
require_once 'config.php';

// Унищожаване на сесията
session_destroy();

// Пренасочване към главната страница
redirect('index.php');
?>
