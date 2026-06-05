<?php
$pageTitle = 'Личный кабинет';
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$user = getCurrentUser();

if (isset($_GET['logout'])) {
    logout();
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$orders = getUserOrders($user['id']);
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'orders';
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<section class="section profile-page">
    <div class="container">
        <h2 class="section-title" style="text-align: center; margin-bottom: 30px;">Личный кабинет</h2>

        <div class="profile-section">
            <aside class="profile-sidebar">
                <div class="profile-info">
                    <div class="profile-avatar">
                        <svg viewBox="0 0 24 24" width="60" height="60" fill="#888">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 20c2.1-3.6 5-5 8-5s5.9 1.4 8 5"/>
                        </svg>
                    </div>
                    <div class="profile-name"><?= htmlspecialchars($user['name']) ?></div>
                    <div class="profile-email"><?= htmlspecialchars($user['email']) ?></div>
                </div>

                <ul class="sidebar-menu">
                    <li>
                        <a href="<?= SITE_URL ?>/profile.php?tab=orders" class="<?= $activeTab == 'orders' ? 'active' : '' ?>">
                            <img src="img/box.png" alt="" class="ico">
                            Мои заказы
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/profile.php?tab=profile" class="<?= $activeTab == 'profile' ? 'active' : '' ?>">
                            <img src="img/profil.png" alt="" class="ico">
                            Профиль
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/profile.php?tab=settings" class="<?= $activeTab == 'settings' ? 'active' : '' ?>">
                            <img src="img/seting.png" alt="" class="ico1">
                            Настройки
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/profile.php?logout=1">
                            <img src="img/exit.png" alt="" class="ico">
                            Выход
                        </a>
                    </li>
                </ul>
            </aside>

            <div class="profile-content">
                <?php if ($activeTab == 'orders'): ?>
                    <h3>Мои заказы</h3>

                    <?php if (count($orders) > 0): ?>
                        <div class="orders-list">
                            <?php foreach ($orders as $order): ?>
                                <?php
                                $statusText = [
                                    'pending' => 'Ожидает обработки',
                                    'processing' => 'Обрабатывается',
                                    'shipped' => 'Отправлен',
                                    'delivered' => 'Доставлен',
                                    'cancelled' => 'Отменён'
                                ];
                                ?>
                                <article class="order-card">
                                    <div class="order-head">
                                        <h4>Заказ #<?= $order['id'] ?></h4>
                                        <strong><?= number_format($order['total_amount'], 0, ',', ' ') ?> руб</strong>
                                    </div>

                                    <div class="order-meta">
                                        <p><span>Дата:</span> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                                        <p><span>Статус:</span> <?= $statusText[$order['status']] ?? $order['status'] ?></p>
                                        <p><span>Адрес:</span> <?= htmlspecialchars($order['address']) ?></p>
                                    </div>

                                    <ul class="order-items">
                                        <?php foreach (getOrderItems($order['id']) as $item): ?>
                                            <li>
                                                <?= htmlspecialchars($item['product_name']) ?>
                                                × <?= $item['quantity'] ?>
                                                = <?= number_format($item['price'] * $item['quantity'], 0, ',', ' ') ?> руб
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>

                                    <div class="order-actions">
                                        <?php if ($order['status'] == 'pending'): ?>
                                            <form method="POST" action="<?= SITE_URL ?>/includes/cancel-order.php" style="display: inline;">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <button type="submit" class="cancel-order-btn" onclick="return confirm('Вы уверены, что хотите отменить заказ?')">Отменить заказ</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="profile-empty">
                            <p>У вас пока нет заказов</p>
                            <a href="<?= SITE_URL ?>/catalog.php" class="filter-btn">Перейти в каталог</a>
                        </div>
                    <?php endif; ?>

                <?php elseif ($activeTab == 'profile'): ?>
                    <h3>Профиль</h3>
                    <div class="profile-card">
                        <div class="profile-field">
                            <label>Имя:</label>
                            <p><?= htmlspecialchars($user['name']) ?></p>
                        </div>
                        <div class="profile-field">
                            <label>Почта:</label>
                            <p><?= htmlspecialchars($user['email']) ?></p>
                        </div>
                        <div class="profile-field">
                            <label>Статус:</label>
                            <p><?= $user['is_admin'] ? 'Администратор' : 'Пользователь' ?></p>
                        </div>
                    </div>

                <?php elseif ($activeTab == 'settings'): ?>
                    <h3>Настройки</h3>
                    
                    <?php if (isset($_GET['updated'])): ?>
                        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                            Данные успешно обновлены!
                        </div>
                    <?php endif; ?>
                    
                    <div class="profile-card">
                        <h4>Редактирование профиля</h4>
                        <form method="POST" action="<?= SITE_URL ?>/includes/update-profile.php">
                            <div class="form-group">
                                <label for="name">Имя</label>
                                <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Почта</label>
                                <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="current_password">Текущий пароль (для подтверждения)</label>
                                <input type="password" name="current_password" id="current_password">
                                <small style="color: #5a5a5a; font-size: 14px;">Оставьте пустым, если не меняете пароль</small>
                            </div>

                            <div class="form-group">
                                <label for="new_password">Новый пароль</label>
                                <input type="password" name="new_password" id="new_password">
                            </div>

                            <div class="form-group">
                                <label for="new_password_confirm">Повторите новый пароль</label>
                                <input type="password" name="new_password_confirm" id="new_password_confirm">
                            </div>

                            <button type="submit" class="filter-btn">Сохранить изменения</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>