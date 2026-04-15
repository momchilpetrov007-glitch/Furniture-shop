<?php
require_once 'config.php';

// Проверка дали потребителят е админ
if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'No ID provided']);
    exit;
}

$id = intval($_GET['id']);

$query = "SELECT * FROM custom_requests WHERE id = $id";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        'success' => true,
        'request' => $row
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Request not found']);
}
?>
