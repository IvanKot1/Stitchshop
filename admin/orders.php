<?php
$pageTitle = 'Управление заказами';
require_once __DIR__ . '/../includes/functions.php';

// Проверка администратора
if (!isAdmin()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$success = '';
$error = '';

// Обработка изменения статуса заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'];
    
    $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (in_array($status, $validStatuses)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
        $success = 'Статус заказа обновлён';
    }
}

// Получаем все заказы
$stmt = $conn->query("
    SELECT o.*, u.name as user_name, u.email as user_email 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="breadcrumbs">
    <div class="container">
        <ul>
            <li><a href="<?= SITE_URL ?>/index.php">Главная</a></li>
            <li><a href="<?= SITE_URL ?>/admin/index.php">Админ-панель</a></li>
            <li>Заказы</li>
        </ul>
    </div>
</div>

<section class="section">
    <div class="container">
        <h2 class="section-title">🛒 Управление заказами</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="cart-section">
            <h3 style="margin-bottom: 20px;">Все заказы (<?= count($orders) ?>)</h3>
            
            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                    <div style="background: #f9f9f9; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div>
                                <h4 style="margin: 0;">Заказ #<?= $order['id'] ?></h4>
                                <p style="margin: 5px 0; color: #666; font-size: 14px;">
                                    <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                                </p>
                            </div>
                            
                            <div style="color: #667eea; font-size: 20px; font-weight: bold;">
                                <?= number_format($order['total_amount'], 2, ',', ' ') ?> ₽
                            </p>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 15px;">
                            <div>
                                <strong>Пользователь:</strong><br>
                                <?= htmlspecialchars($order['user_name'] ?? 'Гость') ?><br>
                                <span style="color: #666; font-size: 14px;"><?= htmlspecialchars($order['user_email'] ?? '') ?></span>
                            </div>
                            <div>
                                <strong>Адрес доставки:</strong><br>
                                <?= nl2br(htmlspecialchars($order['address'])) ?>
                            </div>
                        </div>
                        
                        <div style="border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 15px 0; margin-bottom: 15px;">
                            <strong>Товары:</strong>
                            <ul style="margin-top: 10px; margin-left: 20px;">
                                <?php
                                $orderItems = getOrderItems($order['id']);
                                foreach ($orderItems as $item):
                                ?>
                                    <li style="margin-bottom: 5px;">
                                        <?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?> 
                                        = <?= number_format($item['price'] * $item['quantity'], 2, ',', ' ') ?> ₽
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <label for="status_<?= $order['id'] ?>" style="margin-right: 10px;">Статус:</label>
                                <select name="status" id="status_<?= $order['id'] ?>" style="padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                                    <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Ожидает</option>
                                    <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Обрабатывается</option>
                                    <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Отправлен</option>
                                    <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Доставлен</option>
                                    <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Отменён</option>
                                </select>
                                <button type="submit" name="update_status" class="filter-btn" style="padding: 8px 15px; margin-left: 10px;">
                                    Обновить
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 60px 20px;">Заказов пока нет</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
