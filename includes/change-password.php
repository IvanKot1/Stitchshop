<?php
require_once __DIR__ . '/functions.php';

// Проверка авторизации
if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$user = getCurrentUser();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $newPasswordConfirm = $_POST['new_password_confirm'] ?? '';
    
    // Проверка текущего пароля
    if (!password_verify($currentPassword, $user['password'])) {
        $errors[] = 'Неверный текущий пароль';
    }
    
    // Проверка нового пароля
    if (strlen($newPassword) < 6) {
        $errors[] = 'Новый пароль должен быть не менее 6 символов';
    }
    
    if ($newPassword !== $newPasswordConfirm) {
        $errors[] = 'Новые пароли не совпадают';
    }
    
    if (empty($errors)) {
        // Обновляем пароль
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $user['id']]);
        
        $success = 'Пароль успешно изменён';
    }
}

// Перенаправление обратно в профиль
header('Location: ' . SITE_URL . '/profile.php?tab=settings' . (!empty($errors) ? '#error' : ''));
exit;
?>
