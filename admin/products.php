<?php
$pageTitle = 'Управление товарами';
require_once __DIR__ . '/../includes/functions.php';

// Проверка администратора
if (!isAdmin()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

// Обработка действий
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_product'])) {
        $productId = (int)$_POST['product_id'];
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $success = 'Товар удалён';
    }
    
    if (isset($_POST['add_product']) || isset($_POST['edit_product'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $specifications = $_POST['specifications'] ?? '';
        
        $errors = [];
        
        if (empty($name)) $errors[] = 'Введите название';
        if ($price <= 0) $errors[] = 'Введите корректную цену';
        
        // Обработка изображения
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (in_array($_FILES['image']['type'], $allowedTypes)) {
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $imageName = uniqid() . '.' . $extension;
                move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $imageName);
            }
        }
        
        if (empty($errors)) {
            if (isset($_POST['add_product'])) {
                // Добавление товара
                $stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, image, specifications, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$name, $description, $price, $categoryId, $imageName, $specifications]);
                $success = 'Товар добавлен';
            } else {
                // Редактирование товара
                $productId = (int)$_POST['product_id'];
                $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, image = COALESCE(NULLIF(?, ''), image), specifications = ? WHERE id = ?");
                $stmt->execute([$name, $description, $price, $categoryId, $imageName, $specifications, $productId]);
                $success = 'Товар обновлён';
            }
        } else {
            $error = implode(', ', $errors);
        }
    }
}

// Получаем все категории
$categories = getAllCategories();

// Получаем все товары
$products = getAllProducts([]);

// Получаем товар для редактирования
$editProduct = null;
if (isset($_GET['edit'])) {
    $editProduct = getProductById((int)$_GET['edit']);
}
?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="breadcrumbs">
    <div class="container">
        <ul>
            <li><a href="<?= SITE_URL ?>/index.php">Главная</a></li>
            <li><a href="<?= SITE_URL ?>/admin/index.php">Админ-панель</a></li>
            <li>Товары</li>
        </ul>
    </div>
</div>

<section class="section">
    <div class="container">
        <h2 class="section-title">📦 Управление товарами</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <!-- Форма добавления/редактирования -->
        <div class="cart-section" style="margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px;"><?= $editProduct ? 'Редактировать товар' : 'Добавить новый товар' ?></h3>
            
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editProduct): ?>
                    <input type="hidden" name="product_id" value="<?= $editProduct['id'] ?>">
                <?php endif; ?>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="name">Название *</label>
                        <input type="text" name="name" id="name" 
                               value="<?= $editProduct ? htmlspecialchars($editProduct['name']) : '' ?>" 
                               required style="width: 100%;">
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Цена (₽) *</label>
                        <input type="number" name="price" id="price" step="0.01" 
                               value="<?= $editProduct ? $editProduct['price'] : '' ?>" 
                               required style="width: 100%;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Описание</label>
                    <textarea name="description" id="description" rows="4" style="width: 100%;"><?= $editProduct ? htmlspecialchars($editProduct['description']) : '' ?></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="category_id">Категория</label>
                        <select name="category_id" id="category_id" style="width: 100%;">
                            <option value="">Выберите категорию</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $editProduct && $editProduct['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Изображение</label>
                        <input type="file" name="image" id="image" accept="image/*" style="width: 100%;">
                        <?php if ($editProduct && $editProduct['image']): ?>
                            <p style="font-size: 12px; color: #666; margin-top: 5px;">
                                Текущее: <?= htmlspecialchars($editProduct['image']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="specifications">Характеристики (JSON)</label>
                    <textarea name="specifications" id="specifications" rows="3" 
                              placeholder='{"height": "30 см", "material": "Плюш"}' 
                              style="width: 100%;"><?= $editProduct ? htmlspecialchars($editProduct['specifications']) : '' ?></textarea>
                </div>
                
                <div style="margin-top: 20px;">
                    <button type="submit" name="<?= $editProduct ? 'edit_product' : 'add_product' ?>" class="filter-btn">
                        <?= $editProduct ? 'Сохранить изменения' : 'Добавить товар' ?>
                    </button>
                    <?php if ($editProduct): ?>
                        <a href="<?= SITE_URL ?>/admin/products.php" class="filter-btn filter-btn-reset">Отмена</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Список товаров -->
        <div class="cart-section">
            <h3 style="margin-bottom: 20px;">Все товары (<?= count($products) ?>)</h3>
            
            <?php if (count($products) > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f0f0f0;">
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">ID</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Изображение</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Название</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Цена</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Категория</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #ddd;">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 15px;"><?= $product['id'] ?></td>
                                    <td style="padding: 15px;">
                                        <?php if ($product['image']): ?>
                                            <img src="<?= SITE_URL ?>/uploads/products/<?= htmlspecialchars($product['image']) ?>" 
                                                 alt="Товар" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                        <?php else: ?>
                                            <span style="color: #999;">Нет фото</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 15px;"><?= htmlspecialchars($product['name']) ?></td>
                                    <td style="padding: 15px; color: #667eea; font-weight: bold;">
                                        <?= number_format($product['price'], 2, ',', ' ') ?> ₽
                                    </td>
                                    <td style="padding: 15px;">
                                        <?php
                                        if ($product['category_id']) {
                                            $stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
                                            $stmt->execute([$product['category_id']]);
                                            $cat = $stmt->fetch();
                                            echo $cat ? htmlspecialchars($cat['name']) : '-';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td style="padding: 15px;">
                                        <a href="?edit=<?= $product['id'] ?>" class="filter-btn" style="padding: 8px 15px; font-size: 14px;">
                                            ✏️
                                        </a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Удалить товар?');">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <button type="submit" name="delete_product" class="filter-btn filter-btn-reset" style="padding: 8px 15px; font-size: 14px;">
                                                🗑️
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 40px;">Товаров пока нет</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
