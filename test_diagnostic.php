<?php
// Тестов файл за диагностика
echo "✅ PHP работи!<br>";
echo "📁 Текуща папка: " . __DIR__ . "<br>";
echo "📄 Този файл: " . __FILE__ . "<br>";
echo "🌐 Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";

// Проверка дали съществува config.php
if (file_exists('config.php')) {
    echo "✅ config.php съществува<br>";
} else {
    echo "❌ config.php НЕ е намерен<br>";
}

// Проверка дали съществува view_order.php
if (file_exists('view_order.php')) {
    echo "✅ view_order.php съществува<br>";
    echo "📏 Размер: " . filesize('view_order.php') . " bytes<br>";
} else {
    echo "❌ view_order.php НЕ е намерен<br>";
}

// Списък на всички PHP файлове в папката
echo "<hr>";
echo "<h3>PHP файлове в папката:</h3>";
$files = glob('*.php');
foreach ($files as $file) {
    echo "- $file<br>";
}
?>
