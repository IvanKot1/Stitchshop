<?php
$pageTitle = 'Заказ оформлен';
require_once __DIR__ . '/includes/functions.php';

// Проверка авторизации
if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

// Получаем ID заказа
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($orderId <= 0) {
    header('Location: ' . SITE_URL . '/profile.php');
    exit;
}

// Получаем заказ
global $conn;
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: ' . SITE_URL . '/profile.php');
    exit;
}

// Получаем товары заказа
$orderItems = getOrderItems($orderId);
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<!-- Хлебные крошки -->
<div class="breadcrumbs">
    <div class="container">
        <ul>
            <li><a href="<?= SITE_URL ?>/index.php">Главная</a></li>
            <li><a href="<?= SITE_URL ?>/profile.php">Кабинет</a></li>
            <li>Заказ #<?= $orderId ?></li>
        </ul>
    </div>
</div>

<section class="section">
    <div class="container">
        <div style="text-align: center; margin-bottom: 40px;">
            <div style="font-size: 60px; margin-bottom: 20px;">✅</div>
            <h1 style="color: #28a745; margin-bottom: 15px;">Заказ успешно оформлен!</h1>
            <p style="font-size: 18px; color: #666;">
                Номер вашего заказа: <strong>#<?= $orderId ?></strong>
            </p>
            <p style="color: #666; margin-top: 10px;">
                Мы отправили подтверждение на Email: <strong><?= htmlspecialchars($order['email']) ?></strong>
            </p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 400px; gap: 30px;">
            <!-- Информация о заказе -->
            <div class="cart-section">
                <h3 style="margin-bottom: 20px;">Информация о доставке</h3>
                
                <div style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                    <p><strong>ФИО:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    <p><strong>Адрес:</strong> <?= nl2br(htmlspecialchars($order['address'])) ?></p>
                    <p><strong>Дата заказа:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                    <p><strong>Статус:</strong> 
                        <span style="color: #ffc107; font-weight: bold;">
                            <?= $order['status'] === 'pending' ? 'Ожидает обработки' : ucfirst($order['status']) ?>
                        </span>
                    </p>
                </div>
                
                <h3 style="margin-bottom: 20px;">Товары в заказе</h3>
                
                <?php foreach ($orderItems as $item): ?>
                    <div class="cart-item" style="padding: 15px 0; border-bottom: 1px solid #eee;">
                        <div class="cart-item-image" style="width: 80px; height: 80px;">
                            <img src="<?= SITE_URL ?>/uploads/products/<?= htmlspecialchars($item['product_image']) ?>" 
                                 alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover;"
                                 onerror="this.parentElement.innerHTML='Фото'">
                        </div>
                        
                        <div style="flex: 1; margin-left: 15px;">
                            <div style="font-weight: bold;"><?= htmlspecialchars($item['product_name']) ?></div>
                            <div style="font-size: 14px; color: #666;">
                                <?= $item['quantity'] ?> шт. × <?= number_format($item['price'], 2, ',', ' ') ?> ₽
                            </div>
                        </div>
                        
                        <div style="color: #667eea; font-weight: bold;">
                            <?= number_format($item['price'] * $item['quantity'], 2, ',', ' ') ?> ₽
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #eee; text-align: right;">
                    <p style="font-size: 20px; font-weight: bold;">
                        Итого: <span style="color: #667eea;"><?= number_format($order['total_amount'], 2, ',', ' ') ?> ₽</span>
                    </p>
                    <p style="color: #666; margin-top: 10px;">
                        Способ оплаты: <strong>Оплата при получении</strong>
                    </p>
                </div>
            </div>
            
            <!-- Сайдбар -->
            <div>
                <div class="cart-section">
                    <h3 style="margin-bottom: 20px;">Что дальше?</h3>
                    
                    <div style="margin-bottom: 20px;">
                        <div style="font-size: 24px; margin-bottom: 10px;">📧</div>
                        <p>Мы отправили подтверждение заказа на ваш Email</p>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <div style="font-size: 24px; margin-bottom: 10px;">📦</div>
                        <p>Вы сможете отследить статус заказа в личном кабинете</p>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <div style="font-size: 24px; margin-bottom: 10px;">🚚</div>
                        <p>После отправки заказа вы получите уведомление</p>
                    </div>
                    
                    <a href="<?= SITE_URL ?>/profile.php" class="filter-btn" style="width: 100%; display: block; text-align: center; margin-bottom: 10px;">
                        В личный кабинет
                    </a>
                    <a href="<?= SITE_URL ?>/catalog.php" class="filter-btn" style="width: 100%; display: block; text-align: center; background: #999;">
                        Продолжить покупки
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php ?>
