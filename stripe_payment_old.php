<?php
require_once 'config.php';

// Проверка дали потребителят е логнат
if (!isLoggedIn() || isAdmin()) {
    redirect('index.php');
}

// Проверка дали има pending order
if (!isset($_SESSION['pending_order_id']) || !isset($_SESSION['pending_order_total'])) {
    redirect('cart.php');
}

$order_id = $_SESSION['pending_order_id'];
$total = $_SESSION['pending_order_total'];

// ВАЖНО: Тук трябва да добавите вашия Stripe Secret Key
// За тестване използвайте test key от: https://dashboard.stripe.com/test/apikeys
$stripe_secret_key = 'sk_test_your_secret_key_here'; // ЗАМЕНЕТЕ С ВАШИЯ KEY!
$stripe_publishable_key = 'pk_test_your_publishable_key_here'; // ЗАМЕНЕТЕ С ВАШИЯ KEY!

// Обработка на успешно плащане
if (isset($_GET['success']) && $_GET['success'] == '1') {
    // Актуализиране на статуса на поръчката
    $update_query = "UPDATE orders SET notes = CONCAT(notes, '\nПлатено с карта: Успешно') WHERE id = $order_id";
    mysqli_query($conn, $update_query);
    
    // Изчистване на session данни
    unset($_SESSION['pending_order_id']);
    unset($_SESSION['pending_order_total']);
    
    $success_message = true;
}

// Обработка на отказано плащане
if (isset($_GET['cancel']) && $_GET['cancel'] == '1') {
    $cancel_message = true;
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Плащане с карта - Магазин за Мебели</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://js.stripe.com/v3/"></script>
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
                    <li><a href="logout.php">ИЗХОД</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="payment-container">
        <div class="container">
            <?php if (isset($success_message)): ?>
                <div class="payment-success">
                    <div class="success-icon">✓</div>
                    <h2>Плащането е успешно!</h2>
                    <p>Вашата поръчка #<?php echo $order_id; ?> е потвърдена.</p>
                    <p>Ще получите имейл с детайли за доставката.</p>
                    <div class="success-actions">
                        <a href="my_orders.php" class="btn btn-primary">Виж моите поръчки</a>
                        <a href="catalog.php" class="btn btn-secondary">Продължи пазаруването</a>
                    </div>
                </div>
            <?php elseif (isset($cancel_message)): ?>
                <div class="payment-cancel">
                    <div class="cancel-icon">✗</div>
                    <h2>Плащането е отказано</h2>
                    <p>Вашата поръчка не е завършена.</p>
                    <div class="cancel-actions">
                        <a href="cart.php" class="btn btn-primary">Обратно към количката</a>
                        <a href="catalog.php" class="btn btn-secondary">Продължи пазаруването</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="payment-form">
                    <h2>Плащане с карта</h2>
                    <p class="payment-subtitle">Сигурно плащане чрез Stripe</p>

                    <div class="order-total-display">
                        <h3>Обща сума за плащане:</h3>
                        <div class="total-amount"><?php echo number_format($total, 2); ?> лв.</div>
                    </div>

                    <div class="stripe-info">
                        <h4>💡 Информация за плащането:</h4>
                        <ul>
                            <li>Плащането е напълно сигурно</li>
                            <li>Данните за картата не се съхраняват на нашия сървър</li>
                            <li>Използваме Stripe - водещ доставчик на платежни услуги</li>
                        </ul>
                    </div>

                    <!-- ДЕМО РЕЖИМ - За реална интеграция се изисква Stripe API -->
                    <div class="stripe-demo">
                        <div class="alert alert-warning">
                            <strong>⚠️ ДЕМО РЕЖИМ</strong>
                            <p>За реална Stripe интеграция, моля:</p>
                            <ol style="text-align: left; margin-top: 1rem;">
                                <li>Регистрирайте се на <a href="https://stripe.com" target="_blank">stripe.com</a></li>
                                <li>Вземете вашите API ключове от Dashboard</li>
                                <li>Добавете ги в <code>stripe_payment.php</code> (редове 14-15)</li>
                                <li>Инсталирайте Stripe PHP библиотеката: <code>composer require stripe/stripe-php</code></li>
                            </ol>
                        </div>

                        <!-- Симулиране на Stripe форма -->
                        <div class="stripe-form-placeholder">
                            <h4>Данни за картата:</h4>
                            <form id="payment-form">
                                <div class="form-group">
                                    <label>Номер на карта</label>
                                    <input type="text" placeholder="4242 4242 4242 4242" disabled>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Валидност</label>
                                        <input type="text" placeholder="MM / YY" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>CVC</label>
                                        <input type="text" placeholder="123" disabled>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="demo-actions">
                            <p><strong>В демо режим можете да:</strong></p>
                            <a href="?success=1" class="btn btn-success">✓ Симулирай успешно плащане</a>
                            <a href="?cancel=1" class="btn btn-danger">✗ Симулирай отказано плащане</a>
                        </div>
                    </div>

                    <div class="payment-footer">
                        <a href="checkout.php" class="btn btn-secondary">← Обратно към поръчката</a>
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
</body>
</html>
