<?php
$pageTitle = 'Админ-панель';
require_once __DIR__ . '/../includes/functions.php';

// Проверка администратора
if (!isAdmin()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

// Получаем статистику
$stats = [
    'users' => $conn->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'products' => $conn->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'orders' => $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'revenue' => $conn->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled'")->fetchColumn() ?? 0
];

// Получаем последние заказы
$stmt = $conn->query("SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 10");
$recentOrders = $stmt->fetchAll();
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<!-- Хлебные крошки -->
<div class="breadcrumbs">
    <div class="container">
        <ul>
            <li><a href="<?= SITE_URL ?>/index.php">Главная</a></li>
            <li>Админ-панель</li>
        </ul>
    </div>
</div>

<section class="section">
    <div class="container">
        <h2 class="section-title"> Административная панель</h2>
        
        <!-- Статистика -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <div style="background: #0d4f97; color: white; padding: 30px; border-radius: 12px;">
                <h3 style="font-size: 36px; margin-bottom: 10px;"><?= $stats['users'] ?></h3>
                <p style="font-size: 18px;">Пользователей</p>
            </div>
            
            <div style="background: #0d4f97; color: white; padding: 30px; border-radius: 12px;">
                <h3 style="font-size: 36px; margin-bottom: 10px;"><?= $stats['products'] ?></h3>
                <p style="font-size: 18px;">Товаров</p>
            </div>
            
            <div style="background: #0d4f97;  padding: 30px; border-radius: 12px;">
                <h3 style="font-size: 36px; margin-bottom: 10px;"><?= $stats['orders'] ?></h3>
                <p style="font-size: 18px;">Заказов</p>
            </div>
            
            <div style="background: #0d4f97;  padding: 30px; border-radius: 12px;">
                <h3 style="font-size: 36px; margin-bottom: 10px;"><?= number_format($stats['revenue'], 0, ',', ' ') ?> ₽</h3>
                <p style="font-size: 18px;">Выручка</p>
            </div>
        </div>
        
        <!-- Меню администратора -->
        <div style="height:60px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <a href="<?= SITE_URL ?>/admin/products.php" class="admin-menu-btn" style="text-align: center; padding: 30px; font-size: 18px;">
                 Управление товарами
            </a>
            <a href="<?= SITE_URL ?>/admin/reviews.php" class="admin-menu-btn" style="text-align: center; padding: 30px; font-size: 18px;">
                 Отзывы
            </a>
            <a href="<?= SITE_URL ?>/admin/users.php" class="admin-menu-btn" style="text-align: center; padding: 30px; font-size: 18px;">
                 Пользователи
            </a>
            <a href="<?= SITE_URL ?>/admin/orders.php" class="admin-menu-btn" style="text-align: center; padding: 30px; font-size: 18px;">
                 Заказы
            </a>
        </div>
        
        <!-- Последние заказы -->
        <div class="cart-section">
            <h3 style="margin-bottom: 20px;">Последние заказы</h3>
            
            <?php if (count($recentOrders) > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f0f0f0;">
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">ID</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Пользователь</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Сумма</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Статус</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Дата</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 15px;">#<?= $order['id'] ?></td>
                                    <td style="padding: 15px;"><?= htmlspecialchars($order['user_name'] ?? 'Гость') ?></td>
                                    <td style="padding: 15px; color: #667eea; font-weight: bold;">
                                        <?= number_format($order['total_amount'], 2, ',', ' ') ?> ₽
                                    </td>
                                    <td style="padding: 15px;">
                                        <?php
                                        $statusColors = [
                                            'pending' => '#ffc107',
                                            'processing' => '#17a2b8',
                                            'shipped' => '#007bff',
                                            'delivered' => '#28a745',
                                            'cancelled' => '#dc3545'
                                        ];
                                        ?>
                                        <span style="color: <?= $statusColors[$order['status']] ?? '#333' ?>; font-weight: bold;">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                    </td>
                                    <td style="padding: 15px;"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 40px;">Заказов пока нет</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
