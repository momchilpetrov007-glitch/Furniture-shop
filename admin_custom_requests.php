<?php
require_once 'config.php';

// Проверка дали потребителят е админ
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Обработка на промяна на статус
if (isset($_POST['update_status'])) {
    $request_id = intval($_POST['request_id']);
    $new_status = escape($conn, $_POST['new_status']);
    
    $update_query = "UPDATE custom_requests SET status = '$new_status' WHERE id = $request_id";
    mysqli_query($conn, $update_query);
    
    $success = "Статусът беше променен успешно!";
}

// Статистики
$total_requests_query = "SELECT COUNT(*) as count FROM custom_requests";
$total_requests = mysqli_fetch_assoc(mysqli_query($conn, $total_requests_query))['count'];

$pending_requests_query = "SELECT COUNT(*) as count FROM custom_requests WHERE status = 'pending'";
$pending_requests = mysqli_fetch_assoc(mysqli_query($conn, $pending_requests_query))['count'];

$contacted_requests_query = "SELECT COUNT(*) as count FROM custom_requests WHERE status IN ('contacted', 'quoted')";
$contacted_requests = mysqli_fetch_assoc(mysqli_query($conn, $contacted_requests_query))['count'];

$completed_requests_query = "SELECT COUNT(*) as count FROM custom_requests WHERE status = 'completed'";
$completed_requests = mysqli_fetch_assoc(mysqli_query($conn, $completed_requests_query))['count'];
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление на запитвания - Админ Панел</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container {
            padding: 3rem 0;
            min-height: 70vh;
            background-color: #F5EFE7;
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .admin-header h1 {
            color: #5D4E37;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .admin-header p {
            color: #8B6F47;
            font-size: 1.1rem;
        }
        
        .admin-nav {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }
        
        .admin-nav-link {
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .admin-nav-link.active {
            background: linear-gradient(135deg, #8B6F47 0%, #5D4E37 100%);
            color: #FDFBF7;
        }
        
        .admin-nav-link:not(.active) {
            background: #FDFBF7;
            color: #8B6F47;
            border-color: #D4A574;
        }
        
        .admin-nav-link:not(.active):hover {
            background: #E8DCC8;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #FDFBF7 0%, #F5EFE7 100%);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(139, 111, 71, 0.1);
            border-left: 4px solid #8B6F47;
        }
        
        .stat-card h3 {
            font-size: 0.9rem;
            color: #8B6F47;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #5D4E37;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: #C17C4A;
            margin-top: 0.5rem;
        }
        
        .admin-table-container {
            background-color: #FDFBF7;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(139, 111, 71, 0.1);
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table thead {
            background: linear-gradient(135deg, #8B6F47 0%, #5D4E37 100%);
            color: #FDFBF7;
        }
        
        .admin-table th {
            padding: 1.2rem;
            text-align: left;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }
        
        .admin-table td {
            padding: 1.2rem;
            border-bottom: 1px solid #E8DCC8;
        }
        
        .admin-table tbody tr:hover {
            background-color: #F5EFE7;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #FFE5CC;
            color: #C17C4A;
        }
        
        .status-contacted {
            background-color: #D4E8FF;
            color: #4A90C1;
        }
        
        .status-quoted {
            background-color: #FFF9E6;
            color: #D4A574;
        }
        
        .status-approved {
            background-color: #E3F2FD;
            color: #1565C0;
        }
        
        .status-in_progress {
            background-color: #D4F1E8;
            color: #4AC18B;
        }
        
        .status-completed {
            background-color: #D4F1D4;
            color: #6B8E4E;
        }
        
        .status-cancelled {
            background-color: #FFD4D4;
            color: #C14A4A;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }
        
        .btn-view {
            background-color: #7FA8C9;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: #FDFBF7;
            padding: 2rem;
            border-radius: 8px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #8B6F47;
        }
        
        @media (max-width: 968px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .admin-table-container {
                overflow-x: auto;
            }
            
            .admin-nav {
                flex-direction: column;
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
                    <li><a href="admin_orders.php">АДМИН ПАНЕЛ</a></li>
                    <li><a href="logout.php">ИЗХОД (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <div class="container">
            <div class="admin-header">
                <h1>Админ Панел</h1>
                <p>Управление на магазина</p>
            </div>

            <!-- Admin Navigation -->
            <div class="admin-nav">
                <a href="admin_orders.php" class="admin-nav-link">📦 Поръчки</a>
                <a href="admin_furniture.php" class="admin-nav-link">🪑 Мебели</a>
                <a href="admin_custom_requests.php" class="admin-nav-link active">💬 Запитвания</a>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success" style="margin-bottom: 2rem;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <h2 style="color: #5D4E37; margin-bottom: 2rem; font-size: 2rem;">Управление на запитвания</h2>

            <!-- Статистики -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Всички запитвания</h3>
                    <div class="stat-value"><?php echo $total_requests; ?></div>
                    <div class="stat-label">Общо</div>
                </div>
                <div class="stat-card">
                    <h3>Нови</h3>
                    <div class="stat-value"><?php echo $pending_requests; ?></div>
                    <div class="stat-label">За обработка</div>
                </div>
                <div class="stat-card">
                    <h3>В процес</h3>
                    <div class="stat-value"><?php echo $contacted_requests; ?></div>
                    <div class="stat-label">Contacted/Quoted</div>
                </div>
                <div class="stat-card">
                    <h3>Завършени</h3>
                    <div class="stat-value"><?php echo $completed_requests; ?></div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>

            <!-- Таблица със запитвания -->
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Клиент</th>
                            <th>Email/Телефон</th>
                            <th>Вид мебел</th>
                            <th>Дата</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $requests_query = "SELECT * FROM custom_requests ORDER BY created_at DESC";
                        $requests_result = mysqli_query($conn, $requests_query);

                        if (mysqli_num_rows($requests_result) == 0):
                        ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem; color: #8B6F47;">
                                    Все още няма запитвания
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php while ($request = mysqli_fetch_assoc($requests_result)):
                                $status_class = 'status-' . str_replace('_', '-', $request['status']);
                                $status_labels = [
                                    'pending' => 'Ново',
                                    'contacted' => 'Свързани',
                                    'quoted' => 'Оферта',
                                    'approved' => 'Одобрено',
                                    'in_progress' => 'В процес',
                                    'completed' => 'Завършено',
                                    'cancelled' => 'Отказано'
                                ];
                                $status_text = $status_labels[$request['status']] ?? $request['status'];
                            ?>
                                <tr>
                                    <td><strong>#<?php echo $request['id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($request['name']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($request['email']); ?><br>
                                        <small style="color: #8B6F47;"><?php echo htmlspecialchars($request['phone']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($request['furniture_type']); ?></td>
                                    <td><?php echo date('d.m.Y', strtotime($request['created_at'])); ?></td>
                                    <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="viewRequest(<?php echo $request['id']; ?>)" class="btn btn-view btn-sm">Виж детайли</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal за детайли -->
    <div id="requestModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="color: #5D4E37; margin: 0;">Детайли на запитването</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div id="modalBody"></div>
        </div>
    </div>

    <!-- Футър -->
    <footer class="footer">
        <div class="container">
            <p style="text-align: center;">&copy; 2024 Мебели Онлайн. Всички права запазени.</p>
        </div>
    </footer>

    <script>
    function viewRequest(id) {
        fetch('get_request_details.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const r = data.request;
                    const statusLabels = {
                        'pending': 'Ново',
                        'contacted': 'Свързани',
                        'quoted': 'Оферта',
                        'approved': 'Одобрено',
                        'in_progress': 'В процес',
                        'completed': 'Завършено',
                        'cancelled': 'Отказано'
                    };
                    
                    document.getElementById('modalBody').innerHTML = `
                        <div style="margin-bottom: 2rem;">
                            <h3 style="color: #8B6F47; margin-bottom: 1rem;">Информация за клиента</h3>
                            <p><strong>Име:</strong> ${r.name}</p>
                            <p><strong>Email:</strong> ${r.email}</p>
                            <p><strong>Телефон:</strong> ${r.phone}</p>
                        </div>
                        
                        <div style="margin-bottom: 2rem;">
                            <h3 style="color: #8B6F47; margin-bottom: 1rem;">Детайли за мебелта</h3>
                            <p><strong>Вид:</strong> ${r.furniture_type}</p>
                            <p><strong>Размери:</strong> ${r.dimensions || 'Не е посочено'}</p>
                            <p><strong>Материал:</strong> ${r.material || 'Не е посочено'}</p>
                            <p><strong>Бюджет:</strong> ${r.budget || 'Не е посочено'}</p>
                        </div>
                        
                        <div style="margin-bottom: 2rem;">
                            <h3 style="color: #8B6F47; margin-bottom: 1rem;">Описание</h3>
                            <p style="background: #F5EFE7; padding: 1rem; border-radius: 4px;">${r.description}</p>
                        </div>
                        
                        <div style="margin-bottom: 2rem;">
                            <h3 style="color: #8B6F47; margin-bottom: 1rem;">Промяна на статус</h3>
                            <form method="POST" action="">
                                <input type="hidden" name="request_id" value="${r.id}">
                                <select name="new_status" style="width: 100%; padding: 0.75rem; border: 2px solid #D4A574; border-radius: 4px; margin-bottom: 1rem;">
                                    <option value="pending" ${r.status === 'pending' ? 'selected' : ''}>Ново</option>
                                    <option value="contacted" ${r.status === 'contacted' ? 'selected' : ''}>Свързани</option>
                                    <option value="quoted" ${r.status === 'quoted' ? 'selected' : ''}>Оферта изпратена</option>
                                    <option value="approved" ${r.status === 'approved' ? 'selected' : ''}>Одобрено</option>
                                    <option value="in_progress" ${r.status === 'in_progress' ? 'selected' : ''}>В процес</option>
                                    <option value="completed" ${r.status === 'completed' ? 'selected' : ''}>Завършено</option>
                                    <option value="cancelled" ${r.status === 'cancelled' ? 'selected' : ''}>Отказано</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-primary" style="width: 100%;">Запази промяна</button>
                            </form>
                        </div>
                        
                        <div style="text-align: center; padding-top: 1rem; border-top: 1px solid #E8DCC8;">
                            <p style="color: #8B6F47; font-size: 0.9rem;">Дата на запитване: ${new Date(r.created_at).toLocaleString('bg-BG')}</p>
                        </div>
                    `;
                    
                    document.getElementById('requestModal').classList.add('active');
                }
            });
    }
    
    function closeModal() {
        document.getElementById('requestModal').classList.remove('active');
    }
    
    // Close modal on outside click
    document.getElementById('requestModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    </script>
</body>
</html>
