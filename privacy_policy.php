<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Политика за поверителност - Мебели Онлайн</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .policy-container {
            padding: 3rem 0;
            background-color: #F5EFE7;
            min-height: 70vh;
        }
        
        .policy-content {
            max-width: 900px;
            margin: 0 auto;
            background: #FDFBF7;
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(139, 111, 71, 0.1);
        }
        
        .policy-content h1 {
            color: #5D4E37;
            margin-bottom: 1rem;
            font-size: 2.5rem;
        }
        
        .policy-content h2 {
            color: #8B6F47;
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.5rem;
            border-bottom: 2px solid #D4A574;
            padding-bottom: 0.5rem;
        }
        
        .policy-content h3 {
            color: #8B6F47;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }
        
        .policy-content p {
            color: #5D4E37;
            line-height: 1.8;
            margin-bottom: 1rem;
        }
        
        .policy-content ul, .policy-content ol {
            color: #5D4E37;
            line-height: 1.8;
            margin-left: 2rem;
            margin-bottom: 1rem;
        }
        
        .last-updated {
            color: #8B6F47;
            font-style: italic;
            margin-bottom: 2rem;
        }
        
        .important-notice {
            background: linear-gradient(135deg, #FFF9E6 0%, #F5EFE7 100%);
            border-left: 4px solid #C17C4A;
            padding: 1.5rem;
            margin: 2rem 0;
            border-radius: 4px;
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
                    <?php if (isLoggedIn()): ?>
                        <li><a href="logout.php">ИЗХОД</a></li>
                    <?php else: ?>
                        <li><a href="login.php">ВХОД</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="policy-container">
        <div class="container">
            <div class="policy-content">
                <h1>Политика за поверителност</h1>
                <p class="last-updated">Последна актуализация: 10 Март 2026</p>

                <div class="important-notice">
                    <strong>Важно:</strong> Тази политика описва как Мебели Онлайн събира, използва и защитава вашата лична информация. Използвайки нашия сайт, вие се съгласявате с условията, описани в този документ.
                </div>

                <h2>1. Обща информация</h2>
                <p>
                    Мебели Онлайн ("ние", "нас", "наш") зачита вашата поверителност и се ангажира да защитава личните ви данни. 
                    Тази политика за поверителност ви информира как се грижим за вашите лични данни когато посещавате нашия уебсайт 
                    и ви информира за вашите права относно поверителността.
                </p>

                <h2>2. Какви данни събираме</h2>
                
                <h3>2.1. Данни, които ни предоставяте директно:</h3>
                <ul>
                    <li><strong>При регистрация:</strong> име, потребителско име, имейл адрес, парола (хеширана)</li>
                    <li><strong>При поръчка:</strong> адрес за доставка, телефонен номер, бележки към поръчката</li>
                    <li><strong>При запитване за мебели по поръчка:</strong> име, имейл, телефон, описание на желаната мебел</li>
                </ul>

                <h3>2.2. Данни, които събираме автоматично:</h3>
                <ul>
                    <li>IP адрес и данни за браузъра (за сигурност)</li>
                    <li>История на поръчките</li>
                    <li>Информация за сесията (чрез cookies)</li>
                </ul>

                <h2>3. Как използваме вашите данни</h2>
                <p>Използваме събраните данни за следните цели:</p>
                <ul>
                    <li><strong>Обработка на поръчки:</strong> за изпълнение на вашите поръчки и доставка на продукти</li>
                    <li><strong>Комуникация:</strong> за изпращане на потвърждения за поръчки и актуализации на статуса</li>
                    <li><strong>Подобряване на услугите:</strong> за анализ и подобряване на нашия сайт и услуги</li>
                    <li><strong>Сигурност:</strong> за предотвратяване на измами и злоупотреби</li>
                    <li><strong>Персонализация:</strong> за показване на подходящо съдържание</li>
                </ul>

                <h2>4. Споделяне на данни с трети страни</h2>
                <p>Ние споделяме вашите данни само когато е необходимо:</p>
                <ul>
                    <li><strong>Платежни процесори:</strong> Stripe за обработка на плащания с карта</li>
                    <li><strong>Email доставчици:</strong> Gmail SMTP за изпращане на нотификации</li>
                    <li><strong>Куриерски услуги:</strong> за доставка на поръчките (само адрес и телефон)</li>
                </ul>
                <p><strong>Важно:</strong> Ние НИКОГА не продаваме вашите лични данни на трети страни за маркетинг цели.</p>

                <h2>5. Сигурност на данните</h2>
                <p>Ние предприемаме подходящи технически и организационни мерки за защита на вашите данни:</p>
                <ul>
                    <li>Паролите се съхраняват хеширани (password_hash)</li>
                    <li>HTTPS криптиране на данните при предаване</li>
                    <li>Ограничен достъп до личните данни само за упълномощен персонал</li>
                    <li>Редовни актуализации на системата за сигурност</li>
                </ul>

                <h2>6. Вашите права (GDPR)</h2>
                <p>Съгласно GDPR, вие имате следните права относно вашите лични данни:</p>
                <ul>
                    <li><strong>Право на достъп:</strong> можете да поискате копие от данните, които съхраняваме за вас</li>
                    <li><strong>Право на коригиране:</strong> можете да коригирате неточни или непълни данни</li>
                    <li><strong>Право на изтриване:</strong> можете да поискате изтриване на вашите данни ("право да бъдеш забравен")</li>
                    <li><strong>Право на ограничаване:</strong> можете да ограничите обработката на вашите данни</li>
                    <li><strong>Право на преносимост:</strong> можете да получите данните си в структуриран формат</li>
                    <li><strong>Право на възражение:</strong> можете да възразите срещу обработката на вашите данни</li>
                </ul>

                <h2>7. Съхранение на данни</h2>
                <p>
                    Ние съхраняваме вашите лични данни толкова дълго, колкото е необходимо за целите, описани в тази политика:
                </p>
                <ul>
                    <li><strong>Акаунтни данни:</strong> докато акаунтът ви е активен</li>
                    <li><strong>История на поръчки:</strong> 5 години (за счетоводни и законови цели)</li>
                    <li><strong>Marketing данни:</strong> до оттегляне на съгласие</li>
                </ul>

                <h2>8. Cookies</h2>
                <p>Използваме cookies за:</p>
                <ul>
                    <li><strong>Сесия:</strong> за поддържане на вход в системата</li>
                    <li><strong>Количка:</strong> за запазване на продукти в количката</li>
                    <li><strong>Предпочитания:</strong> за запомняне на вашите настройки</li>
                </ul>
                <p>Можете да управлявате cookies чрез настройките на вашия браузър.</p>

                <h2>9. Деца</h2>
                <p>
                    Нашите услуги не са предназначени за лица под 16 години. Ние не събираме съзнателно лични данни от деца. 
                    Ако сте родител и смятате, че детето ви ни е предоставило лични данни, моля свържете се с нас.
                </p>

                <h2>10. Промени в политиката</h2>
                <p>
                    Можем да актуализираме тази политика за поверителност периодично. Ще ви уведомим за съществени промени 
                    чрез имейл или чрез известие на сайта. Последната дата на актуализация е посочена в началото на документа.
                </p>

                <h2>11. Контакти</h2>
                <p>За въпроси относно тази политика за поверителност или за упражняване на вашите права, моля свържете се с нас:</p>
                <ul>
                    <li><strong>Email:</strong> privacy@mebeli-online.bg</li>
                    <li><strong>Телефон:</strong> +359 888 123 456</li>
                    <li><strong>Адрес:</strong> гр. Варна, България</li>
                </ul>

                <div style="margin-top: 3rem; text-align: center;">
                    <a href="register.php" class="btn btn-primary">Регистрирай се</a>
                    <a href="index.php" class="btn btn-secondary" style="margin-left: 1rem;">Към начало</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Футър -->
    <footer class="footer">
        <div class="container">
            <p style="text-align: center;">
                &copy; 2024 Мебели Онлайн. Всички права запазени. | 
                <a href="privacy_policy.php" style="color: #D4A574;">Политика за поверителност</a> | 
                <a href="terms_of_service.php" style="color: #D4A574;">Условия за ползване</a>
            </p>
        </div>
    </footer>
</body>
</html>
