<?php
require_once 'config.php';

// Check if admin is logged in
if (!isAdmin()) {
    redirect('login.php');
}

// Get order ID
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id <= 0) {
    redirect('admin_orders.php');
}

// Fetch order details with user information
$query = "SELECT o.*, u.username, u.email 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          WHERE o.id = $order_id";
$result = mysqli_query($conn, $query);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    redirect('admin_orders.php');
}

// Fetch order items
$items_query = "SELECT oi.*, f.name as furniture_name, f.image 
                FROM order_items oi 
                LEFT JOIN furniture f ON oi.furniture_id = f.id 
                WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поръчка #<?php echo $order_id; ?> - Админ панел</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .order-details-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 1.5rem;
            padding: 0.75rem 1.5rem;
            background: #8B6F47;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .back-button:hover {
            background: #6B5437;
        }

        .order-header {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .order-header h1 {
            color: #5D4E37;
            margin: 0 0 1rem 0;
        }

        .order-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .meta-item {
            padding: 1rem;
            background: #F5EFE7;
            border-radius: 6px;
        }

        .meta-label {
            font-weight: 600;
            color: #8B6F47;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .meta-value {
            color: #5D4E37;
            font-size: 1rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-pending { background: #FFF3CD; color: #856404; }
        .status-confirmed { background: #D1ECF1; color: #0C5460; }
        .status-shipped { background: #D4EDDA; color: #155724; }
        .status-delivered { background: #C3E6CB; color: #155724; }
        .status-cancelled { background: #F8D7DA; color: #721C24; }

        .info-sections {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .info-card h2 {
            color: #5D4E37;
            margin: 0 0 1.5rem 0;
            padding-bottom: 1rem;
            border-bottom: 2px solid #D4A574;
        }

        .info-row {
            display: flex;
            margin-bottom: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #F5EFE7;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #8B6F47;
            min-width: 150px;
        }

        .info-value {
            color: #5D4E37;
            flex: 1;
        }

        .items-section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .items-section h2 {
            color: #5D4E37;
            margin: 0 0 1.5rem 0;
            padding-bottom: 1rem;
            border-bottom: 2px solid #D4A574;
        }

        .order-item {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            margin-bottom: 1rem;
            background: #FDFBF7;
            border-radius: 8px;
            border: 1px solid #F5EFE7;
        }

        .item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 1.5rem;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #5D4E37;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .item-info {
            color: #8B6F47;
            font-size: 0.9rem;
        }

        .item-price {
            text-align: right;
            min-width: 120px;
        }

        .item-unit-price {
            color: #8B6F47;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .item-total-price {
            font-weight: 700;
            color: #5D4E37;
            font-size: 1.2rem;
        }

        .order-summary {
            margin-top: 2rem;
            padding: 1.5rem;
            background: #F5EFE7;
            border-radius: 8px;
            text-align: right;
        }

        .summary-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .summary-label {
            font-weight: 600;
            color: #8B6F47;
            margin-right: 2rem;
        }

        .summary-value {
            min-width: 120px;
            text-align: right;
            color: #5D4E37;
        }

        .total-row {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #D4A574;
        }

        .total-row .summary-label,
        .total-row .summary-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #5D4E37;
        }

        .action-buttons {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #8B6F47;
            color: white;
        }

        .btn-primary:hover {
            background: #6B5437;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 111, 71, 0.3);
        }

        .btn-secondary {
            background: #D4A574;
            color: #5D4E37;
        }

        .btn-secondary:hover {
            background: #C49564;
        }

        @media (max-width: 768px) {
            .info-sections {
                grid-template-columns: 1fr;
            }

            .order-item {
                flex-direction: column;
                text-align: center;
            }

            .item-image {
                margin-right: 0;
                margin-bottom: 1rem;
            }

            .item-price {
                text-align: center;
                margin-top: 1rem;
            }

            .action-buttons {
                flex-direction: column;
            }
        }

        .warning-box {
            background: #FFF3CD;
            border: 1px solid #FFC107;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            color: #856404;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
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

    <div class="order-details-container">
        <a href="admin_orders.php" class="back-button">← Назад към поръчките</a>

        <!-- Order Header -->
        <div class="order-header">
            <h1>Поръчка #<?php echo $order_id; ?></h1>
            
            <div class="order-meta">
                <div class="meta-item">
                    <div class="meta-label">Дата:</div>
                    <div class="meta-value"><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></div>
                </div>
                
                <div class="meta-item">
                    <div class="meta-label">Метод на плащане:</div>
                    <div class="meta-value">
                        <?php 
                        // Fix Warning 1: Check if payment_method exists
                        if (isset($order['payment_method'])) {
                            echo $order['payment_method'] === 'card' ? 'Карта' : 'Наложен платеж';
                        } else {
                            echo 'Не е посочен';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="meta-item">
                    <div class="meta-label">Статус:</div>
                    <div class="meta-value">
                        <?php 
                        // Fix Warning 2: Check if status exists
                        $status = isset($order['status']) ? $order['status'] : 'pending';
                        $status_text = [
                            'pending' => 'Изчакваща',
                            'confirmed' => 'Потвърдена',
                            'shipped' => 'Изпратена',
                            'delivered' => 'Доставена',
                            'cancelled' => 'Отменена'
                        ];
                        ?>
                        <span class="status-badge status-<?php echo $status; ?>">
                            <?php echo $status_text[$status] ?? 'Неизвестен'; ?>
                        </span>
                    </div>
                </div>

                <div class="meta-item">
                    <div class="meta-label">Обща сума:</div>
                    <div class="meta-value" style="font-size: 1.3rem; font-weight: 700; color: #8B6F47;">
                        <?php echo number_format($order['total_price'], 2); ?> €
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Sections -->
        <div class="info-sections">
            <!-- Client Information -->
            <div class="info-card">
                <h2>Информация за клиента</h2>
                
                <div class="info-row">
                    <div class="info-label">Потребител:</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['username'] ?? 'N/A'); ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Телефон:</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Адрес:</div>
                    <div class="info-value"><?php echo nl2br(htmlspecialchars($order['delivery_address'] ?? 'N/A')); ?></div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="info-card">
                <h2>Детайли на поръчката</h2>
                
                <div class="info-row">
                    <div class="info-label">Дата:</div>
                    <div class="info-value"><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Метод на плащане:</div>
                    <div class="info-value">
                        <?php 
                        if (isset($order['payment_method'])) {
                            echo $order['payment_method'] === 'card' ? 'Карта' : 'Наложен платеж';
                        } else {
                            echo 'Не е посочен';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Статус:</div>
                    <div class="info-value">
                        <span class="status-badge status-<?php echo $status; ?>">
                            <?php echo $status_text[$status] ?? 'Неизвестен'; ?>
                        </span>
                    </div>
                </div>
                
                <?php if (!empty($order['notes'])): ?>
                <div class="info-row">
                    <div class="info-label">Бележки:</div>
                    <div class="info-value"><?php echo nl2br(htmlspecialchars($order['notes'])); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Order Items -->
        <div class="items-section">
            <h2>Поръчани артикули</h2>
            
            <?php 
            $total = 0;
            while ($item = mysqli_fetch_assoc($items_result)): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <div class="order-item">
                    <img src="images/<?php echo htmlspecialchars($item['image'] ?? 'placeholder.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($item['furniture_name']); ?>" 
                         class="item-image">
                    
                    <div class="item-details">
                        <div class="item-name"><?php echo htmlspecialchars($item['furniture_name']); ?></div>
                        <div class="item-info">
                            Количество: <?php echo $item['quantity']; ?> бр. × 
                            <?php echo number_format($item['price'], 2); ?> €
                        </div>
                    </div>
                    
                    <div class="item-price">
                        <div class="item-unit-price">
                            <?php echo number_format($item['price'], 2); ?> € / бр.
                        </div>
                        <div class="item-total-price">
                            <?php echo number_format($subtotal, 2); ?> €
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <!-- Order Summary -->
            <div class="order-summary">
                <div class="summary-row total-row">
                    <div class="summary-label">Обща сума:</div>
                    <div class="summary-value"><?php echo number_format($order['total_price'], 2); ?> €</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="edit_order.php?id=<?php echo $order_id; ?>" class="btn btn-primary">
                Промени статус
            </a>
            <a href="admin_orders.php" class="btn btn-secondary">
                Затвори
            </a>
        </div>
    </div>
</body>
</html>
