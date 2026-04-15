<?php
require_once 'config.php';

echo "<h1>Admin Debug Info</h1>";
echo "<style>body { font-family: Arial; padding: 20px; background: #f5f5f5; }</style>";

// Проверка на сесията
echo "<h2>1. Session данни:</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Session е активна<br>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
} else {
    echo "❌ Session не е активна!<br>";
}

// Проверка дали е логнат
echo "<h2>2. isLoggedIn():</h2>";
if (isLoggedIn()) {
    echo "✅ Потребителят Е логнат<br>";
    echo "User ID: " . $_SESSION['user_id'] . "<br>";
    echo "Username: " . $_SESSION['username'] . "<br>";
} else {
    echo "❌ Потребителят НЕ Е логнат<br>";
}

// Проверка дали е админ
echo "<h2>3. isAdmin():</h2>";
if (isset($_SESSION['is_admin'])) {
    echo "is_admin стойност: " . $_SESSION['is_admin'] . "<br>";
    if (isAdmin()) {
        echo "✅ Потребителят Е админ<br>";
    } else {
        echo "❌ Потребителят НЕ Е админ (is_admin = " . $_SESSION['is_admin'] . ")<br>";
    }
} else {
    echo "❌ is_admin променливата НЕ съществува в session!<br>";
}

// Проверка в базата данни
echo "<h2>4. Данни от базата:</h2>";
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT id, username, email, is_admin FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $query);
    
    if ($user = mysqli_fetch_assoc($result)) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>is_admin</th></tr>";
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . $user['username'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td><strong>" . $user['is_admin'] . "</strong></td>";
        echo "</tr>";
        echo "</table>";
        
        if ($user['is_admin'] == 1) {
            echo "<br>✅ В базата данни is_admin = 1 (ADMIN)";
        } else {
            echo "<br>❌ В базата данни is_admin = " . $user['is_admin'] . " (НЕ Е ADMIN)";
        }
    }
} else {
    echo "Не може да провери базата - потребителят не е логнат.";
}

// Препоръки
echo "<h2>5. Решение на проблема:</h2>";
if (isLoggedIn() && !isAdmin()) {
    echo "<div style='background: #ffe5e5; padding: 15px; border-left: 4px solid red;'>";
    echo "<strong>Проблем намерен!</strong><br>";
    echo "Потребителят е логнат, но не е маркиран като админ.<br><br>";
    echo "<strong>Решение:</strong><br>";
    echo "1. Отидете в phpMyAdmin<br>";
    echo "2. Отворете таблицата 'users'<br>";
    echo "3. Намерете вашия потребител (username: " . htmlspecialchars($_SESSION['username']) . ")<br>";
    echo "4. Променете is_admin от 0 на 1<br>";
    echo "5. Logout и login отново<br>";
    echo "</div>";
} elseif (isLoggedIn() && isAdmin()) {
    echo "<div style='background: #e5ffe5; padding: 15px; border-left: 4px solid green;'>";
    echo "✅ Всичко е наред! Вие сте админ.<br>";
    echo "Ако index.php не пренасочва към admin_orders.php,<br>";
    echo "проверете дали index.php е обновен с admin redirect кода.";
    echo "</div>";
} else {
    echo "<div style='background: #fff5e5; padding: 15px; border-left: 4px solid orange;'>";
    echo "⚠️ Моля, влезте в системата за да тествате admin функциите.";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='login.php'>← Вход</a> | <a href='index.php'>← Начало</a> | <a href='admin_orders.php'>← Admin Панел</a></p>";
?>
