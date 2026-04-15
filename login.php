<?php
require_once 'config.php';

// Ако вече е logged in, пренасочи
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin_orders.php');
    } else {
        redirect('index.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = escape($conn, $_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Моля, въведете потребителско име и парола!';
    } else {
        // Проверка в базата данни (username ИЛИ email)
        $query = "SELECT * FROM users WHERE username = '$username' OR email = '$username'";
        $result = mysqli_query($conn, $query);

        if ($user = mysqli_fetch_assoc($result)) {
            // Проверка на паролата
            if (password_verify($password, $user['password'])) {
                // Успешен вход - записване на сесията
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = $user['is_admin']; // ← ВАЖНО!

                // Пренасочване според admin статус
                if ($user['is_admin'] == 1) {
                    redirect('admin_orders.php');
                } else {
                    redirect('index.php');
                }
            } else {
                $error = 'Грешно потребителско име или парола!';
            }
        } else {
            $error = 'Грешно потребителско име или парола!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Мебели Онлайн</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .policy-notice {
            background: linear-gradient(135deg, #F5EFE7 0%, #E8DCC8 100%);
            border: 1px solid #D4A574;
            padding: 1rem;
            margin-top: 1.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
            color: #5D4E37;
            text-align: center;
        }
        
        .policy-notice a {
            color: #8B6F47;
            text-decoration: underline;
            font-weight: 600;
        }
        
        .policy-notice a:hover {
            color: #C17C4A;
        }
    </style>
</head>
<body>
    <!-- Навигация -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="index.php" class="logo">МЕБЕЛИ ОНЛАЙН</a>
                <ul class="nav-menu">
                    <li><a href="index.php">НАЧАЛО</a></li>
                    <li><a href="catalog.php">КАТАЛОГ</a></li>
                    <li><a href="register.php">РЕГИСТРАЦИЯ</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="form-container">
        <h2>Вход в системата</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Потребителско име или Email</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                       required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Парола</label>
                <div class="password-input-wrapper">
                    <input type="password" id="password" name="password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">👁️</button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Вход
            </button>
        </form>

        <p style="text-align: center; margin-top: 1.5rem; color: #8B6F47;">
            Нямате акаунт? <a href="register.php" style="color: #C17C4A; font-weight: 600;">Регистрирайте се тук</a>
        </p>

        <div class="policy-notice">
            Влизайки в системата, вие се съгласявате с нашите 
            <a href="terms_of_service.php" target="_blank">Условия за ползване</a> и 
            <a href="privacy_policy.php" target="_blank">Политика за поверителност</a>.
        </div>
    </div>

    <!-- Футър -->
    <footer class="footer">
        <div class="container">
            <p style="text-align: center;">
                &copy; 2024 Мебели Онлайн. Всички права запазени. | 
                <a href="privacy_policy.php" style="color: #D4A574;">Политика за поверителност</a> | 
                <a href="terms_of_service.php" style="color: #D4A574;">Условия за ползване</a>
            </p>
        </div>
    </footer>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
