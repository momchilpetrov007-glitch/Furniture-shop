<?php
/**
 * Email Functions for Furniture Shop - PHPMailer Version
 * Изпращане на имейли при поръчки с Gmail SMTP
 * Конфигуриран за: momchil.petrov007@gmail.com
 */

// Импорт на PHPMailer класове
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Зареждане на PHPMailer (ако използвате Composer)
require 'vendor/autoload.php';

// АКО НЕ ИЗПОЛЗВАТЕ COMPOSER, uncomment-нете тези редове:
// require 'PHPMailer/src/Exception.php';
// require 'PHPMailer/src/PHPMailer.php';
// require 'PHPMailer/src/SMTP.php';

/**
 * Конфигурация на SMTP
 */
function getSMTPMailer() {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP настройки
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        // ═══════════════════════════════════════════════════════════
        // 📧 GMAIL НАСТРОЙКИ
        // ═══════════════════════════════════════════════════════════
        $mail->Username   = 'momchil.petrov007@gmail.com';
        $mail->Password   = 'gpozkwfpqznbuevs';        // ← САМО ТОВА ТРЯБВА ДА ПРОМЕНИТЕ!
        // ═══════════════════════════════════════════════════════════
        // Създайте App Password на: https://myaccount.google.com/apppasswords
        // Копирайте 16-символната парола БЕЗ интервали
        // Примерно: abcdefghijklmnop
        // ═══════════════════════════════════════════════════════════
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        
        // От кого е имейла
        $mail->setFrom('momchil.petrov007@gmail.com', 'Мебели Онлайн');
        
        return $mail;
    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Изпращане на имейл до клиента при нова поръчка
 */
function sendOrderConfirmationEmail($conn, $order_id, $user_email, $user_name) {
    $mail = getSMTPMailer();
    if (!$mail) return false;
    
    try {
        // Получател
        $mail->addAddress($user_email, $user_name);
        
        // Тема
        $mail->Subject = "Потвърждение на поръчка #$order_id - Мебели Онлайн";
        
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
                <td style='padding: 15px; border-bottom: 1px solid #D4A574;'>
                    <strong style='color: #5D4E37;'>" . htmlspecialchars($item['name']) . "</strong><br>
                    <span style='color: #8B6F47; font-size: 0.9rem;'>Количество: {$item['quantity']}</span>
                </td>
                <td style='padding: 15px; border-bottom: 1px solid #D4A574; text-align: right;'>
                    <strong style='color: #C17C4A;'>€" . number_format($subtotal, 2) . "</strong>
                </td>
            </tr>";
        }
        
        // Изчисляване на доставка
        $delivery_cost = $total >= 500 ? 0 : 15;
        $final_total = $total + $delivery_cost;
        
        // HTML съдържание със топли цветове
        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #5D4E37; background-color: #F5EFE7; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #8B6F47 0%, #5D4E37 100%); color: #FDFBF7; padding: 40px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #FDFBF7; padding: 40px; border-radius: 0 0 8px 8px; }
                .order-details { background: linear-gradient(135deg, #F5EFE7 0%, #E8DCC8 100%); padding: 25px; margin: 25px 0; border-radius: 8px; border-left: 4px solid #C17C4A; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .total-row { font-weight: bold; font-size: 1.3rem; background: linear-gradient(135deg, #F5EFE7 0%, #E8DCC8 100%); }
                .footer { text-align: center; padding: 30px; color: #8B6F47; font-size: 0.9rem; }
                .button { display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #C17C4A 0%, #A86738 100%); color: #FDFBF7; text-decoration: none; border-radius: 6px; margin: 25px 0; font-weight: bold; box-shadow: 0 4px 12px rgba(193, 124, 74, 0.3); }
                h2 { color: #5D4E37; }
                h3 { color: #8B6F47; margin-top: 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1 style='margin: 0; font-weight: 700; letter-spacing: 2px;'>МЕБЕЛИ ОНЛАЙН</h1>
                </div>
                
                <div class='content'>
                    <h2>Благодарим за вашата поръчка!</h2>
                    <p>Здравейте, <strong>$user_name</strong>,</p>
                    <p>Вашата поръчка беше приета успешно и в момента се обработва.</p>
                    
                    <div class='order-details'>
                        <h3>Детайли на поръчката</h3>
                        <p><strong>Номер на поръчка:</strong> #$order_id</p>
                        <p><strong>Дата:</strong> " . date('d.m.Y H:i', strtotime($order['created_at'])) . "</p>
                        <p><strong>Адрес за доставка:</strong><br>" . nl2br(htmlspecialchars($order['delivery_address'])) . "</p>
                        <p><strong>Телефон:</strong> {$order['phone']}</p>
                    </div>
                    
                    <h3>Продукти:</h3>
                    <table>
                        <thead>
                            <tr style='background: linear-gradient(135deg, #8B6F47 0%, #5D4E37 100%); color: #FDFBF7;'>
                                <th style='padding: 15px; text-align: left;'>Продукт</th>
                                <th style='padding: 15px; text-align: right;'>Цена</th>
                            </tr>
                        </thead>
                        <tbody>
                            $items_html
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style='padding: 12px; text-align: right; color: #8B6F47;'>Междинна сума:</td>
                                <td style='padding: 12px; text-align: right; color: #8B6F47;'><strong>€" . number_format($total, 2) . "</strong></td>
                            </tr>
                            <tr>
                                <td style='padding: 12px; text-align: right; color: #8B6F47;'>Доставка:</td>
                                <td style='padding: 12px; text-align: right; color: #8B6F47;'><strong>" . ($delivery_cost == 0 ? 'Безплатна' : '€' . number_format($delivery_cost, 2)) . "</strong></td>
                            </tr>
                            <tr class='total-row'>
                                <td style='padding: 20px; text-align: right; color: #5D4E37;'>ОБЩА СУМА:</td>
                                <td style='padding: 20px; text-align: right; color: #C17C4A;'>€" . number_format($final_total, 2) . "</td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <p style='margin-top: 30px; color: #8B6F47;'>Ще се свържем с вас в най-скоро време за потвърждаване на доставката.</p>
                    
                    <div style='text-align: center;'>
                        <a href='http://localhost/furniture_shop/my_orders.php' class='button'>Виж моите поръчки</a>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>Ако имате въпроси, моля свържете се с нас:</p>
                    <p><strong>Email:</strong> momchil.petrov007@gmail.com | <strong>Тел:</strong> +359 888 123 456</p>
                    <p style='margin-top: 20px;'>&copy; 2024 Мебели Онлайн. Всички права запазени.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Задаване на HTML
        $mail->isHTML(true);
        $mail->Body = $htmlBody;
        
        // Текстова версия (fallback)
        $mail->AltBody = "Благодарим за поръчката #$order_id. Обща сума: €" . number_format($final_total, 2);
        
        // Изпращане
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Изпращане на имейл до администратора при нова поръчка
 */
function sendAdminNotification($conn, $order_id) {
    $mail = getSMTPMailer();
    if (!$mail) return false;
    
    try {
        // Админ email (ВИЕ ще получавате нотификации тук)
        $mail->addAddress('momchil.petrov007@gmail.com', 'Администратор');
        
        // Тема
        $mail->Subject = "🔔 Нова поръчка #$order_id";
        
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
        
        // Текстово съдържание
        $textBody = "
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
        
        // Задаване на текст
        $mail->isHTML(false);
        $mail->Body = $textBody;
        
        // Изпращане
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Изпращане на имейл при промяна на статус на поръчка
 */
function sendOrderStatusUpdateEmail($conn, $order_id, $new_status) {
    $mail = getSMTPMailer();
    if (!$mail) return false;
    
    try {
        // Вземане на детайли
        $order_query = "SELECT o.*, u.email, u.username 
                        FROM orders o 
                        JOIN users u ON o.user_id = u.id 
                        WHERE o.id = $order_id";
        $order_result = mysqli_query($conn, $order_query);
        $order = mysqli_fetch_assoc($order_result);
        
        // Получател
        $mail->addAddress($order['email'], $order['username']);
        
        // Статуси на български
        $status_labels = array(
            'pending' => 'Обработва се',
            'confirmed' => 'Потвърдена',
            'shipped' => 'Изпратена',
            'delivered' => 'Доставена',
            'cancelled' => 'Отказана'
        );
        
        $status_text = $status_labels[$new_status] ?? $new_status;
        
        // Тема
        $mail->Subject = "Статус на поръчка #$order_id: $status_text";
        
        // HTML съдържание
        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #F5EFE7; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #8B6F47 0%, #5D4E37 100%); color: #FDFBF7; padding: 40px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 40px; background: #FDFBF7; border-radius: 0 0 8px 8px; }
                .status-badge { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #6B8E4E 0%, #527A3A 100%); color: white; border-radius: 25px; margin: 20px 0; font-weight: bold; font-size: 1.1rem; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1 style='margin: 0; font-weight: 700; letter-spacing: 2px;'>МЕБЕЛИ ОНЛАЙН</h1>
                </div>
                <div class='content'>
                    <h2 style='color: #5D4E37;'>Актуализация на поръчка #$order_id</h2>
                    <p style='color: #8B6F47;'>Здравейте, <strong>{$order['username']}</strong>,</p>
                    <p style='color: #8B6F47;'>Статусът на вашата поръчка беше променен:</p>
                    <div class='status-badge'>$status_text</div>
                    <p style='color: #8B6F47;'><strong>Обща сума:</strong> <span style='color: #C17C4A; font-size: 1.2rem;'>€" . number_format($order['total_price'], 2) . "</span></p>
                    <p style='color: #8B6F47;'><strong>Адрес за доставка:</strong><br>{$order['delivery_address']}</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->isHTML(true);
        $mail->Body = $htmlBody;
        $mail->AltBody = "Поръчка #$order_id: Нов статус - $status_text";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
