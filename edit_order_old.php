<?php
require_once 'config.php';

// Проверка дали потребителят е админ
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Проверка за ID
if (!isset($_GET['id'])) {
    redirect('admin_orders.php');
}

$order_id = intval($_GET['id']);

// Обработка на формата
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = escape($conn, $_POST['status']);
    
    $update_query = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
    
    if (mysqli_query($conn, $update_query)) {
        redirect("view_order.php?id=$order_id");
    } else {
        $error = "Грешка при актуализиране на статуса!";
    }
}

// Вземане на информация за поръчката
$order_query = "SELECT o.*, u.username 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = $order_id";
$order_result = mysqli_query($conn, $order_query);

if (!$order = mysqli_fetch_assoc($order_result)) {
    redirect('admin_orders.php');
}

$status_labels = [
    'pending' => 'Обработва се',
    'confirmed' => 'Потвърдена',
    'shipped' => 'Изпратена',
    'delivered' => 'Доставена',
    'cancelled' => 'Отказана'
];
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактиране на поръчка #<?php echo $order_id; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-container {
            padding: 3rem 0;
            background-color: #F5EFE7;
            min-height: 70vh;
        }
        
        .edit-form {
            max-width: 600px;
            margin: 0 auto;
            background: #FDFBF7;
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(139, 111, 71, 0.1);
        }
        
        .edit-form h1 {
            color: #5D4E37;
            margin-bottom: 2rem;
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
                    <li><a href="admin_orders.php">АДМИН ПАНЕЛ</a></li>
                    <li><a href="logout.php">ИЗХОД (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="edit-container">
        <div class="container">
            <div class="edit-form">
                <h1>Редактиране на поръчка #<?php echo $order_id; ?></h1>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <p style="margin-bottom: 2rem; color: #8B6F47;">
                    <strong>Клиент:</strong> <?php echo htmlspecialchars($order['username']); ?><br>
                    <strong>Дата:</strong> <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?><br>
                    <strong>Обща сума:</strong> €<?php echo number_format($order['total_price'], 2); ?>
                </p>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="status">Статус на поръчката</label>
                        <select name="status" id="status" required>
                            <?php foreach ($status_labels as $value => $label): ?>
                                <option value="<?php echo $value; ?>" <?php echo $order['status'] === $value ? 'selected' : ''; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Запази промените</button>
                        <a href="view_order.php?id=<?php echo $order_id; ?>" class="btn btn-secondary" style="flex: 1; text-align: center;">Отказ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Футър -->
    <footer class="footer">
        <div class="container">
            <p style="text-align: center;">&copy; 2024 Мебели Онлайн. Всички права запазени.</p>
        </div>
    </footer>
</body>
</html>
