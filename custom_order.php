<?php
require_once 'config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = escape($conn, $_POST['name']);
    $email = escape($conn, $_POST['email']);
    $phone = escape($conn, $_POST['phone']);
    $furniture_type = escape($conn, $_POST['furniture_type']);
    $dimensions = escape($conn, $_POST['dimensions']);
    $material = escape($conn, $_POST['material']);
    $budget = escape($conn, $_POST['budget']);
    $description = escape($conn, $_POST['description']);
    
    if (empty($name) || empty($email) || empty($phone) || empty($furniture_type) || empty($description)) {
        $error = 'Моля, попълнете всички задължителни полета!';
    } else {
        // Запазване на запитването в базата данни
        $query = "INSERT INTO custom_requests (name, email, phone, furniture_type, dimensions, material, budget, description, status, created_at) 
                  VALUES ('$name', '$email', '$phone', '$furniture_type', '$dimensions', '$material', '$budget', '$description', 'pending', NOW())";
        
        if (mysqli_query($conn, $query)) {
            $success = 'Вашето запитване беше изпратено успешно! Ще се свържем с вас в най-скоро време.';
            
            // Изчистване на полетата
            $_POST = array();
        } else {
            $error = 'Грешка при запазване: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мебели по поръчка - Мебели Онлайн</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .custom-order-hero {
            background: linear-gradient(135deg, rgba(139,111,71,0.95) 0%, rgba(93,78,55,0.95) 100%);
            padding: 4rem 0;
            text-align: center;
            color: #FDFBF7;
        }
        
        .custom-order-hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .custom-order-hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.95;
        }
        
        .custom-order-container {
            padding: 5rem 0;
            background-color: #F5EFE7;
        }
        
        .custom-order-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .custom-order-form {
            background: #FDFBF7;
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(139, 111, 71, 0.1);
        }
        
        .custom-order-form h2 {
            color: #5D4E37;
            margin-bottom: 2rem;
            font-size: 1.8rem;
        }
        
        .custom-order-info {
            background: #FDFBF7;
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(139, 111, 71, 0.1);
        }
        
        .custom-order-info h2 {
            color: #5D4E37;
            margin-bottom: 2rem;
            font-size: 1.8rem;
        }
        
        .info-item {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, #F5EFE7 0%, #E8DCC8 100%);
            border-radius: 6px;
            border-left: 4px solid #C17C4A;
        }
        
        .info-item h3 {
            color: #8B6F47;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .info-item p {
            color: #5D4E37;
            line-height: 1.6;
        }
        
        @media (max-width: 968px) {
            .custom-order-grid {
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
                    <li><a href="catalog.php">КАТАЛОГ</a></li>
                    <li><a href="custom_order.php">МЕБЕЛИ ПО ПОРЪЧКА</a></li>
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

    <!-- Hero секция -->
    <div class="custom-order-hero">
        <div class="container">
            <h1>Мебели по поръчка</h1>
            <p>Изработваме уникални мебели според вашите нужди и предпочитания. 
               Споделете вашата визия и ние ще я превърнем в реалност.</p>
        </div>
    </div>

    <!-- Форма и информация -->
    <div class="custom-order-container">
        <div class="container">
            <?php if ($error): ?>
                <div class="alert alert-error" style="max-width: 1200px; margin: 0 auto 2rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success" style="max-width: 1200px; margin: 0 auto 2rem;">
                    <?php echo $success; ?>
                    <p style="margin-top: 1rem;">
                        <a href="index.php" class="btn btn-primary">Обратно към начална страница</a>
                    </p>
                </div>
            <?php endif; ?>

            <div class="custom-order-grid">
                <!-- Форма -->
                <div class="custom-order-form">
                    <h2>Изпратете запитване</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name">Вашето име *</label>
                            <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Телефон *</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="furniture_type">Вид мебел *</label>
                            <select id="furniture_type" name="furniture_type" required>
                                <option value="">Изберете...</option>
                                <option value="Диван">Диван</option>
                                <option value="Маса">Маса</option>
                                <option value="Легло">Легло</option>
                                <option value="Гардероб">Гардероб</option>
                                <option value="Кухня">Кухня</option>
                                <option value="Рафт">Рафт/Етажерка</option>
                                <option value="Бюро">Бюро</option>
                                <option value="Друго">Друго</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="dimensions">Желани размери (опционално)</label>
                            <input type="text" id="dimensions" name="dimensions" placeholder="напр. 200x80x75 см" value="<?php echo isset($_POST['dimensions']) ? htmlspecialchars($_POST['dimensions']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="material">Предпочитан материал (опционално)</label>
                            <select id="material" name="material">
                                <option value="">Изберете...</option>
                                <option value="Масивно дърво">Масивно дърво</option>
                                <option value="ПДЧ">ПДЧ</option>
                                <option value="МДФ">МДФ</option>
                                <option value="Метал">Метал</option>
                                <option value="Стъкло">Стъкло</option>
                                <option value="Комбинация">Комбинация</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="budget">Бюджет (опционално)</label>
                            <input type="text" id="budget" name="budget" placeholder="напр. 500-1000 €" value="<?php echo isset($_POST['budget']) ? htmlspecialchars($_POST['budget']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="description">Опишете вашата идея *</label>
                            <textarea id="description" name="description" rows="5" placeholder="Опишете подробно какво търсите - стил, цветове, функционалност и др." required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1.2rem;">
                            ИЗПРАТИ ЗАПИТВАНЕ
                        </button>
                    </form>
                </div>

                <!-- Информация -->
                <div class="custom-order-info">
                    <h2>Как работи процесът?</h2>

                    <div class="info-item">
                        <h3>1️⃣ Запитване</h3>
                        <p>Попълнете формата с вашите изисквания и предпочитания. Колкото повече детайли споделите, толкова по-точна оферта ще получите.</p>
                    </div>

                    <div class="info-item">
                        <h3>2️⃣ Консултация</h3>
                        <p>Нашият екип ще се свърже с вас в рамките на 24 часа за да обсъдим вашата идея и да уточним всички детайли.</p>
                    </div>

                    <div class="info-item">
                        <h3>3️⃣ Оферта</h3>
                        <p>Ще получите детайлна оферта с цена, срокове за изработка и 3D визуализация на крайния продукт.</p>
                    </div>

                    <div class="info-item">
                        <h3>4️⃣ Изработка</h3>
                        <p>След вашето одобрение започваме изработката. Ще ви информираме за напредъка на всеки етап.</p>
                    </div>

                    <div class="info-item">
                        <h3>5️⃣ Доставка</h3>
                        <p>Доставяме и монтираме готовата мебел на желаното от вас място. Гаранция 5 години!</p>
                    </div>

                    <div style="margin-top: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #C17C4A 0%, #A86738 100%); border-radius: 6px; color: #FDFBF7;">
                        <h3 style="color: #FDFBF7; margin-bottom: 1rem;">💡 Защо да изберете нас?</h3>
                        <ul style="list-style: none; padding: 0;">
                            <li style="padding: 0.5rem 0;">✓ Индивидуален дизайн</li>
                            <li style="padding: 0.5rem 0;">✓ Висококачествени материали</li>
                            <li style="padding: 0.5rem 0;">✓ Професионално изпълнение</li>
                            <li style="padding: 0.5rem 0;">✓ 5 години гаранция</li>
                            <li style="padding: 0.5rem 0;">✓ Безплатна консултация</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        <li><a href="custom_order.php">Мебели по поръчка</a></li>
                        <li><a href="login.php">Вход</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>КОНТАКТИ</h4>
                    <p>Email: momchil.petrov007@gmail.com</p>
                    <p>Тел: +359 888 123 456</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Мебели Онлайн. Всички права запазени.</p>
            </div>
        </div>
    </footer>
</body>
</html>
