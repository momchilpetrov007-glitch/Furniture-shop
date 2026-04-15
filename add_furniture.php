<?php
require_once 'config.php';

// Проверка дали потребителят е администратор
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = escape($conn, $_POST['name']);
    $description = escape($conn, $_POST['description']);
    $category = escape($conn, $_POST['category']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    // Обработка на снимката
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            // Създаване на уникално име на файла
            $image_name = uniqid() . '_' . time() . '.' . $file_extension;
            $upload_path = 'images/' . $image_name;

            // Проверка дали директорията съществува
            if (!file_exists('images')) {
                mkdir('images', 0777, true);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Снимката е качена успешно
            } else {
                $error = 'Грешка при качване на снимката!';
            }
        } else {
            $error = 'Невалиден формат на снимката! Позволени формати: JPG, JPEG, PNG, GIF, WEBP';
        }
    } else {
        $error = 'Моля, изберете снимка!';
    }

    // Валидация
    if (empty($name) || empty($category) || $price <= 0) {
        $error = 'Моля, попълнете всички задължителни полета правилно!';
    }

    // Вмъкване в базата данни
    if (empty($error) && !empty($image_name)) {
        $insert_query = "INSERT INTO furniture (name, description, category, price, image, stock) 
                        VALUES ('$name', '$description', '$category', $price, '$image_name', $stock)";

        if (mysqli_query($conn, $insert_query)) {
            $success = 'Мебелта е добавена успешно!';
        } else {
            $error = 'Грешка при добавяне: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добави мебел - Админ панел</title>
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
                    <li><a href="admin_panel.php">Админ панел</a></li>
                    <li><a href="logout.php">Изход (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Форма за добавяне на мебел -->
    <div class="form-container">
        <h2>Добави нова мебел</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <br><br>
                <a href="admin_panel.php" class="btn btn-primary">Обратно към админ панела</a>
                <a href="add_furniture.php" class="btn btn-success">Добави още мебели</a>
            </div>
        <?php else: ?>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Име на мебелта *</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="description">Описание</label>
                    <textarea id="description" name="description"></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Категория *</label>
                    <input type="text" id="category" name="category" required>
                </div>

                <div class="form-group">
                    <label for="price">Цена (лв.) *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="stock">Наличност *</label>
                    <input type="number" id="stock" name="stock" min="0" value="0" required>
                </div>

                <div class="form-group">
                    <label for="image">Снимка *</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                    <small>Позволени формати: JPG, JPEG, PNG, GIF, WEBP</small>
                </div>

                <button type="submit" class="btn btn-success" style="width: 100%;">Добави мебел</button>
            </form>

            <div class="form-link">
                <p><a href="admin_panel.php">← Обратно към админ панела</a></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Футър -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Магазин за Мебели. Всички права запазени.</p>
        </div>
    </footer>
</body>
</html>
