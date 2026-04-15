<?php
require_once 'config.php';
require_once 'email_functions.php';

// Проверка дали потребителят е логнат
if (!isLoggedIn() || isAdmin()) {
    redirect('index.php');
}

// Проверка дали има артикули в количката
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    redirect('cart.php');
}

$error = '';
$success = '';

// Вземане на данни на потребителя
$user_query = "SELECT * FROM users WHERE id = " . $_SESSION['user_id'];
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = escape($conn, $_POST['first_name']);
    $last_name = escape($conn, $_POST['last_name']);
    $email = escape($conn, $_POST['email']);
    $phone = escape($conn, $_POST['phone']);
    $delivery_address = escape($conn, $_POST['delivery_address']);
    $city = escape($conn, $_POST['city']);
    $postal_code = escape($conn, $_POST['postal_code']);
    $payment_method = escape($conn, $_POST['payment_method']);
    $notes = escape($conn, $_POST['notes']);

    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($delivery_address) || empty($city) || empty($payment_method)) {
        $error = 'Моля, попълнете всички задължителни полета!';
    } else {
        // Изчисляване на общата сума
        $ids = implode(',', array_keys($_SESSION['cart']));
        $query = "SELECT * FROM furniture WHERE id IN ($ids)";
        $result = mysqli_query($conn, $query);

        $total = 0;
        $order_items = array();

        while ($furniture = mysqli_fetch_assoc($result)) {
            $quantity = $_SESSION['cart'][$furniture['id']];
            $subtotal = $furniture['price'] * $quantity;
            $total += $subtotal;

            $order_items[] = array(
                'furniture_id' => $furniture['id'],
                'quantity' => $quantity,
                'price' => $furniture['price'],
                'stock' => $furniture['stock']
            );
        }

        // Проверка на наличността
        $stock_error = false;
        foreach ($order_items as $item) {
            if ($item['quantity'] > $item['stock']) {
                $stock_error = true;
                $error = 'Недостатъчна наличност за някои продукти!';
                break;
            }
        }

        if (!$stock_error) {
            // Начало на транзакция
            mysqli_begin_transaction($conn);

            try {
                // Форматиране на пълен адрес
                $full_address = "$delivery_address, $city";
                if (!empty($postal_code)) {
                    $full_address .= ", $postal_code";
                }

                // Добавяне на имена и имейл в бележките
                $order_notes = "Име: $first_name $last_name\nИмейл: $email\nНачин на плащане: $payment_method";
                if (!empty($notes)) {
                    $order_notes .= "\nБележки: $notes";
                }

                // Вмъкване на поръчката
                $insert_order = "INSERT INTO orders (user_id, total_price, status, delivery_address, phone, notes) 
                                VALUES ({$_SESSION['user_id']}, $total, 'pending', '$full_address', '$phone', '$order_notes')";
                mysqli_query($conn, $insert_order);
                $order_id = mysqli_insert_id($conn);

                // Вмъкване на артикулите и намаляване на stock
                foreach ($order_items as $item) {
                    $insert_item = "INSERT INTO order_items (order_id, furniture_id, quantity, price) 
                                   VALUES ($order_id, {$item['furniture_id']}, {$item['quantity']}, {$item['price']})";
                    mysqli_query($conn, $insert_item);

                    // Намаляване на наличността
                    $new_stock = $item['stock'] - $item['quantity'];
                    $update_stock = "UPDATE furniture SET stock = $new_stock WHERE id = {$item['furniture_id']}";
                    mysqli_query($conn, $update_stock);
                }

                // Commit на транзакцията
                mysqli_commit($conn);

                // Подготовка на данни за имейли
                $user_full_name = "$first_name $last_name";

                // Изчистване на количката
                unset($_SESSION['cart']);

                // Пренасочване според метода на плащане
                if ($payment_method === 'card') {
                    // Пренасочване към Stripe плащане
                    $_SESSION['pending_order_id'] = $order_id;
                    $_SESSION['pending_order_total'] = $total;
                    $_SESSION['pending_order_email'] = $email;
                    $_SESSION['pending_order_name'] = $user_full_name;
                    redirect('stripe_payment.php');
                } else {
                    // Наложен платеж - изпрати имейли
                    sendOrderConfirmationEmail($conn, $order_id, $email, $user_full_name);
                    sendAdminNotification($conn, $order_id);
                    
                    $success = 'Поръчката е приета успешно! Изпратихме потвърждение на вашия имейл.';
                }

            } catch (Exception $e) {
                // Rollback при грешка
                mysqli_rollback($conn);
                $error = 'Грешка при обработка на поръчката: ' . $e->getMessage();
            }
        }
    }
}

// Проверка дали има артикули в количката преди изчисляване
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    redirect('cart.php');
}

// Изчисляване на общата сума за показване
$ids = implode(',', array_keys($_SESSION['cart']));
$query = "SELECT * FROM furniture WHERE id IN ($ids)";
$result = mysqli_query($conn, $query);
$total = 0;
while ($furniture = mysqli_fetch_assoc($result)) {
    $total += $furniture['price'] * $_SESSION['cart'][$furniture['id']];
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Завършване на поръчка - Магазин за Мебели</title>
    <link rel="stylesheet" href="style.css">
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
                    <li><a href="my_orders.php">МОИТЕ ПОРЪЧКИ</a></li>
                    <li><a href="cart.php">КОЛИЧКА</a></li>
                    <li><a href="logout.php">ИЗХОД (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Форма за поръчка -->
    <div class="checkout-container">
        <div class="container">
            <h2>Завършване на поръчка</h2>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <p style="margin-top: 1rem;">
                        <a href="my_orders.php" class="btn btn-primary">Виж моите поръчки</a>
                        <a href="catalog.php" class="btn btn-secondary">Продължи пазаруването</a>
                    </p>
                </div>
            <?php else: ?>
                <div class="checkout-grid">
                    <!-- Форма -->
                    <div class="checkout-form">
                        <form method="POST" action="" id="checkoutForm">
                            <h3>Лични данни</h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">Име *</label>
                                    <input type="text" id="first_name" name="first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Фамилия *</label>
                                    <input type="text" id="last_name" name="last_name" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">Имейл *</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Телефон за контакт *</label>
                                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                </div>
                            </div>

                            <h3 style="margin-top: 2rem;">Адрес за доставка</h3>

                            <div class="form-group">
                                <label for="delivery_address">Адрес *</label>
                                <textarea id="delivery_address" name="delivery_address" rows="2" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">Град *</label>
                                    <input type="text" id="city" name="city" required>
                                </div>
                                <div class="form-group">
                                    <label for="postal_code">Пощенски код</label>
                                    <input type="text" id="postal_code" name="postal_code">
                                </div>
                            </div>

                            <h3 style="margin-top: 2rem;">Начин на плащане</h3>

                            <div class="payment-methods">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="cash" checked>
                                    <div class="payment-card">
                                        <div class="payment-icon">💵</div>
                                        <div class="payment-info">
                                            <strong>Наложен платеж</strong>
                                            <p>Плащате при доставка</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="card">
                                    <div class="payment-card">
                                        <div class="payment-icon">💳</div>
                                        <div class="payment-info">
                                            <strong>Плащане с карта</strong>
                                            <p>Сигурно плащане с Stripe</p>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="form-group" style="margin-top: 2rem;">
                                <label for="notes">Допълнителни бележки</label>
                                <textarea id="notes" name="notes" rows="3" placeholder="Напр. желано време за доставка, етаж, и др."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-large" style="margin-top: 2rem;">
                                ЗАВЪРШИ ПОРЪЧКАТА
                            </button>
                        </form>
                    </div>

                    <!-- Обобщение -->
                    <div class="order-summary">
                        <h3>Обобщение на поръчката</h3>
                        
                        <div class="summary-items">
                            <?php
                            mysqli_data_seek($result, 0);
                            while ($furniture = mysqli_fetch_assoc($result)):
                                $quantity = $_SESSION['cart'][$furniture['id']];
                                $subtotal = $furniture['price'] * $quantity;
                            ?>
                                <div class="summary-item">
                                    <div class="summary-item-info">
                                        <strong><?php echo htmlspecialchars($furniture['name']); ?></strong>
                                        <span>Количество: <?php echo $quantity; ?></span>
                                    </div>
                                    <div class="summary-item-price">
                                        <?php echo number_format($subtotal, 2); ?> €
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <div class="summary-total">
                            <div class="summary-row">
                                <span>Междинна сума:</span>
                                <strong><?php echo number_format($total, 2); ?> €</strong>
                            </div>
                            <div class="summary-row">
                                <span>Доставка:</span>
                                <strong><?php echo $total >= 500 ? 'Безплатна' : '15.00 €'; ?></strong>
                            </div>
                            <div class="summary-row summary-final">
                                <span>Обща сума:</span>
                                <strong><?php echo number_format($total >= 500 ? $total : $total + 15, 2); ?> €</strong>
                            </div>
                        </div>

                        <?php if ($total < 500): ?>
                            <p class="delivery-note">
                                <small>💡 Добавете още <?php echo number_format(500 - $total, 2); ?> € за безплатна доставка!</small>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Футър -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Магазин за Мебели. Всички права запазени.</p>
        </div>
    </footer>

    <script>
    // Highlight selected payment method
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.payment-card').forEach(card => {
                card.classList.remove('selected');
            });
            this.closest('.payment-option').querySelector('.payment-card').classList.add('selected');
        });
    });
    
    // Initial selection
    document.querySelector('input[name="payment_method"]:checked').closest('.payment-option').querySelector('.payment-card').classList.add('selected');
    </script>
</body>
</html>
