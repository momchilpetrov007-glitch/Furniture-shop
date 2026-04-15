<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = escape($conn, $_POST['username']);
    $email = escape($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = escape($conn, $_POST['full_name']);
    $phone = escape($conn, $_POST['phone']);
    $address = escape($conn, $_POST['address']);

    // Валидация
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = 'Моля, попълнете всички задължителни полета!';
    } elseif ($password !== $confirm_password) {
        $error = 'Паролите не съвпадат!';
    } elseif (strlen($password) < 6) {
        $error = 'Паролата трябва да е поне 6 символа!';
    } else {
        // Проверка дали потребителското име или email вече съществуват
        $check_query = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Потребителското име или email вече съществуват!';
        } else {
            // Хеширане на паролата
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Вмъкване в базата данни
            $insert_query = "INSERT INTO users (username, email, password, full_name, phone, address, role) 
                            VALUES ('$username', '$email', '$hashed_password', '$full_name', '$phone', '$address', 'user')";

            if (mysqli_query($conn, $insert_query)) {
                $success = 'Регистрацията е успешна! Може да влезете в профила си.';
            } else {
                $error = 'Грешка при регистрация: ' . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Магазин за Мебели</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Навигация -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="index.php" class="logo">🪑 Мебели Онлайн</a>
                <ul class="nav-menu">
                    <li><a href="index.php">Начало</a></li>
                    <li><a href="login.php">Вход</a></li>
                    <li><a href="register.php">Регистрация</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Форма за регистрация -->
    <div class="form-container">
        <h2>Регистрация</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Потребителско име *</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Парола *</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Потвърди парола *</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <label for="full_name">Пълно име *</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>

            <div class="form-group">
                <label for="phone">Телефон</label>
                <input type="tel" id="phone" name="phone">
            </div>

            <div class="form-group">
                <label for="address">Адрес</label>
                <textarea id="address" name="address"></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Регистрирай се</button>
        </form>

        <div class="form-link">
            <p>Вече имате акаунт? <a href="login.php">Влезте тук</a></p>
        </div>
    </div>

    <!-- Футър -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Магазин за Мебели. Всички права запазени.</p>
        </div>
    </footer>
</body>
</html>
