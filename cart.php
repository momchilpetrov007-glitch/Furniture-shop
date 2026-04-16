<?php
require_once 'config.php';

// Проверка дали потребителят е логнат
if (!isLoggedIn() || isAdmin()) {
    redirect('index.php');
}

// Обработка на премахване от количката
if (isset($_GET['remove'])) {
    $furniture_id = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$furniture_id])) {
        unset($_SESSION['cart'][$furniture_id]);
    }
    redirect('cart.php');
}

// Обработка на актуализиране на количеството
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $furniture_id => $quantity) {
        $furniture_id = (int)$furniture_id;
        $quantity = (int)$quantity;

        if ($quantity > 0) {
            // Проверка на наличността
            $query = "SELECT stock FROM furniture WHERE id = $furniture_id";
            $result = mysqli_query($conn, $query);
            $furniture = mysqli_fetch_assoc($result);

            if ($furniture && $quantity <= $furniture['stock']) {
                $_SESSION['cart'][$furniture_id] = $quantity;
            } else {
                $_SESSION['cart'][$furniture_id] = $furniture['stock'];
            }
        } else {
            unset($_SESSION['cart'][$furniture_id]);
        }
    }
    redirect('cart.php');
}

// Вземане на артикулите от количката
$cart_items = array();
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $query = "SELECT * FROM furniture WHERE id IN ($ids)";
    $result = mysqli_query($conn, $query);

    while ($furniture = mysqli_fetch_assoc($result)) {
        $quantity = $_SESSION['cart'][$furniture['id']];
        $subtotal = $furniture['price'] * $quantity;
        $total += $subtotal;

        $cart_items[] = array(
            'furniture' => $furniture,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        );
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Количка - Магазин за Мебели</title>
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

    <!-- Количка -->
    <section class="products">
        <div class="container">
            <h2>Моята количка</h2>

            <?php if (empty($cart_items)): ?>
                <div class="alert alert-error">
                    Количката е празна. <a href="index.php">Разгледайте нашите продукти</a>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Продукт</th>
                                    <th>Цена</th>
                                    <th>Количество</th>
                                    <th>Междинна сума</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['furniture']['name']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($item['furniture']['category']); ?></small>
                                        </td>
                                        <td><?php echo number_format($item['furniture']['price'], 2); ?> €</td>
                                        <td>
                                            <input type="number" 
                                                   name="quantities[<?php echo $item['furniture']['id']; ?>]" 
                                                   value="<?php echo $item['quantity']; ?>" 
                                                   min="1" 
                                                   max="<?php echo $item['furniture']['stock']; ?>"
                                                   style="width: 80px;">
                                        </td>
                                        <td><strong><?php echo number_format($item['subtotal'], 2); ?> €</strong></td>
                                        <td>
                                            <a href="cart.php?remove=<?php echo $item['furniture']['id']; ?>" 
                                               class="btn btn-danger"
                                               onclick="return confirm('Сигурни ли сте, че искате да премахнете този артикул?');">
                                                Премахни
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                        <button type="submit" name="update_cart" class="btn btn-primary">Актуализирай количката</button>
                        <a href="index.php" class="btn btn-success">Продължи пазаруването</a>
                    </div>
                </form>

                <div class="cart-total">
                    <h3>Обща сума: <?php echo number_format($total, 2); ?> €</h3>
                    <a href="checkout.php" class="btn btn-success" style="display: inline-block; margin-top: 1rem;">
                        Завърши поръчката
                    </a>
                </div>
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
