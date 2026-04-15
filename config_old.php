<?php
// Конфигурация за връзка с базата данни
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'furniture_shop');

// Създаване на връзка
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Проверка на връзката
if (!$conn) {
    die("Грешка при свързване: " . mysqli_connect_error());
}

// Задаване на кодировка
mysqli_set_charset($conn, "utf8mb4");

// Стартиране на сесия
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Функция за проверка дали потребителят е логнат
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Функция за проверка дали потребителят е администратор
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Функция за пренасочване
function redirect($url) {
    header("Location: $url");
    exit();
}

// Функция за защита от SQL injection
function escape($conn, $string) {
    return mysqli_real_escape_string($conn, $string);
}
?>
