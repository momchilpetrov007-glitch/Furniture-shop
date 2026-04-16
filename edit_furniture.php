<?php
require_once 'config.php';

// Проверка дали потребителят е администратор
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$error = '';
$success = '';

// Вземане на ID на мебелта
if (!isset($_GET['id'])) {
    redirect('admin_panel.php');
}

$furniture_id = (int)$_GET['id'];

// Вземане на данните на мебелта
$query = "SELECT * FROM furniture WHERE id = $furniture_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    redirect('admin_panel.php');
}

$furniture = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = escape($conn, $_POST['name']);
    $description = escape($conn, $_POST['description']);
    $category = escape($conn, $_POST['category']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    // Обработка на нова снимка (ако е качена)
    $image_name = $furniture['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            // Създаване на уникално име на файла
            $new_image_name = uniqid() . '_' . time() . '.' . $file_extension;
            $upload_path = 'images/' . $new_image_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Изтриване на старата снимка
                if (file_exists('images/' . $furniture['image'])) {
                    unlink('images/' . $furniture['image']);
                }
                $image_name = $new_image_name;
            } else {
                $error = 'Грешка при качване на новата снимка!';
            }
        } else {
            $error = 'Невалиден формат на снимката! Позволени формати: JPG, JPEG, PNG, GIF, WEBP';
        }
    }

    // Валидация
    if (empty($name) || empty($category) || $price <= 0) {
        $error = 'Моля, попълнете всички задължителни полета правилно!';
    }

    // Актуализиране в базата данни
    if (empty($error)) {
        $update_query = "UPDATE furniture 
                        SET name = '$name', 
                            description = '$description', 
                            category = '$category', 
                            price = $price, 
                            image = '$image_name', 
                            stock = $stock 
                        WHERE id = $furniture_id";

        if (mysqli_query($conn, $update_query)) {
            $success = 'Мебелта е актуализирана успешно!';
            // Презареждане на данните
            $result = mysqli_query($conn, $query);
            $furniture = mysqli_fetch_assoc($result);
        } else {
            $error = 'Грешка при актуализиране: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактиране на мебел - Админ панел</title>
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

    <!-- Форма за редактиране на мебел -->
    <div class="form-container">
        <h2>Редактиране на мебел</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Име на мебелта *</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($furniture['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Описание</label>
                <textarea id="description" name="description"><?php echo htmlspecialchars($furniture['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="category">Категория *</label>
                <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($furniture['category']); ?>" required>
            </div>

            <div class="form-group">
                <label for="price">Цена (лв.) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $furniture['price']; ?>" required>
            </div>

            <div class="form-group">
                <label for="stock">Наличност *</label>
                <input type="number" id="stock" name="stock" min="0" value="<?php echo $furniture['stock']; ?>" required>
            </div>

            <div class="form-group">
                <label>Текуща снимка</label>
                <img src="images/<?php echo htmlspecialchars($furniture['image']); ?>" 
                     alt="<?php echo htmlspecialchars($furniture['name']); ?>" 
                     style="max-width: 300px; border-radius: 5px;"
                     onerror="this.src='images/placeholder.jpg'">
            </div>

            <div class="form-group">
                <label for="image">Нова снимка (оставете празно, за да запазите текущата)</label>
                <input type="file" id="image" name="image" accept="image/*">
                <small>Позволени формати: JPG, JPEG, PNG, GIF, WEBP</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Актуализирай</button>
        </form>

        <div class="form-link">
            <p><a href="admin_panel.php">← Обратно към админ панела</a></p>
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
