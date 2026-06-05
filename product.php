<?php
$pageTitle = 'Товар';
require_once __DIR__ . '/includes/functions.php';

// Получаем ID товара
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    header('Location: ' . SITE_URL . '/catalog.php');
    exit;
}

// Получаем товар
$product = getProductById($productId);

if (!$product) {
    header('Location: ' . SITE_URL . '/catalog.php');
    exit;
}

$pageTitle = htmlspecialchars($product['name']);

// Получаем отзывы
$reviews = getProductReviews($productId);

// Обработка добавления отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review']) && isLoggedIn()) {
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    
    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
        addReview($productId, $_SESSION['user_id'], $rating, $comment);
        header('Location: ' . SITE_URL . '/product.php?id=' . $productId);
        exit;
    }
}

// Получаем категорию товара
$stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
$stmt->execute([$product['category_id']]);
$category = $stmt->fetch();

// Обработка добавления в корзину
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (isLoggedIn()) {
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        addToCart($productId, $quantity);
        $successMessage = 'Товар добавлен в корзину!';
    } else {
        $errorMessage = 'Для добавления в корзину необходимо войти в систему';
    }
}
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<section class="section product-page">
    <div class="container">
        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>
        
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>
        
        <!-- Breadcrumbs -->
        <div class="product-breadcrumbs">
            <a href="<?= SITE_URL ?>/index.php">главная</a> / 
            <a href="<?= SITE_URL ?>/catalog.php">игрушки</a> / 
            <span><?= htmlspecialchars($product['name']) ?></span>
        </div>
        
        <div class="product-detail-v2">
            <div class="product-gallery">
                <div class="product-main-image">
                    <img src="<?= SITE_URL ?>/uploads/products/<?= htmlspecialchars($product['image']) ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>" 
                         onerror="this.parentElement.innerHTML='Фото товара'">
                </div>
                <div class="product-thumbnails">
                    <div class="thumbnail active">
                        <img src="<?= SITE_URL ?>/uploads/products/<?= htmlspecialchars($product['image']) ?>" alt="">
                    </div>
                    <div class="thumbnail">
                        <img src="<?= SITE_URL ?>/uploads/products/<?= htmlspecialchars($product['image']) ?>" alt="">
                    </div>
                </div>
            </div>
            
            <div class="product-info-v2">
                <h1><?= htmlspecialchars($product['name']) ?></h1>
                
                <div class="product-specs-v2">
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>
                
                <form method="POST" class="product-add-form">
                    <button type="submit" name="add_to_cart" class="product-btn-v2">
                        В корзину
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Отзывы -->
        <div class="reviews-section-v2">
            <h2 class="reviews-title">Отзывы (<?= count($reviews) ?>)</h2>
            
            <?php if (isLoggedIn()): ?>
                <div class="review-form-v2">
                    <h3>Оставить отзыв</h3>
                    <form method="POST">
                        <div class="rating-group">
                            <label>Оценка</label>
                            <select name="rating" class="rating-select">
                                <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                <option value="4">⭐⭐⭐⭐ (4)</option>
                                <option value="3">⭐⭐⭐ (3)</option>
                                <option value="2">⭐⭐ (2)</option>
                                <option value="1">⭐ (1)</option>
                            </select>
                        </div>
                        
                        <div class="comment-group">
                            <textarea name="comment" rows="3" 
                                      placeholder="" 
                                      required></textarea>
                        </div>
                        
                        <button type="submit" name="add_review" class="review-submit-btn">Отправить отзыв</button>
                    </form>
                </div>
            <?php else: ?>
                <p style="margin-bottom: 20px; color: #dcecff;">
                    <a href="<?= SITE_URL ?>/login.php">Войдите</a> или <a href="<?= SITE_URL ?>/register.php">зарегистрируйтесь</a>, чтобы оставить отзыв.
                </p>
            <?php endif; ?>
            
            <?php if (count($reviews) > 0): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-v2">
                        <div class="review-header-v2">
                            <span class="review-author-v2"><?= htmlspecialchars($review['user_name']) ?></span>
                            <span class="review-rating-v2">
                                <?= str_repeat('⭐', $review['rating']) ?>
                            </span>
                        </div>
                        <div class="review-date-v2"><?= date('d.m.Y', strtotime($review['created_at'])) ?></div>
                        <div class="review-comment-v2"><?= nl2br(htmlspecialchars($review['comment'])) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #dcecff;">Отзывов пока нет. Будьте первым!</p>
            <?php endif; ?>
        </div>
        
        <!-- Похожие товары -->
        <?php
        $similarProducts = getAllProducts(['category' => $product['category_id']]);
        $similarProducts = array_filter($similarProducts, function($p) use ($product) {
            return $p['id'] != $product['id'];
        });
        $similarProducts = array_slice($similarProducts, 0, 3);
        ?>
        
        <?php if (count($similarProducts) > 0): ?>
        <div class="similar-products catalog-page">
            <h2 class="section-title">Похожие товары</h2>
            <div class="products-grid">
                <?php foreach ($similarProducts as $similar): ?>
                    <a href="<?= SITE_URL ?>/product.php?id=<?= $similar['id'] ?>" class="product-card product-card-link">
                        <div class="product-image">
                            <img src="<?= SITE_URL ?>/uploads/products/<?= htmlspecialchars($similar['image']) ?>"
                                 alt="<?= htmlspecialchars($similar['name']) ?>"
                                 onerror="this.parentElement.innerHTML='Фото товара'">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($similar['name']) ?></h3>
                            <p class="product-price"><?= number_format($similar['price'], 0, ',', ' ') ?> ₽</p>
                            <div class="product-btn">В корзину</div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
