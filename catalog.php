<?php
$pageTitle = 'Каталог';
require_once __DIR__ . '/includes/functions.php';

$filters = [
    'category' => isset($_GET['category']) ? $_GET['category'] : '',
    'min_price' => isset($_GET['min_price']) ? $_GET['min_price'] : '',
    'max_price' => isset($_GET['max_price']) ? $_GET['max_price'] : '',
    'search' => isset($_GET['search']) ? $_GET['search'] : ''
];

$products = getAllProducts($filters);

if (!empty($filters['search'])) {
    $searchTerm = strtolower($filters['search']);
    $products = array_filter($products, function($product) use ($searchTerm) {
        return strpos(strtolower($product['name']), $searchTerm) !== false ||
               strpos(strtolower($product['description']), $searchTerm) !== false;
    });
    $products = array_values($products);
}

$categories = getAllCategories();
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<section class="section catalog-page">
    <div class="container">
        <div class="catalog-filters-row">
            <button class="catalog-filter-btn" onclick="toggleFilters()">Фильтр</button>
            <button class="catalog-filter-btn" onclick="toggleCategories()">Категории</button>
        </div>

        <div class="catalog-filters-panel" id="filtersPanel" style="display: none;">
            <div class="filters-section" style="margin-bottom: 20px;">
                <form method="GET" action="<?= SITE_URL ?>/catalog.php" class="catalog-filter-form" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
                    <div class="filter-group" style="flex: 1; min-width: 200px;">
                        <label for="category">Категория</label>
                        <select name="category" id="category">
                            <option value="">Все категории</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $filters['category'] == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group" style="flex: 1; min-width: 150px;">
                        <label for="min_price">Цена от</label>
                        <input type="number" name="min_price" id="min_price" value="<?= htmlspecialchars($filters['min_price']) ?>" placeholder="От">
                    </div>

                    <div class="filter-group" style="flex: 1; min-width: 150px;">
                        <label for="max_price">Цена до</label>
                        <input type="number" name="max_price" id="max_price" value="<?= htmlspecialchars($filters['max_price']) ?>" placeholder="До">
                    </div>

                    <div class="catalog-filter-actions" style="display: flex; gap: 10px;">
                        <button type="submit" class="filter-btn">Применить</button>
                        <a href="<?= SITE_URL ?>/catalog.php" class="filter-btn filter-btn-reset">Сбросить</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="catalog-content">
                <?php if (count($products) > 0): ?>
                    <p class="catalog-count">Найдено товаров: <strong><?= count($products) ?></strong></p>

                    <div class="products-grid">
                        <?php foreach ($products as $product): ?>
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
                    <div class="catalog-empty">
                        <p>Товары не найдены</p>
                        <a href="<?= SITE_URL ?>/catalog.php" class="filter-btn">Сбросить фильтры</a>
                    </div>
                <?php endif; ?>
            </div>
    </div>
</section>

<script>
function toggleFilters() {
    const panel = document.getElementById('filtersPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

function toggleCategories() {
    const panel = document.getElementById('filtersPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>