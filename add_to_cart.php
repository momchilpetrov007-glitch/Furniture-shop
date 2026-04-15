<?php
require_once 'config.php';

// Проверка дали потребителят е логнат
if (!isLoggedIn()) {
    redirect('login.php');
}

// Проверка дали е администратор
if (isAdmin()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $furniture_id = (int)$_POST['furniture_id'];
    $quantity = (int)$_POST['quantity'];

    // Проверка на наличността
    $query = "SELECT * FROM furniture WHERE id = $furniture_id";
    $result = mysqli_query($conn, $query);
    $furniture = mysqli_fetch_assoc($result);

    if ($furniture && $quantity > 0 && $quantity <= $furniture['stock']) {
        // Инициализиране на количката, ако не съществува
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        // Добавяне или увеличаване на количеството
        if (isset($_SESSION['cart'][$furniture_id])) {
            $new_quantity = $_SESSION['cart'][$furniture_id] + $quantity;
            if ($new_quantity <= $furniture['stock']) {
                $_SESSION['cart'][$furniture_id] = $new_quantity;
            } else {
                $_SESSION['cart'][$furniture_id] = $furniture['stock'];
            }
        } else {
            $_SESSION['cart'][$furniture_id] = $quantity;
        }
    }
}

redirect('cart.php');
?>
