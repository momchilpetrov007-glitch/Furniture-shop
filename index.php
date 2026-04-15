<?php
require_once 'config.php';

// Ако потребителят е админ, пренасочи го към admin панела
if (isLoggedIn() && isAdmin()) {
    redirect('admin_orders.php');
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин за Мебели - Елегантни мебели за вашия дом</title>
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
                    <li><a href="custom_order.php" class="custom-order-btn">МЕБЕЛИ ПО ПОРЪЧКА</a></li>
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <li><a href="admin_orders.php">АДМИН ПАНЕЛ</a></li>
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

    <!-- Hero Секция -->
    <section class="hero-main">
        <div class="hero-content">
            <div class="hero-text">
                <p class="hero-subtitle">нова колекция</p>
                <h1 class="hero-title">Открийте перфектните<br>мебели за вашия дом</h1>
                <p class="hero-description">Елегантен дизайн, изключително качество и непревзходен комфорт.<br>Създайте пространството, което заслужавате.</p>
                <div class="hero-buttons">
                    <a href="catalog.php" class="btn btn-primary-hero">РАЗГЛЕДАЙТЕ КАТАЛОГА</a>
                    <a href="#featured" class="btn btn-secondary-hero">ПРЕПОРЪЧАНИ ПРОДУКТИ</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Промо Банер -->
    <section class="promo-banner">
        <div class="container">
            <div class="promo-grid">
                <div class="promo-item">
                    <div class="promo-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <h3>Безплатна доставка</h3>
                    <p>При поръчка над 500 €</p>
                </div>
                <div class="promo-item">
                    <div class="promo-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </div>
                    <h3>Гаранция за качество</h3>
                    <p>5 години гаранция на всички продукти</p>
                </div>
                <div class="promo-item">
                    <div class="promo-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                    </div>
                    <h3>Удобно плащане</h3>
                    <p>Плащане на части без лихва</p>
                </div>
                <div class="promo-item">
                    <div class="promo-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h3>Професионален монтаж</h3>
                    <p>Безплатен монтаж на място</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Препоръчани продукти -->
    <section class="featured-section" id="featured">
        <div class="container">
            <div class="section-header">
                <h2>Препоръчани продукти</h2>
                <p>Най-търсените мебели от нашата колекция</p>
            </div>

            <?php
            // Вземане на 6 случайни мебели
            $query = "SELECT * FROM furniture WHERE stock > 0 ORDER BY RAND() LIMIT 6";
            $result = mysqli_query($conn, $query);
            ?>

            <div class="featured-grid">
                <?php while ($furniture = mysqli_fetch_assoc($result)): ?>
                    <div class="featured-card">
                        <a href="product.php?id=<?php echo $furniture['id']; ?>">
                            <div class="featured-image">
                                <img src="images/<?php echo htmlspecialchars($furniture['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($furniture['name']); ?>"
                                     onerror="this.src='images/placeholder.jpg'">
                                <div class="featured-overlay">
                                    <span class="btn btn-overlay">ВИЖ ПОВЕЧЕ</span>
                                </div>
                            </div>
                        </a>
                        <div class="featured-info">
                            <p class="featured-category"><?php echo htmlspecialchars($furniture['category']); ?></p>
                            <h3><a href="product.php?id=<?php echo $furniture['id']; ?>"><?php echo htmlspecialchars($furniture['name']); ?></a></h3>
                            <p class="featured-price"><?php echo number_format($furniture['price'], 2); ?> €</p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="section-footer">
                <a href="catalog.php" class="btn btn-primary">ВИЖТЕ ЦЕЛИЯ КАТАЛОГ</a>
            </div>
        </div>
    </section>

    <!-- Категории Секция -->
    <section class="categories-section">
        <div class="container">
            <div class="section-header">
                <h2>Пазарувайте по категории</h2>
                <p>Намерете точно това, което търсите</p>
            </div>

            <div class="categories-grid">
                <a href="catalog.php?category=Дивани" class="category-card">
                    <div class="category-image" style="background-color: #f5f1e8;">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <path d="M19 10V6a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v4"></path>
                            <path d="M3 18v-8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8"></path>
                            <path d="M3 14h18"></path>
                            <path d="M5 18h14"></path>
                        </svg>
                    </div>
                    <h3>Дивани</h3>
                </a>

                <a href="catalog.php?category=Маси" class="category-card">
                    <div class="category-image" style="background-color: #e8f1f5;">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <line x1="4" y1="9" x2="20" y2="9"></line>
                            <line x1="4" y1="15" x2="20" y2="15"></line>
                            <line x1="10" y1="3" x2="8" y2="21"></line>
                            <line x1="16" y1="3" x2="14" y2="21"></line>
                        </svg>
                    </div>
                    <h3>Маси</h3>
                </a>

                <a href="catalog.php?category=Спални" class="category-card">
                    <div class="category-image" style="background-color: #f5e8f1;">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <path d="M4 9v10"></path>
                            <path d="M20 9v10"></path>
                            <path d="M4 11h16"></path>
                            <path d="M4 15h16"></path>
                        </svg>
                    </div>
                    <h3>Спални</h3>
                </a>

                <a href="catalog.php?category=Гардероби" class="category-card">
                    <div class="category-image" style="background-color: #e8f5f1;">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="12" y1="3" x2="12" y2="21"></line>
                            <circle cx="9" cy="12" r="1"></circle>
                            <circle cx="15" cy="12" r="1"></circle>
                        </svg>
                    </div>
                    <h3>Гардероби</h3>
                </a>

                <a href="catalog.php?category=Кухни" class="category-card">
                    <div class="category-image" style="background-color: #f1e8f5;">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="3" y1="9" x2="21" y2="9"></line>
                            <line x1="9" y1="21" x2="9" y2="9"></line>
                        </svg>
                    </div>
                    <h3>Кухни</h3>
                </a>

                <a href="catalog.php" class="category-card">
                    <div class="category-image" style="background-color: #f5f5e8;">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                    </div>
                    <h3>Всички категории</h3>
                </a>
            </div>
        </div>
    </section>

    <!-- За нас секция -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>Създаваме пространства<br>за живот</h2>
                    <p>С над 10 години опит в индустрията, ние предлагаме внимателно подбрани мебели, които съчетават стил, комфорт и функционалност.</p>
                    <p>Нашата мисия е да направим всеки дом специален с качествени продукти и изключително обслужване.</p>
                    <a href="catalog.php" class="btn btn-primary">ОТКРИЙТЕ ПОВЕЧЕ</a>
                </div>
                <div class="about-image">
                    <div class="about-placeholder">
                        <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.5">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                </div>
            </div>
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
