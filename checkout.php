<?php
$pageTitle = 'Оформление заказа';
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$cartItems = getCartContents();
if (count($cartItems) === 0) {
    header('Location: ' . SITE_URL . '/cart.php');
    exit;
}

$user = getCurrentUser();
$cartTotal = getCartTotal();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    if (empty($fullName)) {
        $errors[] = 'Введите ФИО';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный Email';
    }

    if (empty($address)) {
        $errors[] = 'Введите адрес доставки';
    }

    if (empty($errors)) {
        $orderId = createOrder($user['id'], $address, $fullName, $email);
        header('Location: ' . SITE_URL . '/order-success.php?id=' . $orderId);
        exit;
    }
}
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<section class="section checkout-page">
    <div class="container">
        <h2 class="section-title" style="margin-bottom: 24px;">Оформление заказа</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" style="margin-bottom: 20px;">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="checkout-wrapper-v2">
            <!-- Карточка товара -->
            <div class="checkout-product-card">
                <?php foreach ($cartItems as $item): ?>
                <div class="checkout-product-item">
                    <div class="checkout-product-image">
                        <img src="<?= SITE_URL ?>/uploads/products/<?= htmlspecialchars($item['image']) ?>"
                             alt="<?= htmlspecialchars($item['name']) ?>"
                             onerror="this.parentElement.innerHTML='Фото'">
                    </div>
                    <div class="checkout-product-details">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p class="product-color">Цвет: Синий</p>
                        <p class="product-delivery">Придет 13 мая</p>
                    </div>
                    <div class="checkout-product-actions">
                        <div class="quantity-selector">
                            <button type="button" class="qty-btn" onclick="updateQuantity(<?= $item['id'] ?>, -1)">+</button>
                            <span class="qty-value"><?= $item['quantity'] ?></span>
                            <button type="button" class="qty-btn" onclick="updateQuantity(<?= $item['id'] ?>, 1)">−</button>
                        </div>
                        <div class="product-price"><?= number_format($item['price'] * $item['quantity'], 0, ',', ' ') ?> руб</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Форма доставки -->
            <div class="checkout-delivery-card">
                <h3>Данные для доставки</h3>

                <form method="POST" class="checkout-form-v2">
                    <div class="form-row">
                        <label for="full_name">ФИО</label>
                        <input type="text" name="full_name" id="full_name" value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : htmlspecialchars($user['name']) ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="email">Почта</label>
                        <input type="email" name="email" id="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="address">Адрес доставки</label>
                        <input type="text" name="address" id="address" value="<?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?>" required>
                    </div>

                    <div class="form-row">
                        <label>Способ оплаты</label>
                        <div class="payment-method">
                            <input type="radio" name="payment" id="payment_cash" value="cash" checked hidden>
                            <label for="payment_cash" class="payment-label">
                                <span class="radio-indicator"></span>
                                <span class="payment-text">Оплата при получении</span>
                            </label>
                        </div>
                    </div>

                    <div class="checkout-summary-row-v2">
                        <span class="summary-label">Итог</span>
                        <span class="summary-value"><?= number_format($cartTotal, 0, ',', ' ') ?> руб</span>
                    </div>

                    <button type="submit" name="place_order" class="checkout-order-btn">Заказать</button>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
function updateQuantity(productId, change) {
    // Здесь можно добавить AJAX-запрос для обновления количества
    console.log('Update quantity:', productId, change);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>