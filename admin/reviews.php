<?php
$pageTitle = 'Управление отзывами';
require_once __DIR__ . '/../includes/functions.php';

// Проверка администратора
if (!isAdmin()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

// Удаление отзыва
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $reviewId = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->execute([$reviewId]);
    header('Location: ' . SITE_URL . '/admin/reviews.php?deleted=1');
    exit;
}

// Получаем все отзывы
$stmt = $conn->query("
    SELECT r.*, u.name as user_name, u.email as user_email, p.name as product_name 
    FROM reviews r 
    LEFT JOIN users u ON r.user_id = u.id 
    LEFT JOIN products p ON r.product_id = p.id 
    ORDER BY r.created_at DESC
");
$reviews = $stmt->fetchAll();
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<!-- Хлебные крошки -->
<div class="breadcrumbs">
    <div class="container">
        <ul>
            <li><a href="<?= SITE_URL ?>/index.php">Главная</a></li>
            <li><a href="<?= SITE_URL ?>/admin/index.php">Админ-панель</a></li>
            <li>Отзывы</li>
        </ul>
    </div>
</div>

<section class="section">
    <div class="container">
        <h2 class="section-title"> Управление отзывами</h2>
        
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                Отзыв успешно удалён!
            </div>
        <?php endif; ?>
        
        <div class="cart-section">
            <?php if (count($reviews) > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f0f0f0;">
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">ID</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Товар</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Пользователь</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Оценка</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Комментарий</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Дата</th>
                                <th style="padding: 15px; text-align: center; border-bottom: 2px solid #ddd;">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 15px;">#<?= $review['id'] ?></td>
                                    <td style="padding: 15px;"><?= htmlspecialchars($review['product_name'] ?? 'Товар удалён') ?></td>
                                    <td style="padding: 15px;">
                                        <?= htmlspecialchars($review['user_name'] ?? 'Аноним') ?>
                                        <br>
                                        <small style="color: #666;"><?= htmlspecialchars($review['user_email'] ?? '') ?></small>
                                    </td>
                                    <td style="padding: 15px;">
                                        <span style="color: #ffc107; font-size: 18px;">
                                            <?= str_repeat('⭐', $review['rating']) ?>
                                        </span>
                                        <span style="color: #666; font-size: 14px;">(<?= $review['rating'] ?>)</span>
                                    </td>
                                    <td style="padding: 15px; max-width: 300px;">
                                        <?= htmlspecialchars(mb_substr($review['comment'], 0, 100)) ?><?= mb_strlen($review['comment']) > 100 ? '...' : '' ?>
                                    </td>
                                    <td style="padding: 15px;"><?= date('d.m.Y H:i', strtotime($review['created_at'])) ?></td>
                                    <td style="padding: 15px; text-align: center;">
                                        <a href="<?= SITE_URL ?>/admin/reviews.php?delete=1&id=<?= $review['id'] ?>" 
                                           class="cancel-order-btn" 
                                           style="display: inline-block; padding: 8px 15px; font-size: 14px;"
                                           onclick="return confirm('Вы уверены, что хотите удалить этот отзыв?')">
                                            Удалить
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #2f3137; padding: 40px;">Отзывов пока нет</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
