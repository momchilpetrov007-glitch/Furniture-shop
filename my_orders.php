<?php
require_once 'config.php';

// Проверка дали потребителят е логнат
if (!isLoggedIn() || isAdmin()) {
    redirect('index.php');
}

// Вземане на поръчките на потребителя
$query = "SELECT * FROM orders WHERE user_id = {$_SESSION['user_id']} ORDER BY created_at DESC";
$orders_result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Моите поръчки - Магазин за Мебели</title>
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
                    <li><a href="my_orders.php">Моите поръчки</a></li>
                    <li><a href="cart.php">Количка 🛒</a></li>
                    <li><a href="logout.php">Изход (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Поръчки -->
    <section class="products">
        <div class="container">
            <h2>Моите поръчки</h2>

            <?php if (mysqli_num_rows($orders_result) === 0): ?>
                <div class="alert alert-error">
                    Все още нямате направени поръчки. <a href="index.php">Разгледайте нашите продукти</a>
                </div>
            <?php else: ?>
                <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                    <div class="table-container">
                        <h3>Поръчка #<?php echo $order['id']; ?> 
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
                        <p><strong>Дата:</strong> <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></p>
                        <p><strong>Адрес за доставка:</strong> <?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
                        <p><strong>Телефон:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                        <?php if ($order['notes']): ?>
                            <p><strong>Бележки:</strong> <?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                        <?php endif; ?>

                        <!-- Артикули в поръчката -->
                        <?php
                        $items_query = "SELECT oi.*, f.name, f.category, f.image 
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
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
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
