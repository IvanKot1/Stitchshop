<?php
// Подключение к конфигурации
require_once __DIR__ . '/config.php';

// Запуск сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Проверка авторизации пользователя
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Проверка администратора
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Получение данных текущего пользователя
function getCurrentUser() {
    global $conn;
    if (!isLoggedIn()) return null;
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Получение всех товаров
function getAllProducts($filters = []) {
    global $conn;
    
    $sql = "SELECT * FROM products WHERE 1=1";
    $params = [];
    
    if (!empty($filters['category'])) {
        $sql .= " AND category_id = ?";
        $params[] = $filters['category'];
    }
    
    if (!empty($filters['min_price'])) {
        $sql .= " AND price >= ?";
        $params[] = $filters['min_price'];
    }
    
    if (!empty($filters['max_price'])) {
        $sql .= " AND price <= ?";
        $params[] = $filters['max_price'];
    }
    
    $sql .= " ORDER BY created_at ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Получение одного товара по ID
function getProductById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Получение всех категорий
function getAllCategories() {
    global $conn;
    $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

// Добавление товара в корзину
function addToCart($productId, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

// Удаление товара из корзины
function removeFromCart($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
}

// Обновление количества товара в корзине
function updateCartQuantity($productId, $quantity) {
    if ($quantity <= 0) {
        removeFromCart($productId);
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

// Получение содержимого корзины
function getCartContents() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return [];
    }
    
    global $conn;
    $productIds = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
    
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll();
    
    foreach ($products as &$product) {
        $product['quantity'] = $_SESSION['cart'][$product['id']];
    }
    
    return $products;
}

// Получение общего количества товаров в корзине
function getCartTotalItems() {
    if (!isset($_SESSION['cart'])) return 0;
    return array_sum($_SESSION['cart']);
}

// Получение общей суммы корзины
function getCartTotal() {
    $items = getCartContents();
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Очистка корзины
function clearCart() {
    $_SESSION['cart'] = [];
}

// Добавление отзыва
function addReview($productId, $userId, $rating, $comment) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
    return $stmt->execute([$productId, $userId, $rating, $comment]);
}

// Получение отзывов для товара
function getProductReviews($productId) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT r.*, u.name as user_name 
        FROM reviews r 
        LEFT JOIN users u ON r.user_id = u.id 
        WHERE r.product_id = ? 
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

// Создание заказа
function createOrder($userId, $address, $fullName, $email) {
    global $conn;
    
    $total = getCartTotal();
    $items = getCartContents();
    
    // Создаем заказ
    $stmt = $conn->prepare("INSERT INTO orders (user_id, full_name, email, address, total_amount, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->execute([$userId, $fullName, $email, $address, $total]);
    $orderId = $conn->lastInsertId();
    
    // Добавляем товары заказа
    foreach ($items as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
    }
    
    clearCart();
    return $orderId;
}

// Получение заказов пользователя
function getUserOrders($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Получение товаров заказа
function getOrderItems($orderId) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT oi.*, p.name as product_name, p.image as product_image
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll();
}

// Выход из системы
function logout() {
    session_destroy();
    session_start();
}
?>
