<?php
require_once __DIR__ . '/functions.php';

if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$user = getCurrentUser();

if ($orderId > 0) {
    // Проверяем, что заказ принадлежит пользователю
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, $user['id']]);
    $order = $stmt->fetch();
    
    if ($order && $order['status'] == 'pending') {
        // Отменяем заказ
        $stmt = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$orderId]);
        
        // Возвращаем товары в корзину
        $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll();
        
        foreach ($items as $item) {
            addToCart($item['product_id'], $item['quantity']);
        }
    }
}

header('Location: ' . SITE_URL . '/profile.php?tab=orders');
exit;
