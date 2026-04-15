<?php
require_once 'config.php';

// Проверка дали потребителят е администратор
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Обработка на промяна на статус на поръчка
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = escape($conn, $_POST['status']);

    $update_query = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    mysqli_query($conn, $update_query);
}

// Вземане на всички поръчки
$orders_query = "SELECT o.*, u.username, u.full_name 
                 FROM orders o 
                 JOIN users u ON o.user_id = u.id 
                 ORDER BY o.created_at DESC";
$orders_result = mysqli_query($conn, $orders_query);

// Вземане на всички мебели
$furniture_query = "SELECT * FROM furniture ORDER BY created_at DESC";
$furniture_result = mysqli_query($conn, $furniture_query);
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панел - Магазин за Мебели</title>
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

    <!-- Админ панел -->
    <section class="admin-panel">
        <div class="container">
            <h1>Администраторски панел</h1>

            <!-- Секция с поръчки -->
            <div class="admin-section">
                <h2>Управление на поръчки</h2>

                <?php if (mysqli_num_rows($orders_result) === 0): ?>
                    <div class="alert alert-error">Все още няма направени поръчки.</div>
                <?php else: ?>
                    <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                        <div class="table-container">
                            <h3>
                                Поръчка #<?php echo $order['id']; ?> 
                                - <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php 
                                    $statuses = [
                                        'pending' => 'Очаква обработка',
                                        'processing' => 'В процес на обработка',
                                        'completed' => 'Завършена',
                                        'cancelled' => 'Отказана'
                                    ];
                                    echo $statuses[$order['status']];
                                    ?>
                                </span>
                            </h3>

                            <p><strong>Клиент:</strong> <?php echo htmlspecialchars($order['full_name']); ?> (<?php echo htmlspecialchars($order['username']); ?>)</p>
                            <p><strong>Дата:</strong> <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></p>
                            <p><strong>Адрес за доставка:</strong> <?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
                            <p><strong>Телефон:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                            <?php if ($order['notes']): ?>
                                <p><strong>Бележки:</strong> <?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                            <?php endif; ?>

                            <!-- Артикули в поръчката -->
                            <?php
                            $items_query = "SELECT oi.*, f.name, f.category 
                                           FROM order_items oi 
                                           JOIN furniture f ON oi.furniture_id = f.id 
                                           WHERE oi.order_id = {$order['id']}";
                            $items_result = mysqli_query($conn, $items_query);
                            ?>

                            <table style="margin-top: 1rem;">
                                <thead>
                                    <tr>
                                        <th>Продукт</th>
                                        <th>Категория</th>
                                        <th>Цена</th>
                                        <th>Количество</th>
                                        <th>Междинна сума</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td><?php echo htmlspecialchars($item['category']); ?></td>
                                            <td><?php echo number_format($item['price'], 2); ?> лв.</td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo number_format($item['price'] * $item['quantity'], 2); ?> лв.</td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" style="text-align: right;"><strong>Обща сума:</strong></td>
                                        <td><strong><?php echo number_format($order['total_price'], 2); ?> лв.</strong></td>
                                    </tr>
                                </tfoot>
                            </table>

                            <!-- Промяна на статус -->
                            <form method="POST" action="" style="margin-top: 1rem;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <div style="display: flex; gap: 1rem; align-items: center;">
                                    <label><strong>Промяна на статус:</strong></label>
                                    <select name="status" required>
                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Очаква обработка</option>
                                        <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>В процес на обработка</option>
                                        <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Завършена</option>
                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Отказана</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary">Актуализирай</button>
                                </div>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

            <!-- Секция с мебели -->
            <div class="admin-section">
                <h2>Управление на мебели</h2>
                <a href="add_furniture.php" class="btn btn-success" style="margin-bottom: 1rem;">➕ Добави нова мебел</a>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Име</th>
                                <th>Категория</th>
                                <th>Цена</th>
                                <th>Наличност</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($furniture = mysqli_fetch_assoc($furniture_result)): ?>
                                <tr>
                                    <td><?php echo $furniture['id']; ?></td>
                                    <td><?php echo htmlspecialchars($furniture['name']); ?></td>
                                    <td><?php echo htmlspecialchars($furniture['category']); ?></td>
                                    <td><?php echo number_format($furniture['price'], 2); ?> лв.</td>
                                    <td><?php echo $furniture['stock']; ?></td>
                                    <td>
                                        <a href="edit_furniture.php?id=<?php echo $furniture['id']; ?>" class="btn btn-primary">Редактирай</a>
                                        <a href="delete_furniture.php?id=<?php echo $furniture['id']; ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirm('Сигурни ли сте, че искате да изтриете тази мебел?');">
                                            Изтрий
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Футър -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Магазин за Мебели. Всички права запазени.</p>
        </div>
    </footer>
</body>
</html>
