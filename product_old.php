<?php
require_once 'config.php';

// Проверка дали е подаден ID на продукт
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('catalog.php');
}

$product_id = (int)$_GET['id'];

// Вземане на продукта
$query = "SELECT * FROM furniture WHERE id = $product_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    redirect('catalog.php');
}

$product = mysqli_fetch_assoc($result);

// Вземане на подобни продукти от същата категория
$related_query = "SELECT * FROM furniture WHERE category = '{$product['category']}' AND id != $product_id AND stock > 0 ORDER BY RAND() LIMIT 4";
$related_result = mysqli_query($conn, $related_query);
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Магазин за Мебели</title>
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
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <li><a href="admin_panel.php">АДМИН ПАНЕЛ</a></li>
                        <?php else: ?>
                            <li><a href="my_orders.php">МОИТЕ ПОРЪЧКИ</a></li>
                        <?php endif; ?>
                        <li><a href="cart.php">КОЛИЧКА</a></li>
                        <li><a href="logout.php">ИЗХОД (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                    <?php else: ?>
                        <li><a href="login.php">ВХОД</a></li>
                        <li><a href="register.php">РЕГИСТРАЦИЯ</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <a href="index.php">Начало</a>
            <span class="separator">/</span>
            <a href="catalog.php">Каталог</a>
            <span class="separator">/</span>
            <a href="catalog.php?category=<?php echo urlencode($product['category']); ?>"><?php echo htmlspecialchars($product['category']); ?></a>
            <span class="separator">/</span>
            <span class="current"><?php echo htmlspecialchars($product['name']); ?></span>
        </div>
    </div>

    <!-- Детайли на продукта -->
    <section class="product-details">
        <div class="container">
            <div class="product-details-grid">
                <!-- Снимка -->
                <div class="product-details-image">
                    <img src="images/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         onerror="this.src='images/placeholder.jpg'">
                </div>

                <!-- Информация -->
                <div class="product-details-info">
                    <p class="product-details-category"><?php echo htmlspecialchars($product['category']); ?></p>
                    <h1 class="product-details-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                    
                    <div class="product-details-price">
                        <span class="price-amount"><?php echo number_format($product['price'], 2); ?> лв.</span>
                    </div>

                    <div class="product-details-stock">
                        <?php if ($product['stock'] > 0): ?>
                            <span class="in-stock">✓ В наличност (<?php echo $product['stock']; ?> бр.)</span>
                        <?php else: ?>
                            <span class="out-of-stock">✗ Изчерпан</span>
                        <?php endif; ?>
                    </div>

                    <div class="product-details-description">
                        <h3>Описание</h3>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>

                    <div class="product-details-features">
                        <h3>Характеристики</h3>
                        <ul>
                            <li><strong>Категория:</strong> <?php echo htmlspecialchars($product['category']); ?></li>
                            <li><strong>Наличност:</strong> <?php echo $product['stock']; ?> бр.</li>
                            <li><strong>Код на продукт:</strong> #FRN<?php echo str_pad($product['id'], 4, '0', STR_PAD_LEFT); ?></li>
                        </ul>
                    </div>

                    <?php if (isLoggedIn() && !isAdmin()): ?>
                        <?php if ($product['stock'] > 0): ?>
                            <form method="POST" action="add_to_cart.php" class="product-details-form">
                                <input type="hidden" name="furniture_id" value="<?php echo $product['id']; ?>">
                                <div class="quantity-selector">
                                    <label for="quantity">Количество:</label>
                                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-large">ДОБАВИ В КОЛИЧКАТА</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-disabled btn-large" disabled>ИЗЧЕРПАН</button>
                        <?php endif; ?>
                    <?php elseif (!isLoggedIn()): ?>
                        <a href="login.php" class="btn btn-primary btn-large">ВЛЕЗТЕ ЗА ПОРЪЧКА</a>
                    <?php endif; ?>

                    <div class="product-details-benefits">
                        <div class="benefit-item">
                            <span class="benefit-icon">✓</span>
                            <span>Безплатна доставка над 500 лв.</span>
                        </div>
                        <div class="benefit-item">
                            <span class="benefit-icon">✓</span>
                            <span>5 години гаранция</span>
                        </div>
                        <div class="benefit-item">
                            <span class="benefit-icon">✓</span>
                            <span>Професионален монтаж</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Подобни продукти -->
    <?php if (mysqli_num_rows($related_result) > 0): ?>
    <section class="related-products">
        <div class="container">
            <h2>Подобни продукти</h2>
            <div class="related-products-grid">
                <?php while ($related = mysqli_fetch_assoc($related_result)): ?>
                    <a href="product.php?id=<?php echo $related['id']; ?>" class="related-product-card">
                        <div class="related-product-image">
                            <img src="images/<?php echo htmlspecialchars($related['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($related['name']); ?>"
                                 onerror="this.src='images/placeholder.jpg'">
                        </div>
                        <div class="related-product-info">
                            <p class="related-product-category"><?php echo htmlspecialchars($related['category']); ?></p>
                            <h3><?php echo htmlspecialchars($related['name']); ?></h3>
                            <p class="related-product-price"><?php echo number_format($related['price'], 2); ?> лв.</p>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Футър -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>МЕБЕЛИ ОНЛАЙН</h4>
                    <p>Вашият партньор за елегантен дом</p>
                </div>
                <div class="footer-section">
                    <h4>ВРЪЗКИ</h4>
                    <ul>
                        <li><a href="catalog.php">Каталог</a></li>
                        <li><a href="login.php">Вход</a></li>
                        <li><a href="register.php">Регистрация</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>КОНТАКТИ</h4>
                    <p>Email: info@mebeli.bg</p>
                    <p>Тел: +359 888 123 456</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Магазин за Мебели. Всички права запазени.</p>
            </div>
        </div>
    </footer>
</body>
</html>
