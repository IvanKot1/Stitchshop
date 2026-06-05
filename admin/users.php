<?php
$pageTitle = 'Управление пользователями';
require_once __DIR__ . '/../includes/functions.php';

// Проверка администратора
if (!isAdmin()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$success = '';
$error = '';

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $userId = (int)$_POST['user_id'];
        
        // Нельзя удалить себя
        if ($userId == $_SESSION['user_id']) {
            $error = 'Нельзя удалить собственный аккаунт';
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $success = 'Пользователь удалён';
        }
    }
    
    if (isset($_POST['toggle_admin'])) {
        $userId = (int)$_POST['user_id'];
        $stmt = $conn->prepare("UPDATE users SET is_admin = NOT is_admin WHERE id = ?");
        $stmt->execute([$userId]);
        $success = 'Права доступа изменены';
    }
}

// Получаем всех пользователей
$stmt = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="breadcrumbs">
    <div class="container">
        <ul>
            <li><a href="<?= SITE_URL ?>/index.php">Главная</a></li>
            <li><a href="<?= SITE_URL ?>/admin/index.php">Админ-панель</a></li>
            <li>Пользователи</li>
        </ul>
    </div>
</div>

<section class="section">
    <div class="container">
        <h2 class="section-title">👥 Управление пользователями</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="cart-section">
            <h3 style="margin-bottom: 20px;">Все пользователи (<?= count($users) ?>)</h3>
            
            <?php if (count($users) > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f0f0f0;">
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">ID</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Имя</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Email</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Роль</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Дата регистрации</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 15px;"><?= $user['id'] ?></td>
                                    <td style="padding: 15px;"><?= htmlspecialchars($user['name']) ?></td>
                                    <td style="padding: 15px;"><?= htmlspecialchars($user['email']) ?></td>
                                    <td style="padding: 15px;">
                                        <?php if ($user['is_admin']): ?>
                                            <span style="background: #667eea; color: white; padding: 5px 10px; border-radius: 5px; font-size: 12px;">
                                                Администратор
                                            </span>
                                        <?php else: ?>
                                            <span style="background: #e0e0e0; color: #333; padding: 5px 10px; border-radius: 5px; font-size: 12px;">
                                                Пользователь
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 15px;"><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                                    <td style="padding: 15px;">
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <button type="submit" name="toggle_admin" class="filter-btn" style="padding: 8px 15px; font-size: 14px;">
                                                    <?= $user['is_admin'] ? '👤' : '👑' ?>
                                                </button>
                                            </form>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Удалить пользователя?');">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <button type="submit" name="delete_user" class="filter-btn filter-btn-reset" style="padding: 8px 15px; font-size: 14px;">
                                                    🗑️
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: #999; font-size: 14px;">Вы</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 40px;">Пользователей пока нет</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
