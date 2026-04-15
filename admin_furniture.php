<?php
require_once 'config.php';
if (!isLoggedIn() || !isAdmin()) { redirect('index.php'); }
$total_furniture = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM furniture"))['count'];
$in_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM furniture WHERE stock > 0"))['count'];
$out_of_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM furniture WHERE stock = 0"))['count'];
$total_value = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(price * stock) as value FROM furniture"))['value'] ?? 0;
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление на мебели - Админ Панел</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin_orders.php">
</head>
<body>
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
    <div class="admin-container" style="padding: 3rem 0; min-height: 70vh; background-color: #F5EFE7;">
        <div class="container">
            <div class="admin-header" style="text-align: center; margin-bottom: 3rem;">
                <h1 style="color: #5D4E37; font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Админ Панел</h1>
                <p style="color: #8B6F47; font-size: 1.1rem;">Управление на магазина</p>
            </div>
            <div class="admin-nav" style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 3rem;">
                <a href="admin_orders.php" class="admin-nav-link" style="padding: 1rem 2rem; text-decoration: none; border-radius: 6px; font-weight: 600; background: #FDFBF7; color: #8B6F47; border: 2px solid #D4A574;">📦 Поръчки</a>
                <a href="admin_furniture.php" class="admin-nav-link active" style="padding: 1rem 2rem; text-decoration: none; border-radius: 6px; font-weight: 600; background: linear-gradient(135deg, #8B6F47 0%, #5D4E37 100%); color: #FDFBF7;">🪑 Мебели</a>
                <a href="admin_custom_requests.php" class="admin-nav-link" style="padding: 1rem 2rem; text-decoration: none; border-radius: 6px; font-weight: 600; background: #FDFBF7; color: #8B6F47; border: 2px solid #D4A574;">💬 Запитвания</a>
            </div>
            <h2 style="color: #5D4E37; margin-bottom: 2rem; font-size: 2rem;">Управление на мебели</h2>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem; margin-bottom: 3rem;">
                <div style="background: linear-gradient(135deg, #FDFBF7 0%, #F5EFE7 100%); padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(139, 111, 71, 0.1); border-left: 4px solid #8B6F47;">
                    <h3 style="font-size: 0.9rem; color: #8B6F47; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem;">Общо продукти</h3>
                    <div style="font-size: 2.5rem; font-weight: 700; color: #5D4E37;"><?php echo $total_furniture; ?></div>
                    <div style="font-size: 0.85rem; color: #C17C4A; margin-top: 0.5rem;">Всички</div>
                </div>
                <div style="background: linear-gradient(135deg, #FDFBF7 0%, #F5EFE7 100%); padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(139, 111, 71, 0.1); border-left: 4px solid #8B6F47;">
                    <h3 style="font-size: 0.9rem; color: #8B6F47; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem;">В наличност</h3>
                    <div style="font-size: 2.5rem; font-weight: 700; color: #5D4E37;"><?php echo $in_stock; ?></div>
                    <div style="font-size: 0.85rem; color: #C17C4A; margin-top: 0.5rem;">Активни</div>
                </div>
                <div style="background: linear-gradient(135deg, #FDFBF7 0%, #F5EFE7 100%); padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(139, 111, 71, 0.1); border-left: 4px solid #8B6F47;">
                    <h3 style="font-size: 0.9rem; color: #8B6F47; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem;">Изчерпани</h3>
                    <div style="font-size: 2.5rem; font-weight: 700; color: #5D4E37;"><?php echo $out_of_stock; ?></div>
                    <div style="font-size: 0.85rem; color: #C17C4A; margin-top: 0.5rem;">Нужда от зареждане</div>
                </div>
                <div style="background: linear-gradient(135deg, #FDFBF7 0%, #F5EFE7 100%); padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(139, 111, 71, 0.1); border-left: 4px solid #8B6F47;">
                    <h3 style="font-size: 0.9rem; color: #8B6F47; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem;">Стойност</h3>
                    <div style="font-size: 2.5rem; font-weight: 700; color: #5D4E37;">€<?php echo number_format($total_value, 0); ?></div>
                    <div style="font-size: 0.85rem; color: #C17C4A; margin-top: 0.5rem;">Инвентар</div>
                </div>
            </div>
            <div style="margin-bottom: 2rem;">
                <a href="add_furniture.php" class="btn btn-primary" style="padding: 0.9rem 2rem;">+ Добави нова мебел</a>
            </div>
            <div style="background-color: #FDFBF7; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(139, 111, 71, 0.1); overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: linear-gradient(135deg, #8B6F47 0%, #5D4E37 100%); color: #FDFBF7;">
                        <tr>
                            <th style="padding: 1.2rem; text-align: left; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">ID</th>
                            <th style="padding: 1.2rem; text-align: left; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">Име</th>
                            <th style="padding: 1.2rem; text-align: left; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">Категория</th>
                            <th style="padding: 1.2rem; text-align: left; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">Цена</th>
                            <th style="padding: 1.2rem; text-align: left; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">Наличност</th>
                            <th style="padding: 1.2rem; text-align: left; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $furniture_result = mysqli_query($conn, "SELECT * FROM furniture ORDER BY id DESC");
                        while ($item = mysqli_fetch_assoc($furniture_result)):
                        ?>
                            <tr style="border-bottom: 1px solid #E8DCC8;">
                                <td style="padding: 1.2rem;"><strong>#<?php echo $item['id']; ?></strong></td>
                                <td style="padding: 1.2rem;"><?php echo htmlspecialchars($item['name']); ?></td>
                                <td style="padding: 1.2rem;"><?php echo htmlspecialchars($item['category']); ?></td>
                                <td style="padding: 1.2rem;"><strong>€<?php echo number_format($item['price'], 2); ?></strong></td>
                                <td style="padding: 1.2rem;">
                                    <?php if ($item['stock'] > 0): ?>
                                        <span style="display: inline-block; padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; background-color: #D4F1D4; color: #6B8E4E;"><?php echo $item['stock']; ?> бр.</span>
                                    <?php else: ?>
                                        <span style="display: inline-block; padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; background-color: #FFD4D4; color: #C14A4A;">Изчерпано</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1.2rem;">
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="product.php?id=<?php echo $item['id']; ?>" class="btn btn-view btn-sm" style="padding: 0.5rem 1rem; font-size: 0.85rem; background-color: #7FA8C9; color: white; text-decoration: none; border-radius: 4px;">Виж</a>
                                        <a href="edit_furniture.php?id=<?php echo $item['id']; ?>" class="btn btn-edit btn-sm" style="padding: 0.5rem 1rem; font-size: 0.85rem; background-color: #D4A574; color: #5D4E37; text-decoration: none; border-radius: 4px;">Редактирай</a>
                                        <a href="delete_furniture.php?id=<?php echo $item['id']; ?>" class="btn btn-delete btn-sm" onclick="return confirm('Сигурни ли сте?')" style="padding: 0.5rem 1rem; font-size: 0.85rem; background: linear-gradient(135deg, #C17C4A 0%, #A86738 100%); color: white; text-decoration: none; border-radius: 4px;">Изтрий</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p style="text-align: center;">&copy; 2024 Мебели Онлайн. Всички права запазени.</p>
        </div>
    </footer>
</body>
</html>
