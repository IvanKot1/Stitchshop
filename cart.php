<?php
$pageTitle = 'Корзина';
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'])) {
        $productId = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        updateCartQuantity($productId, $quantity);
    }

    if (isset($_POST['remove_item'])) {
        $productId = (int)$_POST['product_id'];
        removeFromCart($productId);
    }
}

$cartItems = getCartContents();
$cartTotal = getCartTotal();
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<section class="section cart-page">
    <div class="container">
        <?php if (count($cartItems) > 0): ?>
            <div class="cart-section">
                <?php foreach ($cartItems as $item): ?>
                    <article class="cart-item">
                        <div class="cart-item-image">
                            <img src="<?= SITE_URL ?>/uploads/products/<?= htmlspecialchars($item['image']) ?>"
                                 alt="<?= htmlspecialchars($item['name']) ?>"
                                 onerror="this.parentElement.innerHTML='Фото'">
                        </div>

                        <div class="cart-item-info">
                            <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="cart-item-color">Цвет синий</div>
                        </div>

                        <div class="cart-item-controls">
                            <div class="quantity-control">
                                <form method="POST" class="quantity-form">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <button type="button" class="quantity-btn" onclick="this.parentElement.querySelector('input[name=quantity]').value--; this.parentElement.submit()">-</button>
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="100" readonly>
                                    <button type="button" class="quantity-btn" onclick="this.parentElement.querySelector('input[name=quantity]').value++; this.parentElement.submit()">+</button>
                                    <input type="hidden" name="update_quantity" value="1">
                                </form>
                            </div>
                            <div class="cart-item-price"><?= number_format($item['price'], 0, ',', ' ') ?> руб</div>
                        </div>

                        <form method="POST" class="cart-remove-form">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <button type="submit" name="remove_item" class="cart-item-remove" aria-label="Удалить">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </button>
                        </form>
                    </article>
                <?php endforeach; ?>

                <div class="cart-summary">
                    <div class="cart-summary-row">
                        <span>Итоговая сумма</span>
                        <strong><?= number_format($cartTotal, 0, ',', ' ') ?> руб</strong>
                    </div>
                    <a href="<?= SITE_URL ?>/checkout.php" class="cart-btn cart-checkout-btn">Оформить заказ</a>
                </div>
            </div>
        <?php else: ?>
            <div class="catalog-empty">
                <p>Ваша корзина пуста</p>
                <a href="<?= SITE_URL ?>/catalog.php" class="filter-btn">Перейти в каталог</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>