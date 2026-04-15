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

// Fetch current order
$query = "SELECT * FROM orders WHERE id = $order_id";
$result = mysqli_query($conn, $query);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    redirect('admin_orders.php');
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the new status
    $new_status = isset($_POST['status']) ? trim($_POST['status']) : '';
    
    // Validate status
    $allowed_statuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
    
    if (in_array($new_status, $allowed_statuses)) {
        // Update the order status
        $update_query = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Статусът на поръчката е променен успешно!";
            
            // Send email notification to customer (if email functions exist)
            if (function_exists('sendOrderStatusUpdateEmail')) {
                // Fetch user email
                $user_query = "SELECT u.email, u.username FROM users u 
                              INNER JOIN orders o ON u.id = o.user_id 
                              WHERE o.id = $order_id";
                $user_result = mysqli_query($conn, $user_query);
                $user_data = mysqli_fetch_assoc($user_result);
                
                if ($user_data) {
                    sendOrderStatusUpdateEmail(
                        $user_data['email'], 
                        $user_data['username'], 
                        $order_id, 
                        $new_status
                    );
                }
            }
            
            // Refresh order data
            $result = mysqli_query($conn, $query);
            $order = mysqli_fetch_assoc($result);
        } else {
            $error_message = "Грешка при промяна на статуса: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Невалиден статус!";
    }
}

// Status translations
$status_text = [
    'pending' => 'Изчакваща',
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
    <title>Промяна на поръчка #<?php echo $order_id; ?> - Админ панел</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-container {
            max-width: 800px;
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

        .edit-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .edit-card h1 {
            color: #5D4E37;
            margin: 0 0 1.5rem 0;
            padding-bottom: 1rem;
            border-bottom: 2px solid #D4A574;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #8B6F47;
            margin-bottom: 0.5rem;
        }

        .current-info {
            background: #F5EFE7;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .current-info-label {
            font-weight: 600;
            color: #8B6F47;
            font-size: 0.9rem;
        }

        .current-info-value {
            color: #5D4E37;
            font-size: 1.1rem;
            margin-top: 0.25rem;
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

        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .radio-option {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: #FDFBF7;
            border: 2px solid #F5EFE7;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .radio-option:hover {
            background: #F5EFE7;
            border-color: #D4A574;
        }

        .radio-option.selected {
            background: #FFF9E6;
            border-color: #8B6F47;
            box-shadow: 0 2px 8px rgba(139, 111, 71, 0.2);
        }

        .radio-option input[type="radio"] {
            width: 20px;
            height: 20px;
            margin-right: 1rem;
            cursor: pointer;
            accent-color: #8B6F47;
        }

        .radio-label {
            flex: 1;
            cursor: pointer;
        }

        .radio-title {
            font-weight: 600;
            color: #5D4E37;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .radio-description {
            color: #8B6F47;
            font-size: 0.9rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 1rem 2rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
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
            background: #F5EFE7;
            color: #5D4E37;
        }

        .btn-secondary:hover {
            background: #E5DFD7;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: #D4EDDA;
            color: #155724;
            border: 1px solid #C3E6CB;
        }

        .alert-error {
            background: #F8D7DA;
            color: #721C24;
            border: 1px solid #F5C6CB;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
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

    <div class="edit-container">
        <a href="view_order.php?id=<?php echo $order_id; ?>" class="back-button">← Назад към поръчката</a>

        <div class="edit-card">
            <h1>Промяна на статус - Поръчка #<?php echo $order_id; ?></h1>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    ✓ <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    ✗ <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Current Status -->
            <div class="current-info">
                <div class="current-info-label">Текущ статус:</div>
                <div class="current-info-value">
                    <span class="status-badge status-<?php echo htmlspecialchars($order['status']); ?>">
                        <?php echo $status_text[$order['status']] ?? 'Неизвестен'; ?>
                    </span>
                </div>
            </div>

            <!-- Edit Form -->
            <form method="POST" action="" id="statusForm">
                <div class="form-group">
                    <label>Изберете нов статус:</label>
                    
                    <div class="radio-group">
                        <!-- Pending -->
                        <label class="radio-option <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>" 
                               onclick="selectOption(this)">
                            <input type="radio" 
                                   name="status" 
                                   value="pending" 
                                   <?php echo $order['status'] === 'pending' ? 'checked' : ''; ?>>
                            <div class="radio-label">
                                <div class="radio-title">Изчакваща</div>
                                <div class="radio-description">Поръчката е получена и очаква обработка</div>
                            </div>
                        </label>

                        <!-- Confirmed -->
                        <label class="radio-option <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>" 
                               onclick="selectOption(this)">
                            <input type="radio" 
                                   name="status" 
                                   value="confirmed" 
                                   <?php echo $order['status'] === 'confirmed' ? 'checked' : ''; ?>>
                            <div class="radio-label">
                                <div class="radio-title">Потвърдена</div>
                                <div class="radio-description">Поръчката е потвърдена и се подготвя за изпращане</div>
                            </div>
                        </label>

                        <!-- Shipped -->
                        <label class="radio-option <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>" 
                               onclick="selectOption(this)">
                            <input type="radio" 
                                   name="status" 
                                   value="shipped" 
                                   <?php echo $order['status'] === 'shipped' ? 'checked' : ''; ?>>
                            <div class="radio-label">
                                <div class="radio-title">Изпратена</div>
                                <div class="radio-description">Поръчката е предадена на куриер и е в процес на доставка</div>
                            </div>
                        </label>

                        <!-- Delivered -->
                        <label class="radio-option <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>" 
                               onclick="selectOption(this)">
                            <input type="radio" 
                                   name="status" 
                                   value="delivered" 
                                   <?php echo $order['status'] === 'delivered' ? 'checked' : ''; ?>>
                            <div class="radio-label">
                                <div class="radio-title">Доставена</div>
                                <div class="radio-description">Поръчката е успешно доставена на клиента</div>
                            </div>
                        </label>

                        <!-- Cancelled -->
                        <label class="radio-option <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>" 
                               onclick="selectOption(this)">
                            <input type="radio" 
                                   name="status" 
                                   value="cancelled" 
                                   <?php echo $order['status'] === 'cancelled' ? 'checked' : ''; ?>>
                            <div class="radio-label">
                                <div class="radio-title">Отказана</div>
                                <div class="radio-description">Поръчката е отменена</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">
                        Запази промените
                    </button>
                    <a href="view_order.php?id=<?php echo $order_id; ?>" class="btn btn-secondary">
                        Отказ
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function selectOption(element) {
            // Remove 'selected' class from all options
            document.querySelectorAll('.radio-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Add 'selected' class to clicked option
            element.classList.add('selected');
            
            // Check the radio button
            const radio = element.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        }

        // Confirmation before submit
        document.getElementById('statusForm').addEventListener('submit', function(e) {
            const selectedStatus = document.querySelector('input[name="status"]:checked');
            if (!selectedStatus) {
                e.preventDefault();
                alert('Моля изберете статус!');
                return false;
            }
            
            const statusTexts = {
                'pending': 'Изчакваща',
                'confirmed': 'Потвърдена',
                'shipped': 'Изпратена',
                'delivered': 'Доставена',
                'cancelled': 'Отказана'
            };
            
            const statusValue = selectedStatus.value;
            const statusLabel = statusTexts[statusValue] || statusValue;
            
            const confirmMessage = `Сигурни ли сте, че искате да промените статуса на "${statusLabel}"?`;
            
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>
