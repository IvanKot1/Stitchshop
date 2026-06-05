<?php
$pageTitle = 'Главная';
require_once __DIR__ . '/includes/functions.php';

// Новинки: только 4 карточки
$stmt = $conn->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 4");
$newProducts = $stmt->fetchAll();

// Последние отзывы для главной
$reviewsStmt = $conn->query("
    SELECT r.comment, u.name AS user_name
    FROM reviews r
    LEFT JOIN users u ON u.id = r.user_id
    ORDER BY r.created_at DESC
    LIMIT 3
");
$homeReviews = $reviewsStmt->fetchAll();
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<section class="banner">
    <div class="container">
        <a href="<?= SITE_URL ?>/catalog.php" class="banner-link" aria-label="Перейти в каталог">
            <img src="<?= SITE_URL ?>/img/baner.png" alt="Баннер <?= SITE_NAME ?>" class="banner-image banner-desktop">
            <img src="<?= SITE_URL ?>/img/adapt.png" alt="Баннер <?= SITE_NAME ?>" class="banner-image banner-mobile">
        </a>
    </div>
</section>

<section class="section home-new">
    <div class="container">
        <h2 class="section-title">Новинки</h2>

        <?php if (count($newProducts) > 0): ?>
            <div class="products-grid">
                <?php foreach ($newProducts as $product): ?>
                    <a href="<?= SITE_URL ?>/product.php?id=<?= $product['id'] ?>" class="product-card product-card-link">
                        <div class="product-image">
                            <img src="<?= SITE_URL ?>/uploads/products/<?= htmlspecialchars($product['image']) ?>"
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 style="width: 100%; height: 100%; object-fit: cover;"
                                 onerror="this.parentElement.innerHTML='Фото товара'">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="product-price"><?= number_format($product['price'], 0, ',', ' ') ?> ₽</p>
                            <div class="product-btn">В корзину</div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="empty-state">Товары скоро появятся в продаже.</p>
        <?php endif; ?>
    </div>
</section>

<section class="section about-section home-about">
    <div class="container">
        <h2 class="section-title">О нас</h2>

        <div class="about-content">
            <div class="about-image">
                <img src="<?= SITE_URL ?>/img/like_person.png" alt="О нас" class="about-image-photo">
            </div>

            <div class="about-text">
                <h3>Любимые персонажи — в вашем доме</h3>
                <p>Погрузитесь в волшебный мир Stitch и его друзей с нашей подборкой мягких игрушек и коллекционных фигур. Каждый элемент создан с любовью к деталям: от мягкой ткани до точной проработки мимики. Идеально для детей, коллекционеров и всех, кто ценит ностальгию и качество. Добавьте немного волшебства в свой интерьер — выберите свою любимую фигурку уже сегодня.</p>
            </div>
        </div>
    </div>
</section>

<section class="section home-reviews">
    <div class="container">
        <h2 class="section-title">Отзывы</h2>

        <div class="home-reviews-grid">
            <?php if (count($homeReviews) > 0): ?>
                <?php foreach ($homeReviews as $review): ?>
                    <article class="home-review-card">
                        <div class="home-review-head">
                            <span class="home-review-avatar"></span>
                            <h3><?= htmlspecialchars($review['user_name'] ?: 'Гость') ?></h3>
                        </div>
                        <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <article class="home-review-card">
                    <div class="home-review-head"><span class="home-review-avatar"></span><h3>Анна</h3></div>
                    <p>Заказывала в этом магазине коллекционные фигурки. Всё пришло аккуратно упаковано, без повреждений.</p>
                </article>
                <article class="home-review-card">
                    <div class="home-review-head"><span class="home-review-avatar"></span><h3>Олег</h3></div>
                    <p>Хороший выбор коллекционных игрушек, цены разумные.</p>
                </article>
                <article class="home-review-card">
                    <div class="home-review-head"><span class="home-review-avatar"></span><h3>Юля</h3></div>
                    <p>Очень довольна покупкой: редкая фигурка, аккуратно упаковано, без дефектов. Рекомендую.</p>
                </article>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>