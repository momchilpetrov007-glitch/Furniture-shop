<?php
require_once 'config.php';

// Проверка дали потребителят е администратор
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Вземане на ID на мебелта
if (!isset($_GET['id'])) {
    redirect('admin_panel.php');
}

$furniture_id = (int)$_GET['id'];

// Вземане на данните на мебелта
$query = "SELECT * FROM furniture WHERE id = $furniture_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    redirect('admin_panel.php');
}

$furniture = mysqli_fetch_assoc($result);

// Изтриване на мебелта
$delete_query = "DELETE FROM furniture WHERE id = $furniture_id";

if (mysqli_query($conn, $delete_query)) {
    // Изтриване на снимката от сървъра
    if (file_exists('images/' . $furniture['image'])) {
        unlink('images/' . $furniture['image']);
    }
}

redirect('admin_panel.php');
?>
