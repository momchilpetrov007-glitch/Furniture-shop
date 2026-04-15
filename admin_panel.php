<?php
require_once 'config.php';

// Проверка дали потребителят е админ
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Пренасочване към новия admin панел с 3 секции
redirect('admin_orders.php');
?>
