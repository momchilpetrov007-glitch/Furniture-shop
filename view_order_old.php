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

// Вземане на информация за поръчката
$order_query = "SELECT o.*, u.username, u.email as user_email 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = $order_id";
$order_result = mysqli_query($conn, $order_query);

if (!$order = mysqli_fetch_assoc($order_result)) {
    redirect('admin_orders.php');
}

// Вземане на артикулите от поръчката
$items_query = "SELECT oi.*, f.name, f.image 
                FROM order_items oi 
                JOIN furniture f ON oi.furniture_id = f.id 
                WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);

// Статус labels
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
    <title>Поръчка #<?php echo $order_id; ?> - Админ Панел</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .order-detail-container {
            padding: 3rem 0;
            background-color: #F5EFE7;
            min-height: 70vh;
        }
        
        .order-header {
            background: linear-gradient(135deg, #FDFBF7 0%, #F5EFE7 100%);
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(139, 111, 71, 0.1);
        }
        
        .order-header h1 {
            color: #5D4E37;
            margin-bottom: 0.5rem;
        }
        
        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .info-card {
            background: #FDFBF7;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(139, 111, 71, 0.1);
        }
        
        .info-card h2 {
            color: #5D4E37;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            border-bottom: 2px solid #D4A574;
            padding-bottom: 0.5rem;
        }
        
        .info-row {
            margin-bottom: 1rem;
        }
        
        .info-label {
            color: #8B6F47;
            font-weight: 600;
            display: inline-block;
            width: 150px;
        }
        
        .info-value {
            color: #5D4E37;
        }
        
        .items-table {
            background: #FDFBF7;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(139, 111, 71, 0.1);
            margin-bottom: 2rem;
        }
        
        .items-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .items-table thead {
            background: linear-gradient(135deg, #8B6F47 0%, #5D4E37 100%);
            color: #FDFBF7;
        }
        
        .items-table th,
        .items-table td {
            padding: 1rem;
            text-align: left;
        }
        
        .items-table tbody tr {
            border-bottom: 1px solid #E8DCC8;
        }
        
        .items-table tbody tr:hover {
            background-color: #F5EFE7;
        }
        
        .order-total {
            background: linear-gradient(135deg, #8B6F47 0%, #5D4E37 100%);
            color: #FDFBF7;
            padding: 1.5rem;
            text-align: right;
            font-size: 1.3rem;
            font-weight: 700;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .status-pending { background-color: #FFE5CC; color: #C17C4A; }
        .status-confirmed { background-color: #D4E8FF; color: #4A90C1; }
        .status-shipped { background-color: #D4F1E8; color: #4AC18B; }
        .status-delivered { background-color: #D4F1D4; color: #6B8E4E; }
        .status-cancelled { background-color: #FFD4D4; color: #C14A4A; }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .order-info-grid {
                grid-template-columns: 1fr;
            }
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

    <div class="order-detail-container">
        <div class="container">
            <!-- Header -->
            <div class="order-header">
                <h1>Поръчка #<?php echo $order_id; ?></h1>
                <p style="color: #8B6F47;">
                    <span class="status-badge status-<?php echo $order['status']; ?>">
                        <?php echo $status_labels[$order['status']]; ?>
                    </span>
                </p>
            </div>

            <!-- Информация -->
            <div class="order-info-grid">
                <!-- Клиент -->
                <div class="info-card">
                    <h2>Информация за клиента</h2>
                    <div class="info-row">
                        <span class="info-label">Потребител:</span>
                        <span class="info-value"><?php echo htmlspecialchars($order['username']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($order['user_email']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Телефон:</span>
                        <span class="info-value"><?php echo htmlspecialchars($order['phone']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Адрес:</span>
                        <span class="info-value"><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></span>
                    </div>
                </div>

                <!-- Детайли на поръчката -->
                <div class="info-card">
                    <h2>Детайли на поръчката</h2>
                    <div class="info-row">
                        <span class="info-label">Дата:</span>
                        <span class="info-value"><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Метод на плащане:</span>
                        <span class="info-value">
                            <?php echo $order['payment_method'] === 'cash' ? 'Наложен платеж' : 'Карта'; ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Статус:</span>
                        <span class="info-value"><?php echo $status_labels[$order['status']]; ?></span>
                    </div>
                    <?php if ($order['notes']): ?>
                    <div class="info-row">
                        <span class="info-label">Бележки:</span>
                        <span class="info-value"><?php echo nl2br(htmlspecialchars($order['notes'])); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Продукти -->
            <div class="items-table">
                <table>
                    <thead>
                        <tr>
                            <th>Продукт</th>
                            <th>Цена</th>
                            <th>Количество</th>
                            <th style="text-align: right;">Общо</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        while ($item = mysqli_fetch_assoc($items_result)): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                </td>
                                <td>€<?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?> бр.</td>
                                <td style="text-align: right;"><strong>€<?php echo number_format($subtotal, 2); ?></strong></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="order-total">
                    ОБЩА СУМА: €<?php echo number_format($order['total_price'], 2); ?>
                </div>
            </div>

            <!-- Бутони -->
            <div class="action-buttons">
                <a href="admin_orders.php" class="btn btn-secondary">← Назад към поръчките</a>
                <a href="edit_order.php?id=<?php echo $order_id; ?>" class="btn btn-primary">Редактирай статус</a>
                <button onclick="window.print()" class="btn btn-secondary">🖨 Принтирай</button>
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
