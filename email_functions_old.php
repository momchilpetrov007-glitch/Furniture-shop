<?php
/**
 * Email Functions for Furniture Shop
 * Изпращане на имейли при поръчки
 */

/**
 * Изпращане на имейл до клиента при нова поръчка
 */
function sendOrderConfirmationEmail($conn, $order_id, $user_email, $user_name) {
    // Вземане на детайли за поръчката
    $order_query = "SELECT * FROM orders WHERE id = $order_id";
    $order_result = mysqli_query($conn, $order_query);
    $order = mysqli_fetch_assoc($order_result);
    
    // Вземане на продуктите от поръчката
    $items_query = "SELECT oi.*, f.name, f.image 
                    FROM order_items oi 
                    JOIN furniture f ON oi.furniture_id = f.id 
                    WHERE oi.order_id = $order_id";
    $items_result = mysqli_query($conn, $items_query);
    
    // Създаване на HTML съдържание
    $items_html = '';
    $total = 0;
    
    while ($item = mysqli_fetch_assoc($items_result)) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        
        $items_html .= "
        <tr>
            <td style='padding: 15px; border-bottom: 1px solid #e5e5e5;'>
                <strong>" . htmlspecialchars($item['name']) . "</strong><br>
                <span style='color: #666; font-size: 0.9rem;'>Количество: {$item['quantity']}</span>
            </td>
            <td style='padding: 15px; border-bottom: 1px solid #e5e5e5; text-align: right;'>
                €" . number_format($subtotal, 2) . "
            </td>
        </tr>";
    }
    
    // Изчисляване на доставка
    $delivery_cost = $total >= 500 ? 0 : 15;
    $final_total = $total + $delivery_cost;
    
    // Email subject
    $subject = "Потвърждение на поръчка #$order_id - Мебели Онлайн";
    
    // Email body (HTML)
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #1a1a1a; color: white; padding: 30px; text-align: center; }
            .content { background: #ffffff; padding: 30px; }
            .order-details { background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 8px; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            .total-row { font-weight: bold; font-size: 1.2rem; background: #f5f5f5; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 0.9rem; }
            .button { display: inline-block; padding: 12px 30px; background: #1a1a1a; color: white; text-decoration: none; border-radius: 4px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1 style='margin: 0; font-weight: 300;'>МЕБЕЛИ ОНЛАЙН</h1>
            </div>
            
            <div class='content'>
                <h2 style='color: #1a1a1a;'>Благодарим за вашата поръчка!</h2>
                <p>Здравейте, <strong>$user_name</strong>,</p>
                <p>Вашата поръчка беше приета успешно и в момента се обработва.</p>
                
                <div class='order-details'>
                    <h3 style='margin-top: 0;'>Детайли на поръчката</h3>
                    <p><strong>Номер на поръчка:</strong> #$order_id</p>
                    <p><strong>Дата:</strong> " . date('d.m.Y H:i', strtotime($order['created_at'])) . "</p>
                    <p><strong>Адрес за доставка:</strong><br>" . nl2br(htmlspecialchars($order['delivery_address'])) . "</p>
                    <p><strong>Телефон:</strong> {$order['phone']}</p>
                </div>
                
                <h3>Продукти:</h3>
                <table>
                    <thead>
                        <tr style='background: #f5f5f5;'>
                            <th style='padding: 15px; text-align: left;'>Продукт</th>
                            <th style='padding: 15px; text-align: right;'>Цена</th>
                        </tr>
                    </thead>
                    <tbody>
                        $items_html
                    </tbody>
                    <tfoot>
                        <tr>
                            <td style='padding: 10px; text-align: right;'>Междинна сума:</td>
                            <td style='padding: 10px; text-align: right;'>€" . number_format($total, 2) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; text-align: right;'>Доставка:</td>
                            <td style='padding: 10px; text-align: right;'>" . ($delivery_cost == 0 ? 'Безплатна' : '€' . number_format($delivery_cost, 2)) . "</td>
                        </tr>
                        <tr class='total-row'>
                            <td style='padding: 15px; text-align: right;'>ОБЩА СУМА:</td>
                            <td style='padding: 15px; text-align: right;'>€" . number_format($final_total, 2) . "</td>
                        </tr>
                    </tfoot>
                </table>
                
                <p style='margin-top: 30px;'>Ще се свържем с вас в най-скоро време за потвърждаване на доставката.</p>
                
                <div style='text-align: center;'>
                    <a href='http://localhost/furniture_shop/my_orders.php' class='button'>Виж моите поръчки</a>
                </div>
            </div>
            
            <div class='footer'>
                <p>Ако имате въпроси, моля свържете се с нас:</p>
                <p>Email: info@mebeli.bg | Тел: +359 888 123 456</p>
                <p>&copy; 2024 Мебели Онлайн. Всички права запазени.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Мебели Онлайн <noreply@mebeli.bg>" . "\r\n";
    
    // Изпращане на имейл
    return mail($user_email, $subject, $message, $headers);
}

/**
 * Изпращане на имейл до администратора при нова поръчка
 */
function sendAdminNotification($conn, $order_id) {
    // Вземане на детайли за поръчката
    $order_query = "SELECT o.*, u.username, u.email as user_email 
                    FROM orders o 
                    JOIN users u ON o.user_id = u.id 
                    WHERE o.id = $order_id";
    $order_result = mysqli_query($conn, $order_query);
    $order = mysqli_fetch_assoc($order_result);
    
    // Вземане на продуктите от поръчката
    $items_query = "SELECT oi.*, f.name 
                    FROM order_items oi 
                    JOIN furniture f ON oi.furniture_id = f.id 
                    WHERE oi.order_id = $order_id";
    $items_result = mysqli_query($conn, $items_query);
    
    $items_list = '';
    while ($item = mysqli_fetch_assoc($items_result)) {
        $items_list .= "- {$item['name']} x {$item['quantity']} (€" . number_format($item['price'], 2) . ")\n";
    }
    
    // Email subject
    $subject = "🔔 Нова поръчка #$order_id от {$order['username']}";
    
    // Email body
    $message = "
Нова поръчка в системата!

═══════════════════════════════════════
ПОРЪЧКА #$order_id
═══════════════════════════════════════

Клиент: {$order['username']}
Email: {$order['user_email']}
Телефон: {$order['phone']}

Дата: " . date('d.m.Y H:i', strtotime($order['created_at'])) . "

АДРЕС ЗА ДОСТАВКА:
{$order['delivery_address']}

ПРОДУКТИ:
$items_list

ОБЩА СУМА: €" . number_format($order['total_price'], 2) . "

БЕЛЕЖКИ:
{$order['notes']}

═══════════════════════════════════════

Вход в admin панел:
http://localhost/furniture_shop/admin_panel.php
";
    
    // Имейл на администратора - ЗАМЕНЕТЕ С ВАШИЯ!
    $admin_email = "admin@mebeli.bg";
    
    // Email headers
    $headers = "From: Система <system@mebeli.bg>" . "\r\n";
    
    // Изпращане на имейл
    return mail($admin_email, $subject, $message, $headers);
}

/**
 * Изпращане на имейл при промяна на статус на поръчка
 */
function sendOrderStatusUpdateEmail($conn, $order_id, $new_status) {
    // Вземане на детайли
    $order_query = "SELECT o.*, u.email, u.username 
                    FROM orders o 
                    JOIN users u ON o.user_id = u.id 
                    WHERE o.id = $order_id";
    $order_result = mysqli_query($conn, $order_query);
    $order = mysqli_fetch_assoc($order_result);
    
    // Статуси на български
    $status_labels = array(
        'pending' => 'Обработва се',
        'confirmed' => 'Потвърдена',
        'shipped' => 'Изпратена',
        'delivered' => 'Доставена',
        'cancelled' => 'Отказана'
    );
    
    $status_text = $status_labels[$new_status] ?? $new_status;
    
    $subject = "Статус на поръчка #$order_id: $status_text";
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #1a1a1a; color: white; padding: 30px; text-align: center; }
            .content { padding: 30px; background: white; }
            .status-badge { display: inline-block; padding: 10px 20px; background: #27ae60; color: white; border-radius: 20px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1 style='margin: 0;'>МЕБЕЛИ ОНЛАЙН</h1>
            </div>
            <div class='content'>
                <h2>Актуализация на поръчка #$order_id</h2>
                <p>Здравейте, <strong>{$order['username']}</strong>,</p>
                <p>Статусът на вашата поръчка беше променен:</p>
                <div class='status-badge'>$status_text</div>
                <p>Обща сума: €" . number_format($order['total_price'], 2) . "</p>
                <p>Адрес за доставка: {$order['delivery_address']}</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Мебели Онлайн <noreply@mebeli.bg>" . "\r\n";
    
    return mail($order['email'], $subject, $message, $headers);
}
?>
