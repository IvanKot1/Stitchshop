<?php
require_once __DIR__ . '/functions.php';

if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $newPasswordConfirm = isset($_POST['new_password_confirm']) ? $_POST['new_password_confirm'] : '';
    
    if (empty($name) || empty($email)) {
        $_SESSION['error'] = 'Имя и почта обязательны для заполнения';
        header('Location: ' . SITE_URL . '/profile.php?tab=settings');
        exit;
    }
    
    $currentUser = getCurrentUser();
    
    // Проверка email на уникальность (если он изменился)
    if ($email !== $currentUser['email']) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $currentUser['id']]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Этот email уже занят';
            header('Location: ' . SITE_URL . '/profile.php?tab=settings');
            exit;
        }
    }
    
    // Проверка и обновление пароля
    $passwordData = [];
    if (!empty($currentPassword) || !empty($newPassword)) {
        if (empty($currentPassword) || empty($newPassword) || empty($newPasswordConfirm)) {
            $_SESSION['error'] = 'Для смены пароля заполните все поля пароля';
            header('Location: ' . SITE_URL . '/profile.php?tab=settings');
            exit;
        }
        
        if ($newPassword !== $newPasswordConfirm) {
            $_SESSION['error'] = 'Новые пароли не совпадают';
            header('Location: ' . SITE_URL . '/profile.php?tab=settings');
            exit;
        }
        
        // Проверка текущего пароля
        if (!password_verify($currentPassword, $currentUser['password'])) {
            $_SESSION['error'] = 'Неверный текущий пароль';
            header('Location: ' . SITE_URL . '/profile.php?tab=settings');
            exit;
        }
        
        $passwordData = ['password' => password_hash($newPassword, PASSWORD_DEFAULT)];
    }
    
    // Обновление данных
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?" . 
                           (isset($passwordData['password']) ? ", password = ?" : "") . 
                           " WHERE id = ?");
    
    if (isset($passwordData['password'])) {
        $stmt->execute([$name, $email, $passwordData['password'], $currentUser['id']]);
    } else {
        $stmt->execute([$name, $email, $currentUser['id']]);
    }
    
    header('Location: ' . SITE_URL . '/profile.php?tab=settings&updated=1');
    exit;
}

header('Location: ' . SITE_URL . '/profile.php?tab=settings');
exit;
?>
