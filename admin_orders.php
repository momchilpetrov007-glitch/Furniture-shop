<?php
require_once 'config.php';

// Check if admin is logged in
if (!isAdmin()) {
    redirect('login.php');
}

// Fetch all orders with user information
$query = "SELECT o.*, u.username, u.email 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $query);

// Calculate statistics - EXCLUDE cancelled orders from revenue
$stats_query = "SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'pending' OR status IS NULL OR status = '' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_orders,
                SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                SUM(CASE WHEN status != 'cancelled' THEN total_price ELSE 0 END) as total_revenue
                FROM orders";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Status translations
$status_text = [
    'pending' => 'Изчакваща',
    'confirmed' => 'Потвърдена', 
    'shipped' => 'Изпратена',
    'delivered' => 'Доставена',
    'cancelled' => 'Отказана',
    '' => 'Обработва се',
    null => 'Обработва се'
];
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление на поръчки - Админ панел</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 1rem;
        }

        /* Header with centered title and subtitle */
        .admin-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .admin-header h1 {
            color: #5D4E37;
            font-size: 2.5rem;
            margin: 0 0 0.5rem 0;
            font-weight: 700;
        }

        .admin-subtitle {
            color: #8B6F47;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        /* Tab navigation - centered buttons */
        .admin-tabs {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .admin-tab {
            padding: 1rem 2.5rem;
            background: white;
            color: #8B6F47;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            border: 2px solid #D4A574;
            border-radius: 8px;
            transition: all 0.3s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .admin-tab:hover {
            background: #F5EFE7;
            border-color: #8B6F47;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 111, 71, 0.2);
        }

        .admin-tab.active {
            background: #8B6F47;
            color: white;
            border-color: #8B6F47;
        }

        .admin-tab-icon {
            font-size: 1.2rem;
        }

        /* Content section title */
        .content-header {
            margin-bottom: 2rem;
        }

        .content-header h2 {
            color: #5D4E37;
            font-size: 1.8rem;
            margin: 0;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #8B6F47;
        }

        .stat-label {
            color: #8B6F47;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            color: #5D4E37;
            font-size: 2rem;
            font-weight: 700;
        }

        .stat-sublabel {
            color: #D4A574;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .orders-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table thead {
            background: #8B6F47;
            color: white;
        }

        .orders-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .orders-table tbody tr {
            border-bottom: 1px solid #F5EFE7;
            transition: background 0.2s;
        }

        .orders-table tbody tr:hover {
            background: #FDFBF7;
        }

        .orders-table td {
            padding: 1rem;
            color: #5D4E37;
        }

        .order-id {
            font-weight: 700;
            color: #8B6F47;
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .status-pending {
            background: #FFF3CD;
            color: #856404;
        }

        .status-confirmed {
            background: #D1ECF1;
            color: #0C5460;
        }

        .status-shipped {
            background: #FFE5CC;
            color: #CC6600;
        }

        .status-delivered {
            background: #D4EDDA;
            color: #155724;
        }

        .status-cancelled {
            background: #F8D7DA;
            color: #721C24;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            text-transform: uppercase;
        }

        .btn-view {
            background: #6C9BD2;
            color: white;
        }

        .btn-view:hover {
            background: #5A86BD;
            transform: translateY(-1px);
        }

        .btn-edit {
            background: #D4A574;
            color: #5D4E37;
        }

        .btn-edit:hover {
            background: #C49564;
            transform: translateY(-1px);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #8B6F47;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .admin-header h1 {
                font-size: 2rem;
            }

            .admin-tabs {
                flex-direction: column;
            }

            .admin-tab {
                justify-content: center;
            }

            .orders-section {
                overflow-x: auto;
            }

            .orders-table {
                min-width: 800px;
            }
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

    <div class="admin-container">
        <!-- Centered Header -->
        <div class="admin-header">
            <h1>Админ Панел</h1>
            <p class="admin-subtitle">Управление на магазина</p>
            
            <!-- Centered Tab Navigation -->
            <div class="admin-tabs">
                <a href="admin_orders.php" class="admin-tab active">
                    <span class="admin-tab-icon">📦</span>
                    Поръчки
                </a>
                <a href="admin_furniture.php" class="admin-tab">
                    <span class="admin-tab-icon">🛋️</span>
                    Мебели
                </a>
                <a href="admin_custom_requests.php" class="admin-tab">
                    <span class="admin-tab-icon">✉️</span>
                    Запитвания
                </a>
            </div>
        </div>

        <!-- Content Header -->
        <div class="content-header">
            <h2>Управление на поръчки</h2>
        </div>

        <!-- Statistics -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-label">Общо поръчки</div>
                <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
                <div class="stat-sublabel">Всички</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Изчакващи</div>
                <div class="stat-value"><?php echo $stats['pending_orders']; ?></div>
                <div class="stat-sublabel">Нови поръчки</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Потвърдени</div>
                <div class="stat-value"><?php echo $stats['confirmed_orders']; ?></div>
                <div class="stat-sublabel">В обработка</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Изпратени</div>
                <div class="stat-value"><?php echo $stats['shipped_orders']; ?></div>
                <div class="stat-sublabel">В доставка</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Доставени</div>
                <div class="stat-value"><?php echo $stats['delivered_orders']; ?></div>
                <div class="stat-sublabel">Завършени</div>
            </div>
            
            <div class="stat-card" style="border-left-color: #28a745;">
                <div class="stat-label">Общи приходи</div>
                <div class="stat-value" style="color: #28a745;">€<?php echo number_format($stats['total_revenue'], 2); ?></div>
                <div class="stat-sublabel">Без отказани</div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="orders-section">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Клиент</th>
                            <th>Дата</th>
                            <th>Сума</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = mysqli_fetch_assoc($result)): ?>
                            <?php
                            // Fix empty status
                            $order_status = $order['status'];
                            if (empty($order_status) || $order_status === null || $order_status === '') {
                                $order_status = 'pending';
                            }
                            
                            $status_class = 'status-' . $order_status;
                            $status_label = $status_text[$order_status] ?? 'Обработва се';
                            ?>
                            <tr>
                                <td class="order-id">#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['username'] ?? 'N/A'); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                                <td style="font-weight: 600;">
                                    €<?php echo number_format($order['total_price'], 2); ?>
                                    <?php if ($order_status === 'cancelled'): ?>
                                        <span style="font-size: 0.8rem; color: #dc3545;">(отказана)</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo $status_label; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="view_order.php?id=<?php echo $order['id']; ?>" class="btn-action btn-view">
                                            Виж
                                        </a>
                                        <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="btn-action btn-edit">
                                            Редактирай
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <h3>Няма поръчки</h3>
                    <p>Все още няма направени поръчки в системата.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
