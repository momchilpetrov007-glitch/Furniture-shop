<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = escape($conn, $_POST['username']);
    $email = escape($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Проверка за съгласие с условията
    $terms_accepted = isset($_POST['terms_accepted']) ? 1 : 0;
    $privacy_accepted = isset($_POST['privacy_accepted']) ? 1 : 0;

    // Валидация
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Моля, попълнете всички задължителни полета!';
    } elseif (!$terms_accepted) {
        $error = 'Трябва да се съгласите с Условията за ползване!';
    } elseif (!$privacy_accepted) {
        $error = 'Трябва да се съгласите с Политиката за поверителност!';
    } elseif ($password !== $confirm_password) {
        $error = 'Паролите не съвпадат!';
    } elseif (strlen($password) < 6) {
        $error = 'Паролата трябва да е поне 6 символа!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Невалиден email адрес!';
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
            $insert_query = "INSERT INTO users (username, email, password, is_admin, created_at) 
                            VALUES ('$username', '$email', '$hashed_password', 0, NOW())";

            if (mysqli_query($conn, $insert_query)) {
                $success = 'Регистрацията е успешна! Можете да влезете в профила си.';
                // Изчистване на полетата
                $_POST = array();
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
    <title>Регистрация - Мебели Онлайн</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .checkbox-group {
            margin: 1.5rem 0;
        }
        
        .checkbox-label {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            color: #5D4E37;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .checkbox-label input[type="checkbox"] {
            margin-top: 0.3rem;
            width: 18px;
            height: 18px;
            cursor: pointer;
            flex-shrink: 0;
        }
        
        .checkbox-label a {
            color: #8B6F47;
            text-decoration: underline;
            font-weight: 600;
        }
        
        .checkbox-label a:hover {
            color: #C17C4A;
        }
        
        .required-notice {
            background: #FFF9E6;
            border-left: 4px solid #C17C4A;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
            color: #5D4E37;
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
                    <li><a href="login.php">ВХОД</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="form-container">
        <h2>Регистрация</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <p style="margin-top: 1rem;">
                    <a href="login.php" class="btn btn-primary">Влезте в профила си</a>
                </p>
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Потребителско име *</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="email">Email адрес *</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="password">Парола *</label>
                    <input type="password" id="password" name="password" required>
                    <small style="color: #8B6F47;">Минимум 6 символа</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Потвърдете паролата *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="required-notice">
                    <strong>⚠️ Важно:</strong> За да създадете акаунт, трябва да се съгласите с нашите условия и политики.
                </div>

                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms_accepted" id="terms_accepted" required>
                        <span>
                            Прочетох и се съгласявам с 
                            <a href="terms_of_service.php" target="_blank">Условията за ползване</a> *
                        </span>
                    </label>
                </div>

                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="privacy_accepted" id="privacy_accepted" required>
                        <span>
                            Прочетох и се съгласявам с 
                            <a href="privacy_policy.php" target="_blank">Политиката за поверителност</a> *
                        </span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Регистрирай се
                </button>
            </form>

            <p style="text-align: center; margin-top: 1.5rem; color: #8B6F47;">
                Вече имате акаунт? <a href="login.php" style="color: #C17C4A; font-weight: 600;">Влезте тук</a>
            </p>
        <?php endif; ?>
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
</body>
</html>
