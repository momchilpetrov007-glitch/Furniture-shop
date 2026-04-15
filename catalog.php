<?php
require_once 'config.php';

// Филтриране по категория ако има
$category_filter = '';
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = escape($conn, $_GET['category']);
    $category_filter = "WHERE category = '$category'";
}

// Вземане на всички мебели
$query = "SELECT * FROM furniture $category_filter ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог - Магазин за Мебели</title>
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

    <!-- Заглавна секция на каталога -->
    <section class="catalog-hero">
        <div class="container">
            <h1>Нашият каталог</h1>
            <p>Открийте перфектните мебели за вашия дом</p>
        </div>
    </section>

    <!-- Каталог с мебели -->
    <section class="products">
        <div class="container">
            <?php if (isset($_GET['category'])): ?>
                <div class="catalog-filter">
                    <p>Филтър: <strong><?php echo htmlspecialchars($_GET['category']); ?></strong></p>
                    <a href="catalog.php" class="btn-clear-filter">Изчисти филтъра</a>
                </div>
            <?php endif; ?>

            <div class="products-grid">
                <?php while ($furniture = mysqli_fetch_assoc($result)): ?>
                    <div class="product-card">
                        <a href="product.php?id=<?php echo $furniture['id']; ?>" class="product-link">
                            <div class="product-image">
                                <img src="images/<?php echo htmlspecialchars($furniture['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($furniture['name']); ?>"
                                     onerror="this.src='images/placeholder.jpg'">
                            </div>
                        </a>
                        <div class="product-info">
                            <p class="category"><?php echo htmlspecialchars($furniture['category']); ?></p>
                            <h3><a href="product.php?id=<?php echo $furniture['id']; ?>" class="product-title-link"><?php echo htmlspecialchars($furniture['name']); ?></a></h3>
                            <p class="description"><?php echo htmlspecialchars($furniture['description']); ?></p>
                            <div class="product-footer">
                                <span class="price"><?php echo number_format($furniture['price'], 2); ?> €</span>
                                <span class="stock">В наличност: <?php echo $furniture['stock']; ?></span>
                            </div>
                            <div class="product-actions">
                                <a href="product.php?id=<?php echo $furniture['id']; ?>" class="btn btn-secondary">ВИЖ ПОВЕЧЕ</a>
                                <?php if (isLoggedIn() && !isAdmin()): ?>
                                    <?php if ($furniture['stock'] > 0): ?>
                                        <form method="POST" action="add_to_cart.php" class="quick-add-form">
                                            <input type="hidden" name="furniture_id" value="<?php echo $furniture['id']; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-primary">ДОБАВИ</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-disabled" disabled>ИЗЧЕРПАН</button>
                                    <?php endif; ?>
                                <?php elseif (!isLoggedIn()): ?>
                                    <a href="login.php" class="btn btn-primary">ВХОД</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php if (mysqli_num_rows($result) == 0): ?>
                <div class="empty-catalog">
                    <p>Няма намерени продукти в тази категория.</p>
                    <a href="catalog.php" class="btn btn-primary">Вижте всички продукти</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

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
